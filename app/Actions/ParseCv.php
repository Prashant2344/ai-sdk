<?php

namespace App\Actions;

use App\Ai\Agents\ResumeIntelligenceAgent;
use App\Support\AiUsageReporter;
use Illuminate\Http\UploadedFile;
use Laravel\Ai\Files\Document;
use Throwable;

class ParseCv
{
    /**
     * @var list<string>
     */
    private const DOCUMENT_ATTACHMENT_UNSUPPORTED_PROVIDERS = [
        'ollama',
        'groq',
        'deepseek',
    ];

    public function __construct(
        private readonly ExtractCvText $extractCvText,
        private readonly AiUsageReporter $usageReporter,
    ) {}

    /**
     * @return array{data: array<string, mixed>, usage: array<string, mixed>}
     */
    public function handle(?UploadedFile $file = null, ?string $text = null): array
    {
        $timeout = (int) config('ai.agents.resume.timeout', 300);
        set_time_limit($timeout + 30);

        $attachments = [];

        if ($file) {
            if ($this->providerSupportsDocumentAttachments()) {
                $attachments[] = Document::fromUpload($file);
                $prompt = 'Extract structured candidate information from the attached resume/CV document.';
            } else {
                $prompt = $this->extractCvText->fromUpload($file);
            }
        } else {
            $prompt = $text;
        }

        $run = $this->usageReporter->startRun('parse-cv');

        try {
            $response = ResumeIntelligenceAgent::make()
                ->prompt($prompt, $attachments, timeout: $timeout);

            return $this->usageReporter->wrap(
                $response->toArray(),
                $this->usageReporter->track($response, $run),
            );
        } catch (Throwable $exception) {
            $this->usageReporter->failRun($run, $exception);

            throw $exception;
        }
    }

    private function providerSupportsDocumentAttachments(): bool
    {
        $provider = (string) config('ai.agents.resume.provider', config('ai.default'));

        return ! in_array($provider, self::DOCUMENT_ATTACHMENT_UNSUPPORTED_PROVIDERS, true);
    }
}
