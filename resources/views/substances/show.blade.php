@extends('layouts.app')

@section('title', 'Substance Details')

@section('content')
<h1 class="title">{{ $substance->name }}</h1>
<ul>
    <li>CAS: {{ $substance->cas_number }}</li>
    <li>EC: {{ $substance->ec_number }}</li>
    <li>Acute Threshold: {{ $substance->acute_threshold }}</li>
    <li>Chronic Threshold: {{ $substance->chronic_threshold }}</li>
</ul>
<p>{{ $substance->description }}</p>
@endsection
