@extends('layouts.app')

@section('title', 'Calculation Details')

@section('content')
<h1 class="title">{{ $calculation->name }}</h1>
<p>Status: <strong id="status">{{ $calculation->status }}</strong></p>

<h2 class="title is-5">Inputs</h2>
<ul>
    <li>Geometry: {{ $calculation->geometry->name }}</li>
    <li>Weather: {{ $calculation->weather->name }}</li>
    <li>Substance: {{ $calculation->substance->name }}</li>
    <li>Emission Function: {{ $calculation->emissionFunction->function_type }}</li>
</ul>

@if ($calculation->result)
    <h2 class="title is-5">Summary</h2>
    <table class="table is-fullwidth">
        @foreach ($calculation->result->summary as $key => $value)
            <tr>
                <th>{{ $key }}</th>
                <td>{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    <div class="buttons">
        <a class="button is-link" href="{{ route('calculations.report', $calculation) }}">Download PDF</a>
        <a class="button is-info" href="{{ route('calculations.export', $calculation) }}">Download ZIP</a>
    </div>
@endif

<script>
    const currentStatus = '{{ $calculation->status }}';
    if (!['finished', 'failed'].includes(currentStatus)) {
        setInterval(() => {
            window.location.reload();
        }, 8000);
    }
</script>
@endsection
