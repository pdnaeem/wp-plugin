<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
    </style>
</head>
<body>
    <h1>COMLEAM Calculation Report</h1>
    <p><strong>Name:</strong> {{ $calculation->name }}</p>
    <p><strong>Status:</strong> {{ $calculation->status }}</p>

    <h2>Inputs</h2>
    <ul>
        <li>Geometry: {{ $calculation->geometry->name }}</li>
        <li>Weather: {{ $calculation->weather->name }}</li>
        <li>Substance: {{ $calculation->substance->name }}</li>
        <li>Emission Function: {{ $calculation->emissionFunction->function_type }}</li>
    </ul>

    <h2>Summary Results</h2>
    <table>
        <tbody>
            @foreach ($summary as $key => $value)
                <tr>
                    <th>{{ $key }}</th>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Timeseries output: {{ $timeseriesPath }}</p>
</body>
</html>
