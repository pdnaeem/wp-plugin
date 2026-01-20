@extends('layouts.app')

@section('title', 'Create Emission Function')

@section('content')
<h1 class="title">Create Emission Function</h1>

<div class="columns">
    <div class="column">
        <h2 class="title is-5">Manual</h2>
        <form method="POST" action="{{ route('emissions.manual.store') }}">
            @csrf
            <div class="field">
                <label class="label">Substance</label>
                <div class="select is-fullwidth">
                    <select name="substance_id" required>
                        @foreach ($substances as $substance)
                            <option value="{{ $substance->id }}">{{ $substance->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="field">
                <label class="label">Function Type</label>
                <div class="select is-fullwidth">
                    <select name="function_type" required>
                        @foreach ($types as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="field">
                <label class="label">Parameters (JSON)</label>
                <textarea class="textarea" name="parameters">{"a":1,"b":0.1}</textarea>
            </div>
            <button class="button is-link" type="submit">Save Manual Function</button>
        </form>
    </div>
    <div class="column">
        <h2 class="title is-5">Fit from CSV</h2>
        <form method="POST" action="{{ route('emissions.fit.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="field">
                <label class="label">Substance</label>
                <div class="select is-fullwidth">
                    <select name="substance_id" required>
                        @foreach ($substances as $substance)
                            <option value="{{ $substance->id }}">{{ $substance->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="field">
                <label class="label">Function Type</label>
                <div class="select is-fullwidth">
                    <select name="function_type" required>
                        @foreach ($types as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="field">
                <label class="label">Leaching CSV</label>
                <input class="input" type="file" name="file" required>
            </div>
            <button class="button is-link" type="submit">Fit Function</button>
        </form>
    </div>
</div>
@endsection
