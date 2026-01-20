@extends('layouts.app')

@section('title', 'Substances')

@section('content')
<h1 class="title">Substances</h1>
<a class="button is-link" href="{{ route('substances.create') }}">Add Substance</a>

<table class="table is-fullwidth">
    <thead>
        <tr>
            <th>Name</th>
            <th>CAS</th>
            <th>Acute Threshold</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($substances as $substance)
            <tr>
                <td><a href="{{ route('substances.show', $substance) }}">{{ $substance->name }}</a></td>
                <td>{{ $substance->cas_number }}</td>
                <td>{{ $substance->acute_threshold }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $substances->links() }}
@endsection
