const HtmlEntities = require("html-entities");

// Create node server.
const PORT = 3280;
const Http = require("http");
const httpServer = Http.createServer();
httpServer.listen(PORT, () => {
	console.log(`Server is listening to port: ${PORT}`);
});

/**
 * List of currently connected clients.
 *
 * @const {array} $clients
 */
const clients = [];

// Create websocket server. https://www.npmjs.com/package/websocket server example
const WebSocket = require("websocket").server;
const wsServer = new WebSocket({ httpServer });

// Accept an open connection to any client application the requests the websocket.
wsServer.on("request", (request) => {
	// Hold connection information from the client application's accepted request.
	const connection = request.accept(null, request.origin);

	// Push connection instances to the clients array.
	const index = clients.push(connection) - 1; // subtracted by one to start the index from 0.
	console.log("Client of index:", index, "is connected");

	// Receive the client's applications' messages.
	connection.on("message", (message) => {
		console.log(message);

		// Parse data that would be sent to clients.
		const utf8Data = JSON.parse(message.utf8Data);

		// prepare data that would be sent to clients.
		if (message.type === "utf8") {
			// Stringify encoded data to be sent
			const obj = JSON.stringify({
				eventName    : HtmlEntities.encode(utf8Data.eventName),
				eventMessage : HtmlEntities.encode(utf8Data.eventMessage),
			});

			// Send/broadcast message to all connected clients.
			clients.forEach((client) => {
				client.sendUTF(obj);
			});
		}
	});

	// Detect whether the current connection is closed.
	connection.on("close", (connection) => {
		// Remove closed connection from clients array.
		clients.splice(index, 1);
		console.log("Client of index:", index, "is disconnected");
	});
});
