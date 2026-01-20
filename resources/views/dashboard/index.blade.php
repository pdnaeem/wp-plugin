@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="title">Dashboard</h1>
<div class="columns is-multiline">
    @foreach ($counts as $label => $count)
        <div class="column is-one-third">
            <div class="box">
                <p class="heading">{{ ucfirst($label) }}</p>
                <p class="title">{{ $count }}</p>
            </div>
        </div>
    @endforeach
</div>

<h2 class="title is-4">Recent Calculations</h2>
<table class="table is-fullwidth">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($recentCalculations as $calc)
            <tr>
                <td><a href="{{ route('calculations.show', $calc) }}">{{ $calc->name }}</a></td>
                <td>{{ ucfirst($calc->status) }}</td>
                <td>{{ $calc->created_at }}</td>
            </tr>
        @empty
            <tr><td colspan="3">No calculations yet.</td></tr>
        @endforelse
    </tbody>
</table>
@endsection
