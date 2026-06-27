<?php

namespace App\Actions;

use App\Ai\Agents\ResumeIntelligenceAgent;
use Illuminate\Http\UploadedFile;
use Laravel\Ai\Files\Document;

class ParseCv
{
    /**
     * @return array<string, mixed>
     */
    public function handle(?UploadedFile $file = null, ?string $text = null): array
    {
        $attachments = [];

        if ($file) {
            $attachments[] = Document::fromUpload($file);
            $prompt = 'Extract structured candidate information from the attached resume/CV document.';
        } else {
            $prompt = $text;
        }

        return ResumeIntelligenceAgent::make()
            ->prompt($prompt, $attachments)
            ->toArray();
    }
}
