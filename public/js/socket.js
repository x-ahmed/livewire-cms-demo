/**
 * Initialize websocket client.
 * https://developer.mozilla.org/en-US/docs/Web/API/WebSocket
 *
 * @param {object} config
 * @return {object}
 */
const createWebSocketClient = (config = {}) => {
	const route = config.route || "127.0.0.1";
	const port = config.port || 3280;

	// Create websocket client.
	return new WebSocket(`ws://${route}:${port}`);
};
