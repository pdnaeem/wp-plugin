@extends('layouts.app')

@section('title', 'Emission Functions')

@section('content')
<h1 class="title">Emission Functions</h1>
<a class="button is-link" href="{{ route('emissions.create') }}">Create Emission Function</a>

<table class="table is-fullwidth">
    <thead>
        <tr>
            <th>Substance</th>
            <th>Type</th>
            <th>Source</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($functions as $function)
            <tr>
                <td><a href="{{ route('emissions.show', $function) }}">{{ $function->substance->name }}</a></td>
                <td>{{ $function->function_type }}</td>
                <td>{{ $function->source }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $functions->links() }}
@endsection
