<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CV Parser</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            background: #f5f5f4;
            color: #1c1917;
            margin: 0;
            padding: 2rem 1rem;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        h1 {
            margin: 0 0 0.5rem;
            font-size: 1.75rem;
        }
        p {
            color: #57534e;
            margin: 0 0 1.5rem;
        }
        .card {
            background: #fff;
            border: 1px solid #e7e5e4;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        input[type="file"],
        textarea {
            width: 100%;
            border: 1px solid #d6d3d1;
            border-radius: 8px;
            padding: 0.75rem;
            font: inherit;
        }
        textarea {
            min-height: 140px;
            resize: vertical;
        }
        .field {
            margin-bottom: 1.25rem;
        }
        .divider {
            text-align: center;
            color: #a8a29e;
            margin: 1rem 0;
            font-size: 0.875rem;
        }
        button {
            background: #1c1917;
            color: #fff;
            border: 0;
            border-radius: 8px;
            padding: 0.75rem 1.25rem;
            font: inherit;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover {
            background: #292524;
        }
        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        pre {
            background: #1c1917;
            color: #f5f5f4;
            border-radius: 8px;
            padding: 1rem;
            overflow: auto;
            font-size: 0.875rem;
            line-height: 1.5;
            margin: 0;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }
        .badge {
            background: #ecfdf5;
            color: #047857;
            border-radius: 999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>CV Parser</h1>
        <p>Upload a resume file or paste CV text to extract structured candidate data.</p>

        @if ($errors->any())
            <div class="error">
                <ul style="margin: 0; padding-left: 1.25rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @isset($error)
            <div class="error">{{ $error }}</div>
        @endisset

        <div class="card">
            <form method="POST" action="{{ route('cv.parse') }}" enctype="multipart/form-data">
                @csrf

                <div class="field">
                    <label for="cv">Upload CV</label>
                    <input type="file" id="cv" name="cv" accept=".pdf,.txt,.doc,.docx">
                </div>

                <div class="divider">or</div>

                <div class="field">
                    <label for="text">Paste CV text</label>
                    <textarea id="text" name="text" placeholder="Paste resume content here...">{{ old('text', $text ?? '') }}</textarea>
                </div>

                <button type="submit">Parse CV</button>
            </form>
        </div>

        @isset($result)
            <div class="card">
                <div class="meta">
                    <h2 style="margin: 0; font-size: 1.125rem;">Parsed Result</h2>
                    @if (isset($result['parse_confidence']))
                        <span class="badge">Confidence: {{ number_format($result['parse_confidence'] * 100, 0) }}%</span>
                    @endif
                </div>
                <pre>{{ json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        @endisset
    </div>
</body>
</html>
