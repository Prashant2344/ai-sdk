<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Budget Enforcement
    |--------------------------------------------------------------------------
    |
    | When enabled, agents tagged with the #[MaxCost] attribute are blocked
    | (via a BudgetExceededException) once they have spent their budget for the
    | current request or queued job. Set to false to disable enforcement; spend
    | is still recorded, so AiCost::for() and Budget::spent() keep working.
    |
    | Note: a budget can only guard models with known pricing.
    |
    */

    'budget' => [
        'enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Model Pricing
    |--------------------------------------------------------------------------
    |
    | Per-model token rates, in USD per 1,000,000 tokens, merged over the
    | package's built-in defaults. List only the models you want to add or
    | override. Models without pricing report an "unknown" cost rather than
    | throwing.
    |
    */

    'pricing' => [
        'models' => [
            'ollama' => [
                'llama3.1:8b' => ['input' => 0, 'output' => 0],
                'qwen2.5-coder' => ['input' => 0, 'output' => 0],
            ],
            'gemini' => [
                'gemini-3.5-flash' => ['input' => 0.15, 'output' => 0.60],
            ],
        ],
    ],

];
