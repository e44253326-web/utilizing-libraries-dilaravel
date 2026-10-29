<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\User;
use App\Services\EscPosReceiptPrinter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class KasirTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_kasir_page_displays_the_store_and_transaction_number(): void
    {
        $this->login();
        $response = $this->get(route('kasir.index'));

        $response->assertOk()
            ->assertSee('TOKO DIAN NAME');
    }

    public function test_product_search_returns_products_from_the_database(): void
    {
        Product::create([
            'name' => 'Susu UHT',
            'price' => 12500,
            'stock' => 8,
        ]);

        $this->login();
        $response = $this->getJson(route('kasir.search', ['q' => 'Susu']));

        $response->assertOk()
            ->assertJsonPath('products.0.name', 'Susu UHT')
            ->assertJsonPath('products.0.price', 12500)
            ->assertJsonPath('products.0.stock', 8);
    }

    public function test_cashier_catalog_includes_products_created_and_updated_by_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->post(route('products.store'), [
                'name' => 'Produk Sinkron',
                'price' => 10000,
                'stock' => 8,
            ])
            ->assertRedirect(route('products.index'));

        $product = Product::query()->where('name', 'Produk Sinkron')->firstOrFail();

        $this->actingAs(User::factory()->create(['role' => 'cashier']))
            ->getJson(route('kasir.search'))
            ->assertOk()
            ->assertJsonPath('products.0.name', 'Produk Sinkron')
            ->assertJsonPath('products.0.price', 10000)
            ->assertJsonPath('products.0.stock', 8);

        $this->actingAs($admin)
            ->put(route('products.update', $product), [
                'name' => 'Produk Sinkron Baru',
                'price' => 15000,
                'stock' => 5,
            ])
            ->assertRedirect(route('products.index'));

        $this->actingAs(User::factory()->create(['role' => 'cashier']))
            ->getJson(route('kasir.search', ['q' => 'Produk Sinkron Baru']))
            ->assertOk()
            ->assertJsonPath('products.0.name', 'Produk Sinkron Baru')
            ->assertJsonPath('products.0.price', 15000)
            ->assertJsonPath('products.0.stock', 5);
    }

    public function test_successful_payment_recalculates_totals_and_decrements_stock(): void
    {
        $product = Product::create([
            'name' => 'Kopi Bubuk',
            'price' => 10000,
            'stock' => 5,
        ]);

        $this->login();
        $response = $this->postJson(route('kasir.store'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ],
            'discount_percent' => 10,
            'discount_amount' => 500,
            'tax' => 1000,
            'other_fee' => 500,
            'paid_amount' => 20000,
            'payment_method' => 'QRIS',
            'customer_name' => 'Pelanggan Uji',
            'customer_phone' => '081234567890',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Transaksi berhasil disimpan.');

        $transaction = Transaction::query()->with('details')->firstOrFail();
        $response->assertJsonPath('thermal_print_url', route('kasir.receipt.print', $transaction));
        $this->assertSame('paid', $transaction->status);
        $this->assertSame('QRIS', $transaction->payment_method);
        $this->assertSame(20000.0, (float) $transaction->subtotal);
        $this->assertSame(2500.0, (float) $transaction->discount);
        $this->assertSame(19000.0, (float) $transaction->grand_total);
        $this->assertSame(1000.0, (float) $transaction->change_amount);
        $this->assertSame(1, $transaction->details->count());
        $this->assertSame(2, $transaction->details->first()->qty);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);

        $this->get(route('kasir.receipt', $transaction))
            ->assertOk()
            ->assertSee($transaction->transaction_number)
            ->assertSee('Pelanggan Uji')
            ->assertSee('QRIS')
            ->assertSee('Cetak ulang via thermal')
            ->assertSee('Cetak via browser')
            ->assertSee(route('transactions.invoice', $transaction));
    }

    public function test_cashier_can_download_a_pdf_invoice_for_their_paid_transaction(): void
    {
        $cashier = $this->login();
        $transaction = $this->createPaidTransaction($cashier);
        TransactionDetail::factory()->create([
            'transaction_id' => $transaction->id,
            'product_name' => 'Beras 5kg',
            'price' => 1000,
            'qty' => 1,
            'subtotal' => 1000,
        ]);

        $this->get(route('transactions.invoice', $transaction))
            ->assertDownload('invoice_'.$transaction->id.'.pdf');

        $this->get(route('pdf.cetak'))
            ->assertDownload('invoice_'.$transaction->id.'.pdf');
    }

    public function test_non_cashier_cannot_download_a_pdf_from_the_pdf_cetak_route(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $transaction = $this->createPaidTransaction($customer);

        $this->actingAs($customer)
            ->get(route('pdf.cetak', $transaction))
            ->assertNotFound();
    }

    public function test_insufficient_payment_does_not_save_transaction_or_decrement_stock(): void
    {
        $product = Product::create([
            'name' => 'Air Mineral',
            'price' => 10000,
            'stock' => 4,
        ]);

        $this->login();
        $response = $this->postJson(route('kasir.store'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ],
            'paid_amount' => 19999,
            'payment_method' => 'Tunai',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['paid_amount']);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 4,
        ]);
    }

    public function test_cashier_can_print_their_paid_receipt(): void
    {
        $cashier = $this->login();
        $transaction = $this->createPaidTransaction($cashier);

        $this->mock(EscPosReceiptPrinter::class, function (MockInterface $mock): void {
            $mock->shouldReceive('print')
                ->once()
                ->with(\Mockery::type(Transaction::class));
        });

        $this->postJson(route('kasir.receipt.print', $transaction))
            ->assertOk()
            ->assertJsonPath('message', 'Struk berhasil dikirim ke printer thermal.');
    }

    public function test_failed_thermal_print_keeps_the_paid_transaction_saved(): void
    {
        $cashier = $this->login();
        $transaction = $this->createPaidTransaction($cashier);

        $this->mock(EscPosReceiptPrinter::class, function (MockInterface $mock): void {
            $mock->shouldReceive('print')
                ->once()
                ->andThrow(new \RuntimeException('Printer is offline.'));
        });

        $this->postJson(route('kasir.receipt.print', $transaction))
            ->assertServiceUnavailable()
            ->assertJsonPath('message', 'Struk tidak dapat dicetak. Periksa printer dan nama printer di konfigurasi.');

        $this->assertModelExists($transaction);
        $this->assertSame('paid', $transaction->fresh()->status);
    }

    public function test_cashier_cannot_print_another_users_receipt(): void
    {
        $owner = User::factory()->create(['role' => 'cashier']);
        $transaction = $this->createPaidTransaction($owner);

        $this->login();

        $this->postJson(route('kasir.receipt.print', $transaction))
            ->assertNotFound();
    }

    public function test_held_transaction_is_saved_and_does_not_reserve_stock(): void
    {
        $product = Product::create([
            'name' => 'Mi Instan',
            'price' => 3500,
            'stock' => 10,
        ]);

        $this->login();
        $response = $this->postJson(route('kasir.hold'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 3],
            ],
            'paid_amount' => 1000,
            'payment_method' => 'Tunai',
            'customer_name' => 'Umum',
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Transaksi berhasil ditahan.');

        $transaction = Transaction::query()->firstOrFail();
        $this->assertSame('held', $transaction->status);
        $this->assertSame(10500.0, (float) $transaction->grand_total);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 10,
        ]);

        $this->get(route('kasir.ditahan'))
            ->assertOk()
            ->assertSee($transaction->transaction_number);

        $this->get(route('kasir.index', ['held' => $transaction->id]))
            ->assertOk()
            ->assertSee($transaction->transaction_number)
            ->assertSee('Mi Instan');
    }

    public function test_resuming_a_held_transaction_completes_the_same_record_and_decrements_stock(): void
    {
        $this->login();
        $product = Product::create([
            'name' => 'Teh Kotak',
            'price' => 7000,
            'stock' => 6,
        ]);

        $this->postJson(route('kasir.hold'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 3],
            ],
            'paid_amount' => 0,
            'payment_method' => 'Tunai',
            'customer_name' => 'Umum',
        ])->assertOk();
        $heldTransaction = Transaction::query()->firstOrFail();

        $this->postJson(route('kasir.store'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ],
            'paid_amount' => 15000,
            'payment_method' => 'Debit',
            'customer_name' => 'Umum',
            'held_transaction_id' => $heldTransaction->id,
        ])->assertOk();

        $heldTransaction->refresh()->load('details');
        $this->assertSame(1, Transaction::query()->count());
        $this->assertSame('paid', $heldTransaction->status);
        $this->assertSame('Debit', $heldTransaction->payment_method);
        $this->assertSame(2, $heldTransaction->details->first()->qty);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 4,
        ]);
    }

    public function test_checkout_rejects_quantities_above_available_stock(): void
    {
        $product = Product::create([
            'name' => 'Gula Pasir',
            'price' => 16000,
            'stock' => 2,
        ]);

        $this->login();
        $response = $this->postJson(route('kasir.store'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 3],
            ],
            'paid_amount' => 50000,
            'payment_method' => 'Debit',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['items']);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 2,
        ]);
    }

    public function test_checkout_rejects_products_with_negative_prices(): void
    {
        $product = Product::create([
            'name' => 'Produk Harga Tidak Valid',
            'price' => -1000,
            'stock' => 3,
        ]);

        $this->login();
        $response = $this->postJson(route('kasir.store'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 1],
            ],
            'paid_amount' => 0,
            'payment_method' => 'Tunai',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['items']);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);
    }

    public function test_receipt_escapes_customer_supplied_html(): void
    {
        $product = Product::create([
            'name' => 'Biskuit',
            'price' => 5000,
            'stock' => 1,
        ]);
        $dangerousName = '<script>alert(1)</script>';

        $this->login();
        $this->postJson(route('kasir.store'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 1],
            ],
            'paid_amount' => 5000,
            'payment_method' => 'Tunai',
            'customer_name' => $dangerousName,
        ])->assertOk();

        $content = $this->get(route('kasir.receipt', Transaction::query()->firstOrFail()))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $content);
        $this->assertStringNotContainsString($dangerousName, $content);
    }

    public function test_unauthenticated_cashier_is_redirected_to_login(): void
    {
        $this->get(route('kasir.index'))
            ->assertRedirect(route('login'));
    }

    public function test_cashier_cannot_resume_another_users_held_transaction(): void
    {
        $owner = $this->login();
        $product = Product::create([
            'name' => 'Sereal',
            'price' => 9000,
            'stock' => 4,
        ]);

        $this->postJson(route('kasir.hold'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 1],
            ],
            'payment_method' => 'Tunai',
            'customer_name' => 'Umum',
        ])->assertOk();
        $transaction = Transaction::query()->firstOrFail();
        $this->assertSame($owner->id, $transaction->user_id);

        $this->actingAs(User::factory()->create(['role' => 'cashier']))
            ->get(route('kasir.index', ['held' => $transaction->id]))
            ->assertNotFound();
    }

    public function test_cashier_cannot_view_another_users_receipt(): void
    {
        $owner = $this->login();
        $product = Product::create([
            'name' => 'Sampo',
            'price' => 12000,
            'stock' => 2,
        ]);

        $this->postJson(route('kasir.store'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 1],
            ],
            'paid_amount' => 12000,
            'payment_method' => 'Tunai',
            'customer_name' => 'Umum',
        ])->assertOk();
        $transaction = Transaction::query()->firstOrFail();
        $this->assertSame($owner->id, $transaction->user_id);

        $this->actingAs(User::factory()->create(['role' => 'cashier']))
            ->get(route('kasir.receipt', $transaction))
            ->assertNotFound();

        $this->get(route('transactions.invoice', $transaction))
            ->assertNotFound();
    }

    public function test_cashier_cannot_download_an_invoice_for_a_held_transaction(): void
    {
        $cashier = $this->login();
        $transaction = Transaction::factory()->create([
            'user_id' => $cashier->id,
            'status' => 'held',
        ]);

        $this->get(route('transactions.invoice', $transaction))
            ->assertNotFound();
    }

    public function test_cashier_cannot_delete_another_users_held_transaction(): void
    {
        $owner = $this->login();
        $product = Product::create([
            'name' => 'Sereal Cokelat',
            'price' => 9000,
            'stock' => 4,
        ]);

        $this->postJson(route('kasir.hold'), [
            'items' => [
                ['product_id' => $product->id, 'qty' => 1],
            ],
            'payment_method' => 'Tunai',
            'customer_name' => 'Umum',
        ])->assertOk();
        $transaction = Transaction::query()->firstOrFail();
        $this->assertSame($owner->id, $transaction->user_id);

        $this->actingAs(User::factory()->create(['role' => 'cashier']))
            ->delete(route('kasir.held.destroy', $transaction))
            ->assertNotFound();

        $this->assertModelExists($transaction);
    }

    private function login(): User
    {
        $user = User::factory()->create(['role' => 'cashier']);
        $this->actingAs($user);

        return $user;
    }

    private function createPaidTransaction(User $cashier): Transaction
    {
        return Transaction::create([
            'transaction_number' => 'TRX-20261008-001',
            'user_id' => $cashier->id,
            'customer_name' => 'Umum',
            'subtotal' => 1000,
            'discount_percent' => 0,
            'discount_amount' => 0,
            'discount' => 0,
            'tax' => 0,
            'other_fee' => 0,
            'grand_total' => 1000,
            'paid_amount' => 1000,
            'change_amount' => 0,
            'payment_method' => 'Tunai',
            'status' => 'paid',
        ]);
    }
}
