<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\OpenSearchService;

class ProductObserver
{
    public function __construct(
        private readonly OpenSearchService $openSearch,
    ) {}

    public function saved(Product $product): void
    {
        $this->openSearch->indexProduct($product);
    }
}
