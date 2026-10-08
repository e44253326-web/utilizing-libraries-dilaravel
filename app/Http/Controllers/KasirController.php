<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class KasirController extends Controller
{
    public function index(Request $request): View
    {
        $heldTransaction = null;
        $heldId = $request->integer('held');

        if ($heldId > 0) {
            $transaction = $this->ownedHeldTransactions()
                ->with('details.product')
                ->findOrFail($heldId);
            $this->authorize('viewHeld', $transaction);

            $heldTransaction = [
                'id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number,
                'customer_name' => $transaction->customer_name,
                'customer_phone' => $transaction->customer_phone,
                'discount_percent' => (float) $transaction->discount_percent,
                'discount_amount' => (float) $transaction->discount_amount,
                'tax' => (float) $transaction->tax,
                'other_fee' => (float) $transaction->other_fee,
                'paid_amount' => (float) $transaction->paid_amount,
                'payment_method' => $transaction->payment_method,
                'items' => $transaction->details->map(fn ($detail): array => [
                    'id' => $detail->product?->id,
                    'name' => $detail->product?->name ?? $detail->product_name,
                    'price' => (float) ($detail->product?->price ?? $detail->price),
                    'stock' => (int) ($detail->product?->stock ?? 0),
                    'qty' => (int) $detail->qty,
                ])->values()->all(),
            ];
        }

        return view('kasir.index', [
            'cashierName' => Auth::user()?->name ?? 'Kasir',
            'transactionNumber' => $heldTransaction['transaction_number'] ?? $this->nextTransactionNumber(),
            'heldTransaction' => $heldTransaction,
            'heldCount' => $this->ownedHeldTransactions()->count(),
        ]);
    }

    public function searchProduct(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ], [
            'q.max' => 'Kata pencarian maksimal 100 karakter.',
        ]);

        $term = trim((string) ($validated['q'] ?? ''));
        $columns = ['name'];

        foreach (['code', 'barcode'] as $column) {
            if (Schema::hasColumn('products', $column)) {
                $columns[] = $column;
            }
        }

        $productsQuery = Product::query();

        if ($term !== '') {
            $productsQuery
                ->where(function (Builder $query) use ($columns, $term): void {
                    foreach ($columns as $column) {
                        $query->orWhere($column, 'like', '%'.$term.'%');
                    }
                })
                ->orderBy('name');
        } else {
            $productsQuery->latest();
        }

        $products = $productsQuery
            ->limit(20)
            ->get()
            ->map(fn (Product $product): array => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'stock' => (int) $product->stock,
                'image_url' => $product->image_path ? Storage::disk('public')->url($product->image_path) : null,
                'code' => $product->getAttribute('code'),
                'barcode' => $product->getAttribute('barcode'),
            ])
            ->values();

        return response()->json(['products' => $products]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $this->validatedCart($request, true);
        $transaction = $this->persistTransaction($validated, 'paid');

        return response()->json([
            'message' => 'Transaksi berhasil disimpan.',
            'transaction_id' => $transaction->id,
            'transaction_number' => $transaction->transaction_number,
            'receipt_url' => route('kasir.receipt', $transaction),
            'thermal_print_url' => route('kasir.receipt.print', $transaction),
        ]);
    }

    public function hold(Request $request): JsonResponse
    {
        $validated = $this->validatedCart($request, false);
        $transaction = $this->persistTransaction($validated, 'held');

        return response()->json([
            'message' => 'Transaksi berhasil ditahan.',
            'transaction_id' => $transaction->id,
            'transaction_number' => $transaction->transaction_number,
            'next_transaction_number' => $this->nextTransactionNumber(),
        ]);
    }

    public function held(): View
    {
        return view('kasir.ditahan', [
            'transactions' => $this->ownedHeldTransactions()
                ->with('details')
                ->latest()
                ->get(),
        ]);
    }

    public function destroyHeld(Transaction $transaction): RedirectResponse
    {
        $this->authorize('deleteHeld', $transaction);

        $transaction->delete();

        return to_route('kasir.ditahan')->with('success', 'Transaksi ditahan berhasil dihapus.');
    }

    public function receipt(Transaction $transaction): View
    {
        $this->authorize('viewReceipt', $transaction);

        return view('kasir.receipt', [
            'transaction' => $transaction->load(['details', 'user']),
            'cashierName' => $transaction->user?->name ?? 'Kasir',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedCart(Request $request, bool $paymentRequired): array
    {
        $paymentRules = $paymentRequired
            ? ['required', 'numeric', 'min:0']
            : ['nullable', 'numeric', 'min:0'];

        return $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'tax' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'other_fee' => ['nullable', 'numeric', 'min:0', 'max:9999999999999.99'],
            'paid_amount' => [...$paymentRules, 'max:9999999999999.99'],
            'payment_method' => ['required', 'in:Tunai,QRIS,Debit,Kredit,E-Wallet,Transfer'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'held_transaction_id' => ['nullable', 'integer', 'exists:transactions,id'],
        ], [
            'items.required' => 'Keranjang masih kosong.',
            'items.min' => 'Keranjang masih kosong.',
            'items.*.product_id.required' => 'Produk tidak valid.',
            'items.*.product_id.exists' => 'Produk tidak tersedia. Cari ulang produk tersebut.',
            'items.*.product_id.distinct' => 'Produk yang sama tidak boleh dikirim berulang.',
            'items.*.qty.min' => 'Jumlah produk minimal 1.',
            'discount_percent.max' => 'Diskon persen maksimal 100%.',
            'paid_amount.required' => 'Nominal uang dibayar harus diisi.',
            'paid_amount.min' => 'Nominal uang dibayar tidak boleh negatif.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function persistTransaction(array $validated, string $status): Transaction
    {
        return DB::transaction(function () use ($validated, $status): Transaction {
            $quantities = [];
            foreach ($validated['items'] as $item) {
                $productId = (int) $item['product_id'];
                $quantities[$productId] = (int) $item['qty'];
            }

            $products = Product::query()
                ->whereIn('id', array_keys($quantities))
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== count($quantities)) {
                throw ValidationException::withMessages([
                    'items' => 'Salah satu produk sudah tidak tersedia. Muat ulang keranjang.',
                ]);
            }

            $subtotal = 0.0;
            foreach ($quantities as $productId => $quantity) {
                /** @var Product $product */
                $product = $products->get($productId);
                $price = (float) $product->price;
                if (! is_finite($price) || $price < 0) {
                    throw ValidationException::withMessages([
                        'items' => "Harga produk {$product->name} tidak valid.",
                    ]);
                }

                if ($quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi. Stok tersedia {$product->stock}.",
                    ]);
                }
                $subtotal += $price * $quantity;
            }
            $subtotal = round($subtotal, 2);

            $discountPercent = round((float) ($validated['discount_percent'] ?? 0), 2);
            $discountAmount = round((float) ($validated['discount_amount'] ?? 0), 2);
            $discount = round(($subtotal * $discountPercent / 100) + $discountAmount, 2);
            $tax = round((float) ($validated['tax'] ?? 0), 2);
            $otherFee = round((float) ($validated['other_fee'] ?? 0), 2);
            $grandTotal = round(max(0, $subtotal - $discount + $tax + $otherFee), 2);
            $paidAmount = round((float) ($validated['paid_amount'] ?? 0), 2);

            if ($status === 'paid' && $paidAmount < $grandTotal) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Uang dibayar belum mencukupi total transaksi.',
                ]);
            }

            $transaction = null;
            if (! empty($validated['held_transaction_id'])) {
                $transaction = Transaction::query()
                    ->whereKey($validated['held_transaction_id'])
                    ->where('status', 'held')
                    ->lockForUpdate()
                    ->first();

                abort_if($transaction === null, 404);
                $this->authorize('resumeHeld', $transaction);
            }

            $transaction ??= new Transaction([
                'transaction_number' => $this->nextTransactionNumber(),
            ]);

            $transaction->fill([
                'user_id' => Auth::id(),
                'customer_name' => trim((string) ($validated['customer_name'] ?? '')) ?: 'Umum',
                'customer_phone' => trim((string) ($validated['customer_phone'] ?? '')) ?: null,
                'subtotal' => $subtotal,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'discount' => $discount,
                'tax' => $tax,
                'other_fee' => $otherFee,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'change_amount' => $status === 'paid' ? round($paidAmount - $grandTotal, 2) : 0,
                'payment_method' => $validated['payment_method'],
                'status' => $status,
            ]);
            $transaction->save();
            $transaction->details()->delete();

            foreach ($quantities as $productId => $quantity) {
                /** @var Product $product */
                $product = $products->get($productId);
                $lineSubtotal = round((float) $product->price * $quantity, 2);

                $transaction->details()->create([
                    'product_id' => $product->id,
                    'product_code' => $this->productCode($product),
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $quantity,
                    'discount' => 0,
                    'subtotal' => $lineSubtotal,
                ]);

                if ($status === 'paid') {
                    $product->decrement('stock', $quantity);
                }
            }

            return $transaction;
        });
    }

    private function nextTransactionNumber(): string
    {
        $prefix = 'TRX-'.now('Asia/Jakarta')->format('Ymd').'-';
        $latestNumber = Transaction::query()
            ->where('transaction_number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('transaction_number');
        $sequence = $latestNumber === null
            ? 1
            : ((int) substr($latestNumber, strlen($prefix))) + 1;

        return $prefix.str_pad((string) $sequence, 3, '0', STR_PAD_LEFT);
    }

    private function productCode(Product $product): ?string
    {
        foreach (['code', 'barcode'] as $column) {
            if (Schema::hasColumn('products', $column)) {
                $value = $product->getAttribute($column);
                if ($value !== null && $value !== '') {
                    return (string) $value;
                }
            }
        }

        return null;
    }

    private function ownedHeldTransactions(): Builder
    {
        return Transaction::query()
            ->where('status', 'held')
            ->where('user_id', Auth::id());
    }
}
