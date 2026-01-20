@extends('layouts.app')

@section('title', 'Materials')

@section('content')
<h1 class="title">Material Types & Subtypes</h1>
<div class="columns">
    <div class="column">
        <form method="POST" action="{{ route('materials.types.store') }}">
            @csrf
            <h2 class="title is-5">Add Material Type</h2>
            <div class="field">
                <label class="label">Name</label>
                <input class="input" type="text" name="name" required>
            </div>
            <div class="field">
                <label class="label">Description</label>
                <textarea class="textarea" name="description"></textarea>
            </div>
            <button class="button is-link" type="submit">Add Type</button>
        </form>
    </div>
    <div class="column">
        <form method="POST" action="{{ route('materials.subtypes.store') }}">
            @csrf
            <h2 class="title is-5">Add Material Subtype</h2>
            <div class="field">
                <label class="label">Type</label>
                <div class="select is-fullwidth">
                    <select name="material_type_id" required>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="field">
                <label class="label">Code</label>
                <input class="input" type="text" name="code" required>
            </div>
            <div class="field">
                <label class="label">Runoff Coefficient ψ</label>
                <input class="input" type="number" step="0.01" name="runoff_coefficient" required>
            </div>
            <div class="field">
                <label class="label">Description</label>
                <textarea class="textarea" name="description"></textarea>
            </div>
            <button class="button is-link" type="submit">Add Subtype</button>
        </form>
    </div>
</div>

<h2 class="title is-4">Building Materials</h2>
<form method="POST" action="{{ route('materials.store') }}">
    @csrf
    <div class="columns">
        <div class="column">
            <label class="label">Subtype</label>
            <div class="select is-fullwidth">
                <select name="material_subtype_id" required>
                    @foreach ($types as $type)
                        <optgroup label="{{ $type->name }}">
                            @foreach ($type->subtypes as $subtype)
                                <option value="{{ $subtype->id }}">{{ $subtype->code }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="column">
            <label class="label">Material Name</label>
            <input class="input" type="text" name="name" required>
        </div>
    </div>
    <div class="field">
        <label class="label">Description</label>
        <textarea class="textarea" name="description"></textarea>
    </div>
    <h3 class="title is-6">Substance Contents (optional)</h3>
    @foreach ($substances as $substance)
        <div class="field is-horizontal">
            <div class="field-label is-normal">
                <label class="label">{{ $substance->name }}</label>
            </div>
            <div class="field-body">
                <div class="field">
                    <div class="control">
                        <input type="hidden" name="substances[{{ $loop->index }}][id]" value="{{ $substance->id }}">
                        <input class="input" type="number" step="0.001" name="substances[{{ $loop->index }}][initial_content]" placeholder="c0">
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    <button class="button is-link" type="submit">Add Building Material</button>
</form>

<h2 class="title is-4">Existing Materials</h2>
<table class="table is-fullwidth">
    <thead>
        <tr>
            <th>Name</th>
            <th>Subtype</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($materials as $material)
            <tr>
                <td>{{ $material->name }}</td>
                <td>{{ $material->subtype->code ?? '-' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
{{ $materials->links() }}
@endsection
