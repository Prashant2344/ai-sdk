<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Services\OpenSearchService;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function __construct(
        private readonly OpenSearchService $openSearch,
    ) {}

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $products = Product::withoutEvents(function () {
            return Product::factory()->count(1000)->create();
        });

        $products->chunk(100)->each(
            fn ($chunk) => $this->openSearch->bulkIndexProducts($chunk),
        );
    }
}
