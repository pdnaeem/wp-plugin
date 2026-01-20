@extends('layouts.app')

@section('title', 'Emission Function Details')

@section('content')
<h1 class="title">Emission Function</h1>
<ul>
    <li>Substance: {{ $function->substance->name }}</li>
    <li>Type: {{ $function->function_type }}</li>
    <li>Source: {{ $function->source }}</li>
</ul>

<h2 class="title is-5">Parameters</h2>
<pre>{{ json_encode($function->parameters, JSON_PRETTY_PRINT) }}</pre>

@if ($function->diagnostics)
    <h2 class="title is-5">Diagnostics</h2>
    <pre>{{ json_encode($function->diagnostics, JSON_PRETTY_PRINT) }}</pre>
@endif
@endsection
