<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('service-worker.js') }}')
                    .then(registration => {
                        console.log('Service Worker registrado com sucesso: ', registration.scope);
                    })
                    .catch(error => {
                        console.log('Falha ao registrar Service Worker: ', error);
                    });
            });
        }
    </script>
  </head>
   <body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
        @yield('content')
        @stack('scripts')
        @livewireScripts
    </body>
</html>