@extends('layouts.app')

@section('title', 'Geometry Datasets')

@section('content')
<h1 class="title">Geometry Datasets</h1>
<a class="button is-link" href="{{ route('geometry.create') }}">Upload Geometry CSV</a>

<table class="table is-fullwidth">
    <thead>
        <tr>
            <th>Name</th>
            <th>Components</th>
            <th>Total Area (m²)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datasets as $dataset)
            <tr>
                <td><a href="{{ route('geometry.show', $dataset) }}">{{ $dataset->name }}</a></td>
                <td>{{ $dataset->summary['component_count'] ?? '-' }}</td>
                <td>{{ $dataset->summary['total_surface_area_m2'] ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $datasets->links() }}
@endsection
