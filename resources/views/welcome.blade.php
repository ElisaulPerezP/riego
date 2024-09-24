<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            body {
                font-family: 'Figtree', sans-serif;
                background-color: #f3f4f6;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 0;
            }

            .container {
                text-align: center;
            }

            .logo {
                margin-bottom: 2rem;
            }

            .buttons a {
                margin: 0 1rem;
                padding: 0.5rem 1.5rem;
                font-size: 1rem;
                color: #ffffff;
                background-color: #4f46e5;
                border-radius: 0.375rem;
                text-decoration: none;
                font-weight: 600;
                transition: background-color 0.3s ease;
            }

            .buttons a:hover {
                background-color: #4338ca;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <!-- Logo -->
            <div class="logo">
                <img src="{{ asset('storage/logo.svg') }}" alt="Logo" width="150">
            </div>

            <!-- Buttons for login and register -->
            <div class="buttons">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </body>
</html>
