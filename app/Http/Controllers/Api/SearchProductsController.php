<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OpenSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchProductsController extends Controller
{
    public function __invoke(Request $request, OpenSearchService $openSearch): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:1', 'max:255'],
            'size' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'from' => ['sometimes', 'integer', 'min:0'],
        ]);

        return response()->json(
            $openSearch->searchProducts(
                $validated['q'],
                $validated['size'] ?? 10,
                $validated['from'] ?? 0,
            ),
        );
    }
}
