@extends('layouts.app')

@section('title', 'Weather Datasets')

@section('content')
<h1 class="title">Weather Datasets</h1>
<a class="button is-link" href="{{ route('weather.create') }}">Upload Weather CSV</a>

<table class="table is-fullwidth">
    <thead>
        <tr>
            <th>Name</th>
            <th>Rows</th>
            <th>Date Range</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($datasets as $dataset)
            <tr>
                <td><a href="{{ route('weather.show', $dataset) }}">{{ $dataset->name }}</a></td>
                <td>{{ $dataset->summary['row_count'] ?? '-' }}</td>
                <td>{{ $dataset->starts_at }} → {{ $dataset->ends_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $datasets->links() }}
@endsection
