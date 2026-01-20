@extends('layouts.app')

@section('title', 'Geometry Details')

@section('content')
<h1 class="title">{{ $dataset->name }}</h1>
<p>{{ $dataset->description }}</p>

<h2 class="title is-5">Summary</h2>
<ul>
    <li>Components: {{ $dataset->summary['component_count'] ?? '-' }}</li>
    <li>Total Surface Area: {{ $dataset->summary['total_surface_area_m2'] ?? '-' }} m²</li>
</ul>

<h2 class="title is-5">Exposure Distribution</h2>
<table class="table is-fullwidth">
    <thead>
        <tr>
            <th>Exposure</th>
            <th>Area (m²)</th>
        </tr>
    </thead>
    <tbody>
        @foreach (($dataset->summary['exposure_distribution_m2'] ?? []) as $exposure => $area)
            <tr>
                <td>{{ $exposure }}</td>
                <td>{{ $area }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
