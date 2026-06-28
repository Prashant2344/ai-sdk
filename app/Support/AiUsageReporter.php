<?php

namespace App\Support;

use Laravel\Ai\Responses\AgentResponse;
use Laraveljutsu\Usaige\Models\AiRun;
use Laraveljutsu\Usaige\Models\AiUsage;
use SulimanBenhalim\AiCost\Cost;
use SulimanBenhalim\AiCost\Facades\AiCost;
use Throwable;

class AiUsageReporter
{
    /**
     * @return array<string, mixed>
     */
    public function track(AgentResponse $response, AiRun $run): array
    {
        $cost = AiCost::for($response);

        $usage = ai_usage(
            $run,
            promptTokens: $response->usage->promptTokens,
            completionTokens: $response->usage->completionTokens,
            costUsd: $cost->isKnown() ? $cost->total() : null,
        );

        return $this->format($run->fresh(), $usage, $response, $cost);
    }

    public function startRun(string $feature, ?string $provider = null, ?string $model = null): AiRun
    {
        return ai_run(
            $feature,
            provider: $provider ?? config('ai.agents.resume.provider', config('ai.default')),
            model: $model ?? config('ai.agents.resume.model'),
        );
    }

    public function failRun(AiRun $run, Throwable $exception): void
    {
        $run->fail($exception->getMessage());
    }

    /**
     * @return array<string, mixed>
     */
    public function format(AiRun $run, AiUsage $usage, AgentResponse $response, Cost $cost): array
    {
        return [
            'run_id' => $run->id,
            'feature' => $run->feature_key,
            'provider' => $response->meta->provider ?? $run->provider,
            'model' => $response->meta->model ?? $run->model,
            'prompt_tokens' => $usage->prompt_tokens,
            'completion_tokens' => $usage->completion_tokens,
            'total_tokens' => $usage->total_tokens,
            'cost_usd' => $usage->cost_usd !== null ? (float) $usage->cost_usd : null,
            'cost_formatted' => $cost->format(),
            'cost_known' => $cost->isKnown(),
            'duration_ms' => $run->durationMs(),
            'status' => $run->status->value,
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
