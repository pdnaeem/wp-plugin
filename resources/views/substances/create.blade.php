@extends('layouts.app')

@section('title', 'Add Substance')

@section('content')
<h1 class="title">Add Substance</h1>
<form method="POST" action="{{ route('substances.store') }}">
    @csrf
    <div class="field">
        <label class="label">Name</label>
        <div class="control">
            <input class="input" type="text" name="name" required>
        </div>
    </div>
    <div class="field">
        <label class="label">CAS Number</label>
        <div class="control">
            <input class="input" type="text" name="cas_number">
        </div>
    </div>
    <div class="field">
        <label class="label">EC Number</label>
        <div class="control">
            <input class="input" type="text" name="ec_number">
        </div>
    </div>
    <div class="columns">
        <div class="column">
            <label class="label">Acute Threshold</label>
            <input class="input" type="number" step="0.001" name="acute_threshold">
        </div>
        <div class="column">
            <label class="label">Chronic Threshold</label>
            <input class="input" type="number" step="0.001" name="chronic_threshold">
        </div>
    </div>
    <div class="field">
        <label class="label">Description</label>
        <textarea class="textarea" name="description"></textarea>
    </div>
    <button class="button is-link" type="submit">Save</button>
</form>
@endsection
