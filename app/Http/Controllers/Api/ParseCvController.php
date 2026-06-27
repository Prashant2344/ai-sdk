<?php

namespace App\Http\Controllers\Api;

use App\Actions\ParseCv;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParseCvController extends Controller
{
    public function __invoke(Request $request, ParseCv $parseCv): JsonResponse
    {
        $validated = $request->validate([
            'cv' => ['required_without:text', 'file', 'mimes:pdf,txt,doc,docx', 'max:10240'],
            'text' => ['required_without:cv', 'string', 'max:100000'],
        ]);

        $result = $parseCv->handle(
            $request->file('cv'),
            $validated['text'] ?? null,
        );

        return response()->json($result);
    }
}
