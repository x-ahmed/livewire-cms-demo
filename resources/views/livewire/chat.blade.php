<div>
    <button class="open-button"
        wire:click="$toggle('popChatUp')">Chat</button>
    @if ($popChatUp)
        <div class="chat-popup">
            <form class="form-container">
                <div class="flex items-center justify-between">
                    <label class="font-semibold">Message</label>
                    <div class="font-semibold">Room ID: #{{ $roomId }}</div>
                </div>
                <div wire:ignore
                    class="h-64 px-3 py-2 overflow-y-auto border bg-gray-50"
                    style="width: 280px"
                    id="message-box"></div>
                <textarea id="message"
                    class="w-full px-3 py-2 focus:outline-none focus:bg-gray-100"
                    placeholder="Type in your message here..."
                    wire:model="message"
                    wire:keydown.enter.prevent="send"></textarea>
                <button class="btn cancel"
                    type="button"
                    wire:click="$toggle('popChatUp')">Close</button>
            </form>
        </div>
    @endif
    @push('chat-websocket-client')
        <script>
            $(() => {
                /**
                 * Keeps the chat message box focus
                 * at the bottom.
                 *
                 * @param {string} elementId
                 */
                const keepChatboxFocusAtBottom = (elementId) => {
                    const element = document.getElementById(elementId);
                    element.scrollTop = element.scrollHeight;
                }

                /**
                 * Returns the chat message proper format
                 *
                 * @param {string} id
                 * @param {string} username
                 * @param {string} message
                 */
                const messageFormat = (id, name, message) => {
                    const userId = "{{ auth()->user()->id }}";
                    const color = id == userId ? "bg-blue-400" : "bg-green-400";
                    const alignment = id == userId ? "text-right" : "text-left";
                    return `
                            <div class="grid items-center grid-cols-1 gap-0">
                                <span class="${alignment} font-semibold text-sm">${name}</span>
                                <span class="${alignment} ${color} text-sm text-white px-3 py-2 rounded mb-2">${message}</span>
                            </div>
                        `;
                }

                /**
                 * Open websocket connection.
                 * https://developer.mozilla.org/en-US/docs/Web/API/WebSocket
                 *
                 */
                const chatSocket = createWebSocketClient({
                    port: 3281
                });
                chatSocket.addEventListener('open', (event) => {
                    console.log('chat connection is open');

                    // Send information of the client user.
                    chatSocket.send(JSON.stringify({
                        type: 'chatInfo',
                        data: {
                            room_id: {{ $roomId }},
                            user_id: {{ auth()->id() }},
                        },
                    }));

                });

                /**
                 * Listen for websocket messages and show them into UI.
                 * https://developer.mozilla.org/en-US/docs/Web/API/WebSocket
                 *
                 */
                chatSocket.addEventListener('message', (serverMessage) => {
                    console.log(serverMessage.data);

                    const result = JSON.parse(serverMessage.data);
                    const {
                        user_id,
                        room_id,
                        message,
                        name,
                        created_at
                    } = result.data;

                    if (result.type === 'chatMessage') {
                        messageBox.append(messageFormat(
                            user_id,
                            name,
                            message,
                        ));
                    }
                    keepChatboxFocusAtBottom('message-box');
                });

                /**
                 * Listen to livewire event and dispatch message to chat server
                 */
                window.addEventListener('send-message-to-chat-server', (event) => {
                    console.log(event.detail);
                    chatSocket.send(JSON.stringify({
                        type: 'chatMessage',
                        data: event.detail,
                    }));
                });

                /**
                 * reload page on closing chat to presest the data inside message box.
                 */
                window.addEventListener('reload-page', (event) => {
                    window.location.reload();
                });

                // The messageBox element selector.
                const messageBox = $("#message-box");
                // The message element selector.
                const message = $("#message");

            });

        </script>
    @endpush
</div>
