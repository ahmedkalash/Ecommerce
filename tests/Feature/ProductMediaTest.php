<?php

namespace Tests\Feature;

use App\Models\CustomerProduct;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductMediaTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /**
     * Test that CustomerProduct has registered expected media collections.
     */
    public function test_customer_product_media_collections_registration()
    {
        $this->withoutExceptionHandling();
        Storage::fake('public');

        try {
            // Create a dummy CustomerProduct manually since we don't have a factory
            $user = \App\Models\User::factory()->create();
            $customerProduct = new CustomerProduct;
            $customerProduct->name = 'Test Customer Product';
            $customerProduct->slug = 'test-customer-product';
            $customerProduct->user_id = $user->id;
            $customerProduct->save();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Test Exception: '.$e->getMessage());
            throw $e;
        }

        // 1. Test Thumbnail Collection
        $thumbnail = UploadedFile::fake()->image('thumbnail.jpg');
        $customerProduct->addMedia($thumbnail)->toMediaCollection('thumbnail');

        $this->assertTrue($customerProduct->hasMedia('thumbnail'));
        $this->assertNotNull($customerProduct->thumbnailImg);
        $this->assertStringContainsString('thumbnail.jpg', $customerProduct->getFirstMediaUrl('thumbnail'));

        // 2. Test Gallery Collection
        $gallery1 = UploadedFile::fake()->image('gallery1.jpg');
        $gallery2 = UploadedFile::fake()->image('gallery2.jpg');
        $customerProduct->addMedia($gallery1)->toMediaCollection('gallery');
        $customerProduct->addMedia($gallery2)->toMediaCollection('gallery');

        $this->assertCount(2, $customerProduct->galleryMedia());
        $this->assertEquals(2, $customerProduct->getMedia('gallery')->count());

        // 3. Test Meta Image
        $metaImg = UploadedFile::fake()->image('meta.jpg');
        $customerProduct->addMedia($metaImg)->toMediaCollection('meta');

        $this->assertEquals($customerProduct->getFirstMediaUrl('meta'), $customerProduct->metaImg);

        // 4. Test PDF
        $pdf = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $customerProduct->addMedia($pdf)->toMediaCollection('pdf');

        $this->assertEquals($customerProduct->getFirstMediaUrl('pdf'), $customerProduct->pdfUrl);
    }

    /**
     * Test that ProductService::destroy correctly triggers model events to delete media.
     */
    public function test_product_service_destroy_deletes_media()
    {
        Storage::fake('public');

        // Create a product using the factory
        // We need to ensure dependencies created by factory exist or are mocked if needed
        // Assuming ProductFactory works out of the box with RefreshDatabase
        $product = Product::factory()->create();

        // Attach media to the product
        $image = UploadedFile::fake()->image('product-image.jpg');
        $product->addMedia($image)->toMediaCollection('gallery');

        $this->assertTrue($product->hasMedia('gallery'));
        $mediaItem = $product->getFirstMedia('gallery');
        $this->assertNotNull($mediaItem);

        // Call ProductService destroy
        $service = new ProductService;
        $service->destroy($product->id);

        // Assert Product is deleted
        $this->assertDatabaseMissing('products', ['id' => $product->id]);

        // Assert Media is deleted (Spatie deletes the record from media table)
        $this->assertDatabaseMissing('media', ['id' => $mediaItem->id]);
    }
}
