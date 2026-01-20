@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="columns is-centered">
    <div class="column is-half">
        <h1 class="title">Login</h1>
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
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
                <label class="checkbox">
                    <input type="checkbox" name="remember"> Remember me
                </label>
            </div>
            <div class="field">
                <button class="button is-link" type="submit">Login</button>
                <a class="button is-text" href="{{ route('register') }}">Register</a>
            </div>
        </form>
    </div>
</div>
@endsection
