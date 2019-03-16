<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="stylesheet" href="css/app.css">


        <title>040 - @yield('title', 'Lorem ipsum')</title>

    </head>
    <body>

        <main role="main">
            @yield('content')
        </main>

    </body>
</html>