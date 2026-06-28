<?php

namespace App\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use InvalidArgumentException;
use Smalot\PdfParser\Parser;
use ZipArchive;

class ExtractCvText
{
    public function fromUpload(UploadedFile $file): string
    {
        $path = $file->getRealPath();

        if ($path === false) {
            throw new InvalidArgumentException('Could not read the uploaded file.');
        }

        $text = match (strtolower($file->getClientOriginalExtension())) {
            'txt' => file_get_contents($path),
            'pdf' => $this->fromPdf($path),
            'docx' => $this->fromDocx($path),
            'doc' => $this->fromDoc($path),
            default => throw new InvalidArgumentException('Unsupported file type.'),
        };

        $text = trim((string) $text);

        if ($text === '') {
            throw new InvalidArgumentException('Could not extract text from the uploaded file. Try pasting the CV text instead.');
        }

        return $text;
    }

    private function fromPdf(string $path): string
    {
        return (new Parser)->parseFile($path)->getText();
    }

    private function fromDocx(string $path): string
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            return $this->fromOfficeDocumentWithTextutil($path);
        }

        $zip = new ZipArchive;

        if ($zip->open($path) !== true) {
            throw new InvalidArgumentException('Could not open the DOCX file.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new InvalidArgumentException('Could not read the DOCX file contents.');
        }

        $text = str_replace(['</w:p>', '</w:tr>'], ["\n", "\n"], $xml);

        return html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function fromDoc(string $path): string
    {
        if (PHP_OS_FAMILY !== 'Darwin') {
            throw new InvalidArgumentException('Legacy .doc files are only supported on macOS. Save as PDF, DOCX, or paste the CV text instead.');
        }

        return $this->fromOfficeDocumentWithTextutil($path);
    }

    private function fromOfficeDocumentWithTextutil(string $path): string
    {
        $result = Process::run([
            'textutil',
            '-convert', 'txt',
            '-stdout',
            $path,
        ]);

        if (! $result->successful()) {
            throw new InvalidArgumentException('Could not extract text from the uploaded document.');
        }

        return $result->output();
    }
}
