<?php

namespace App\Support;

use Laravel\Ai\Responses\AgentResponse;
use SulimanBenhalim\AiCost\Facades\AiCost;

class AiUsageReporter
{
    /**
     * @return array<string, mixed>
     */
    public function track(AgentResponse $response): array
    {
        $cost = AiCost::for($response);

        return [
            'provider' => $response->meta->provider ?? null,
            'model' => $response->meta->model ?? null,
            'prompt_tokens' => $response->usage->promptTokens,
            'completion_tokens' => $response->usage->completionTokens,
            'total_tokens' => $response->usage->promptTokens + $response->usage->completionTokens,
            'cost_usd' => $cost->isKnown() ? $cost->total() : null,
            'cost_formatted' => $cost->format(),
            'cost_known' => $cost->isKnown(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $usage
     * @return array{data: array<string, mixed>, usage: array<string, mixed>}
     */
    public function wrap(array $data, array $usage): array
    {
        return [
            'data' => $data,
            'usage' => $usage,
        ];
    }
}
