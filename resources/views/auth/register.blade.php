@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="columns is-centered">
    <div class="column is-half">
        <h1 class="title">Register</h1>
        <form method="POST" action="{{ route('register.submit') }}">
            @csrf
            <div class="field">
                <label class="label">Name</label>
                <div class="control">
                    <input class="input" type="text" name="name" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Email</label>
                <div class="control">
                    <input class="input" type="email" name="email" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Password</label>
                <div class="control">
                    <input class="input" type="password" name="password" required>
                </div>
            </div>
            <div class="field">
                <label class="label">Confirm Password</label>
                <div class="control">
                    <input class="input" type="password" name="password_confirmation" required>
                </div>
            </div>
            <div class="field">
                <button class="button is-link" type="submit">Register</button>
                <a class="button is-text" href="{{ route('login') }}">Back to login</a>
            </div>
        </form>
    </div>
</div>
@endsection
