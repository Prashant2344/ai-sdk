<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Enumerable;
use OpenSearch\Client;
use OpenSearch\ClientBuilder;

class OpenSearchService
{
    private readonly Client $client;

    public function __construct()
    {
        $builder = ClientBuilder::create()
            ->setHosts([config('services.opensearch.host')]);

        $username = config('services.opensearch.username');
        $password = config('services.opensearch.password');

        if (filled($username) && filled($password)) {
            $builder->setBasicAuthentication($username, $password);
        }

        if (! config('services.opensearch.verify_ssl', false)) {
            $builder->setSSLVerification(false);
        }

        $this->client = $builder->build();
    }

    public function client(): Client
    {
        return $this->client;
    }

    /**
     * @return array<string, mixed>
     */
    public function indexProduct(Product $product): array
    {
        return $this->client->index([
            'index' => 'products',
            'id' => $product->id,
            'body' => [
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
            ],
        ]);
    }

    /**
     * @param  Enumerable<int, Product>|iterable<int, Product>  $products
     */
    public function bulkIndexProducts(Enumerable|iterable $products): void
    {
        $body = [];

        foreach ($products as $product) {
            $body[] = [
                'index' => [
                    '_index' => 'products',
                    '_id' => $product->id,
                ],
            ];
            $body[] = [
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
            ];
        }

        if ($body === []) {
            return;
        }

        $this->client->bulk(['body' => $body]);
    }

    /**
     * @return array{total: int, results: list<array<string, mixed>>}
     */
    public function searchProducts(string $query, int $size = 10, int $from = 0): array
    {
        $response = $this->client->search([
            'index' => 'products',
            'body' => [
                'from' => $from,
                'size' => $size,
                'query' => [
                    'multi_match' => [
                        'query' => $query,
                        'fields' => ['name', 'description'],
                        'fuzziness' => 'AUTO',
                    ],
                ],
            ],
        ]);

        $hits = $response['hits']['hits'] ?? [];

        return [
            'total' => (int) ($response['hits']['total']['value'] ?? 0),
            'results' => array_map(fn (array $hit) => [
                'id' => $hit['_id'],
                'score' => $hit['_score'],
                ...$hit['_source'],
            ], $hits),
        ];
    }
}
