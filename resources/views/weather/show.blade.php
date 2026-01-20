@extends('layouts.app')

@section('title', 'Weather Details')

@section('content')
<h1 class="title">{{ $dataset->name }}</h1>
<p>{{ $dataset->description }}</p>

<h2 class="title is-5">Summary</h2>
<ul>
    <li>Rows: {{ $dataset->summary['row_count'] ?? '-' }}</li>
    <li>Total Precipitation: {{ $dataset->summary['total_precipitation_mm'] ?? '-' }} mm</li>
    <li>Date Range: {{ $dataset->starts_at }} → {{ $dataset->ends_at }}</li>
</ul>
@endsection
