@extends('layouts.app')

@section('title', 'New Calculation')

@section('content')
<h1 class="title">New Calculation</h1>
<form method="POST" action="{{ route('calculations.store') }}">
    @csrf
    <div class="field">
        <label class="label">Name</label>
        <input class="input" type="text" name="name" required>
    </div>
    <div class="field">
        <label class="label">Description</label>
        <textarea class="textarea" name="description"></textarea>
    </div>
    <div class="columns">
        <div class="column">
            <label class="label">Geometry Dataset</label>
            <div class="select is-fullwidth">
                <select name="geometry_dataset_id" required>
                    @foreach ($geometry as $dataset)
                        <option value="{{ $dataset->id }}">{{ $dataset->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="column">
            <label class="label">Weather Dataset</label>
            <div class="select is-fullwidth">
                <select name="weather_dataset_id" required>
                    @foreach ($weather as $dataset)
                        <option value="{{ $dataset->id }}">{{ $dataset->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="columns">
        <div class="column">
            <label class="label">Substance</label>
            <div class="select is-fullwidth">
                <select name="substance_id" required>
                    @foreach ($substances as $substance)
                        <option value="{{ $substance->id }}">{{ $substance->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="column">
            <label class="label">Emission Function</label>
            <div class="select is-fullwidth">
                <select name="emission_function_id" required>
                    @foreach ($emissionFunctions as $function)
                        <option value="{{ $function->id }}">{{ $function->function_type }} ({{ $function->substance->name }})</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="columns">
        <div class="column">
            <label class="label">Date Range Start</label>
            <input class="input" type="date" name="date_range_start" required>
        </div>
        <div class="column">
            <label class="label">Date Range End</label>
            <input class="input" type="date" name="date_range_end" required>
        </div>
    </div>
    <div class="field">
        <label class="checkbox">
            <input type="checkbox" name="wdr_enabled" value="1"> Enable WDR
        </label>
    </div>
    <div class="field">
        <label class="label">WDR Factor</label>
        <input class="input" type="number" step="0.1" name="wdr_factor" value="1.0">
    </div>
    <div class="field">
        <label class="label">Receiving Water Class</label>
        <div class="select">
            <select name="receiving_water_class">
                <option value="S">Small</option>
                <option value="M" selected>Medium</option>
                <option value="L">Large</option>
            </select>
        </div>
    </div>
    <button class="button is-link" type="submit">Run (Queue)</button>
</form>
@endsection
