<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_product_form_and_add_a_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $this->get(route('products.create'))->assertOk();

        $this->post(route('products.store'), [
            'name' => 'Kopi Susu',
            'price' => 12500,
            'stock' => 18,
        ])->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Kopi Susu',
            'price' => 12500,
            'stock' => 18,
        ]);
    }

    public function test_admin_can_upload_replace_and_remove_a_product_image(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->post(route('products.store'), [
            'name' => 'Teh Melati',
            'price' => 7000,
            'stock' => 12,
            'image' => UploadedFile::fake()->image('teh.jpg'),
        ])->assertRedirect(route('products.index'));

        $product = Product::query()->where('name', 'Teh Melati')->firstOrFail();
        $oldImagePath = $product->image_path;
        $this->assertNotNull($oldImagePath);
        Storage::disk('public')->assertExists($oldImagePath);

        $this->put(route('products.update', $product), [
            'name' => 'Teh Melati',
            'price' => 7000,
            'stock' => 12,
            'image' => UploadedFile::fake()->image('teh-baru.png'),
        ])->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertNotSame($oldImagePath, $product->image_path);
        Storage::disk('public')->assertMissing($oldImagePath);
        Storage::disk('public')->assertExists($product->image_path);

        $currentImagePath = $product->image_path;
        $this->put(route('products.update', $product), [
            'name' => 'Teh Melati',
            'price' => 7000,
            'stock' => 12,
            'remove_image' => 1,
        ])->assertRedirect(route('products.index'));

        $this->assertNull($product->fresh()->image_path);
        Storage::disk('public')->assertMissing($currentImagePath);
    }

    public function test_product_image_must_be_an_allowed_image_under_two_megabytes(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->from(route('products.create'))
            ->post(route('products.store'), [
                'name' => 'File Tidak Valid',
                'price' => 1000,
                'stock' => 1,
                'image' => UploadedFile::fake()->create('dokumen.pdf', 20, 'application/pdf'),
            ])
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseMissing('products', ['name' => 'File Tidak Valid']);
    }

    public function test_cashier_and_customer_cannot_manage_products(): void
    {
        foreach (['cashier', 'customer'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]));

            $this->get(route('products.create'))->assertForbidden();
            $this->post(route('products.store'), [
                'name' => 'Produk Terlarang',
                'price' => 1000,
                'stock' => 1,
            ])->assertForbidden();
        }

        $this->assertDatabaseMissing('products', ['name' => 'Produk Terlarang']);
    }

    public function test_admin_cannot_add_product_with_invalid_price_or_stock(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $this->from(route('products.create'))
            ->post(route('products.store'), [
                'name' => 'Produk Tidak Valid',
                'price' => 0,
                'stock' => -1,
            ])
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors(['price', 'stock']);

        $this->assertSame(0, Product::query()->count());
    }
}
