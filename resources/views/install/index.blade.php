@extends('layouts.app')

@section('title', 'Install')

@section('content')
<h1 class="title">Installer</h1>

@if ($installed)
    <div class="notification is-success">
        The application is already installed.
    </div>
@else
    <p class="mb-4">Enter the installer key and database settings to initialize the application.</p>
    <form method="POST" action="{{ route('install.store') }}">
        @csrf
        <div class="field">
            <label class="label">Installer Key</label>
            <input class="input" type="text" name="installer_key" required>
        </div>
        <div class="field">
            <label class="label">Application Name</label>
            <input class="input" type="text" name="app_name" value="COMLEAM Web" required>
        </div>
        <div class="field">
            <label class="label">Application URL</label>
            <input class="input" type="url" name="app_url" placeholder="https://example.com" required>
        </div>
        <div class="columns">
            <div class="column">
                <label class="label">DB Host</label>
                <input class="input" type="text" name="db_host" value="127.0.0.1" required>
            </div>
            <div class="column">
                <label class="label">DB Port</label>
                <input class="input" type="number" name="db_port" value="3306" required>
            </div>
        </div>
        <div class="columns">
            <div class="column">
                <label class="label">DB Database</label>
                <input class="input" type="text" name="db_database" required>
            </div>
            <div class="column">
                <label class="label">DB Username</label>
                <input class="input" type="text" name="db_username" required>
            </div>
        </div>
        <div class="field">
            <label class="label">DB Password</label>
            <input class="input" type="password" name="db_password">
        </div>
        <button class="button is-link" type="submit">Run Installer</button>
    </form>
@endif
@endsection
