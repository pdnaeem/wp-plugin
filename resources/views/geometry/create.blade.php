@extends('layouts.app')

@section('title', 'Upload Geometry')

@section('content')
<h1 class="title">Upload Geometry Dataset</h1>
<form method="POST" action="{{ route('geometry.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="field">
        <label class="label">Name</label>
        <div class="control">
            <input class="input" type="text" name="name" required>
        </div>
    </div>
    <div class="field">
        <label class="label">Description</label>
        <div class="control">
            <textarea class="textarea" name="description"></textarea>
        </div>
    </div>
    <div class="field">
        <label class="label">CSV File (semicolon separated)</label>
        <div class="control">
            <input class="input" type="file" name="file" required>
        </div>
    </div>
    <button class="button is-link" type="submit">Upload</button>
</form>
@endsection
