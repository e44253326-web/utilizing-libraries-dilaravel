<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased pink-app">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <nav class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
                <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
                    <a href="{{ route('dashboard') }}" class="font-semibold text-gray-800 dark:text-gray-200">
                        {{ config('app.name', 'Laravel') }}
                    </a>

                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('settings.index') }}" class="text-sm text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                Pengaturan akun
                            </a>
                            <a href="{{ route('profile.edit') }}" class="text-sm text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                {{ Auth::user()->name }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="text-sm text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                    {{ __('Log Out') }}
                                </button>
                            </form>
                        @endauth

                        @guest
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                {{ __('Log In') }}
                            </a>
                            <a href="{{ route('register') }}" class="text-sm text-gray-700 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white">
                                {{ __('Register') }}
                            </a>
                        @endguest
                    </div>
                </div>
            </nav>

            @isset($header)
                <header class="bg-white shadow dark:bg-gray-800">
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
