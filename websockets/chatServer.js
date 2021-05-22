const htmlEntities = require("html-entities");
const uniqId = require("uniqid");
const mysql = require("mysql");

// Create node server.
const PORT = 3281;
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

	// Unique identifier for the connection instance of the client
	let connection_id;
	// The users' room_id
	let room_id;

	// Receive the client's applications' messages.
	connection.on("message", (message) => {
		// console.log(message);

		// Parse data that would be sent to clients.
		const utf8Data = JSON.parse(message.utf8Data);

		// prepare data that would be sent to clients.
		if (message.type === "utf8") {
			if (utf8Data.type === "chatInfo") {
				console.log("chatInfo", utf8Data);

				// Generate a unique identifier.
				connection_id = `connection__${uniqId()}`;

				// Store the room_id value
				room_id = utf8Data.data.room_id;

				// Push the connection instance.
				clients.push({
					connection,
					connection_id,
					room_id,
					user_id       : utf8Data.data.user_id,
				});

				console.log("the connection info of the connected client:", {
					connection_id,
					room_id,
					user_id       : utf8Data.data.user_id,
				});

                loadChatHistory(room_id, 20);
			} else if (utf8Data.type === "chatMessage") {
				console.log("chatMessage", utf8Data);
				retrieveLatestChatMessage();
			}
		}
	});

	// Detect whether the current connection is closed.
	connection.on("close", (connection) => {});

	/**
     * Load the chat history.
     *
     * @param  {integer} room_id
     * @param  {integer} messageLimit=30
     */
	const loadChatHistory = (room_id, messageLimit = 30) => {
		const statement = `
            SELECT
                messages.room_id, messages.user_id, messages.message, messages.created_at, users.name
            FROM
                messages
            LEFT JOIN
                users
            ON
                messages.user_id = users.id
            WHERE
                messages.room_id = ${room_id}
            ORDER BY
                messages.created_at
            ASC
            LIMIT
                ${messageLimit};
        `;

		// DB configurations.
		const db = mysql.createConnection({
			host     : "localhost",
			user     : "root",
			password : "",
			database : "livewire_cms_demo",
		});

		// start connected db instance.
		db.connect();

		// perform query statement operation.
		db.query(statement, (error, results, fields) => {
			if (error) {
				console.error(error);
				throw error;
			}

			if (results) {
				console.log("The history results are: ", results[0]);

				results.forEach((dbRecord) => {
					clients.forEach((client) => {
						// broadcast message to a specific client.
						if (
							client.room_id === room_id &&
							client.connection_id === connection_id
						) {
							client.connection.sendUTF(
								JSON.stringify({
									type : "chatMessage",
									data : {
										room_id    : dbRecord["room_id"],
										user_id    : dbRecord["user_id"],
										name       : htmlEntities.encode(
											dbRecord["name"],
										),
										message    : htmlEntities.encode(
											dbRecord["message"],
										),
										created_at : dbRecord["created_at"],
									},
								}),
							);
						}
					});
				});
			}
		});

		// close DB instance connection.
		db.end();
	};

	/**
     * Retrieve the latest message within a specific room.
	 */
	const retrieveLatestChatMessage = () => {
		const statement = `
            SELECT
                messages.room_id, messages.user_id, messages.message, messages.created_at, users.name
            FROM
                messages
            LEFT JOIN
                users
            ON
                messages.user_id = users.id
            WHERE
                messages.room_id = ${room_id}
            ORDER BY
                messages.created_at
            DESC
            LIMIT
                1;
        `;

		// DB configurations.
		const db = mysql.createConnection({
			host     : "localhost",
			user     : "root",
			password : "",
			database : "livewire_cms_demo",
		});

		// start connected db instance.
		db.connect();

		// perform query statement operation.
		db.query(statement, (error, results, fields) => {
			if (error) {
				console.error(error);
				throw error;
			}

			if (results) {
				console.log("The results are: ", results[0]);

				// broadcast message to all users in the same room.
				clients.forEach((client) => {
					if (client.room_id === room_id) {
						client.connection.sendUTF(
							JSON.stringify({
								type : "chatMessage",
								data : {
									room_id    : results[0]["room_id"],
									user_id    : results[0]["user_id"],
									name       : htmlEntities.encode(
										results[0]["name"],
									),
									message    : htmlEntities.encode(
										results[0]["message"],
									),
									created_at : results[0]["created_at"],
								},
							}),
						);
					}
				});
			}
		});

		// close DB instance connection.
		db.end();
	};
});
