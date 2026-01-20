@extends('layouts.app')

@section('title', 'Calculations')

@section('content')
<h1 class="title">Calculations</h1>
<a class="button is-link" href="{{ route('calculations.create') }}">New Calculation</a>

<table class="table is-fullwidth">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($calculations as $calc)
            <tr>
                <td><a href="{{ route('calculations.show', $calc) }}">{{ $calc->name }}</a></td>
                <td>{{ $calc->status }}</td>
                <td>{{ $calc->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $calculations->links() }}
@endsection
