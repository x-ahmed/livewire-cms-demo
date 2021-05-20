<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

        @trixassets
        @livewireStyles

        <!-- Scripts -->
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <x-jet-banner />

        <div class="min-h-screen bg-gray-100">
            @livewire('navigation-menu')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{-- show notification --}}
                <div class="fixed top-0 right-0 px-5 py-3 mt-3 mr-3 text-white duration-700 transform bg-green-400 rounded-sm shadow-lg opacity-0 event-notification-box"></div>

                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
        <script
            src="{{ asset('js/turbolinks.js') }}"
            data-turbolinks-eval="false"
            data-turbo-eval="false"></script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <x-livewire-alert::scripts />

        {{-- jquery --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script>
            /**
             * Initialize websocket client.
             * https://developer.mozilla.org/en-US/docs/Web/API/WebSocket
             *
             * @param {object} config
             * @return {object}
            */
            const createWebSocketClient = (config = {}) => {
                const route = config.route || '127.0.0.1';
                const port = config.port || 3280;

                // Create websocket client.
                return new WebSocket(`ws://${route}:${port}`);
            }

            /**
             * Open websocket connection.
             * https://developer.mozilla.org/en-US/docs/Web/API/WebSocket
             *
            */
            const connection = createWebSocketClient();
            connection.addEventListener('open', (event) => {
                console.log('connection is open');
            });

            /**
             * Listen for websocket messages and show them into UI.
             * https://developer.mozilla.org/en-US/docs/Web/API/WebSocket
             *
            */
            connection.addEventListener('message', (message) => {
                console.log('Message from server ', message.data);
                const {eventName, eventMessage} = JSON.parse(message.data);

                // Display message animation.
                const selector = '.event-notification-box';
                $(selector).html(`
                    <h3>${eventName}</h3>
                    <p>${eventMessage}</p>
                `);
                $(selector).removeClass('opacity-0');
                $(selector).addClass('opcaity-100');

                setTimeout(() => {
                    $(selector).addClass('opacity-0');
                    $(selector).removeClass('opcaity-100');
                }, 3000);
            });

            /**
             * Listen for close websocket server connection.
             * https://developer.mozilla.org/en-US/docs/Web/API/WebSocket/close_event
             *
            */
            connection.addEventListener('close', (connection) => {
                console.log('The connection has been closed successfully.', connection);
                console.log('reconnection after 3 seconds...');
                
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            });

            /**
             * Listen to dispateched livewire event and send message to websocket server.
             * https://laravel-livewire.com/docs/2.x/events#browser
             * https://developer.mozilla.org/en-US/docs/Web/API/WebSocket
            */
            window.addEventListener('event-notification', (event) => {
                console.log(event);
                const {eventName, eventMessage} = event.detail;
                connection.send(JSON.stringify({
                    eventName,
                    eventMessage,
                }));
            });
        </script>
    </body>
</html>
