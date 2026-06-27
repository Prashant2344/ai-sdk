<?php

namespace App\Http\Controllers;

use App\Actions\ParseCv;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Ai\Exceptions\AiException;
use Throwable;

class ParseCvPageController extends Controller
{
    public function index(): View
    {
        return view('cv.parse');
    }

    public function store(Request $request, ParseCv $parseCv): View
    {
        $validated = $request->validate([
            'cv' => ['required_without:text', 'file', 'mimes:pdf,txt,doc,docx', 'max:10240'],
            'text' => ['required_without:cv', 'string', 'max:100000'],
        ]);

        try {
            $result = $parseCv->handle(
                $request->file('cv'),
                $validated['text'] ?? null,
            );
        } catch (AiException|Throwable $e) {
            return view('cv.parse', [
                'error' => $e->getMessage(),
                'text' => $request->input('text'),
            ]);
        }

        return view('cv.parse', [
            'result' => $result,
            'text' => $request->input('text'),
        ]);
    }
}
