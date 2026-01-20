<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }} - @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<nav class="navbar is-link" role="navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="{{ route('dashboard') }}">{{ config('app.name') }}</a>
    </div>
    @auth
        <div class="navbar-menu">
            <div class="navbar-start">
                <a class="navbar-item" href="{{ route('geometry.index') }}">Geometry</a>
                <a class="navbar-item" href="{{ route('weather.index') }}">Weather</a>
                <a class="navbar-item" href="{{ route('substances.index') }}">Substances</a>
                <a class="navbar-item" href="{{ route('materials.index') }}">Materials</a>
                <a class="navbar-item" href="{{ route('emissions.index') }}">Emission Functions</a>
                <a class="navbar-item" href="{{ route('calculations.index') }}">Calculations</a>
            </div>
            <div class="navbar-end">
                <div class="navbar-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="button is-light" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <div class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item">
                    <a class="button is-light" href="{{ route('login') }}">Login</a>
                </div>
            </div>
        </div>
    @endauth
</nav>

<section class="section">
    <div class="container">
        @if ($errors->any())
            <div class="notification is-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @yield('content')
    </div>
</section>
</body>
</html>
