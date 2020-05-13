// ########### CLIENT CONFIG ###########

// Our country code
const COUNTRY_CODE = 'SU';

// Our bank code
const BANK_CODE = 'BANQ';

// When disconnect try to reconnect timeout (in ms)
const RECONNECT_TIMEOUT = 2 * 1000;

// The local http server port
const HTTP_SERVER_PORT = process.env.PORT || 8081;

// Banq API keys
const BANQ_API_URL = 'https://banq.ml/api';
const BANQ_API_DEVICE_KEY = '5d19e2ac9ed1c9a4f14350b46a10bf25';

// ########### CLIENT CODE ###########
// Import http, https, url and websocket libs
const http = require('http');
const https = require('https');
const url = require('url');
const WebSocket = require('ws');

// A wrapper function to do a http request and return the data
function fetch(url, callback) {
    https.get(url, function (response) {
        let body = '';
        response.on('data', function (data) {
            body += data;
        });
        response.on('end', function () {
            callback(JSON.parse(body));
        });
    });
}

// A function that parses a standart account id string
function parseAccountParts(account) {
    return {
        country: account.substring(0, 2),
        bank: account.substring(3, 7),
        account: parseInt(account.substring(8))
    };
}

// The Banq Gosbank client http server
let httpServer;

// Connects to Gosbank
function connectToGosbank() {
    const ws = new WebSocket('wss://ws.gosbank.ml/');

    // Do a request and some callback logic
    const pendingCallbacks = [];

    function requestMessage(type, data, callback) {
        const id = Date.now();

        if (callback !== undefined) {
            pendingCallbacks.push({ id: id, type: type + '_response', callback: callback });
        }

        ws.send(JSON.stringify({ id: id, type: type, data: data }));
    }

    // Reponse to a message
    function responseMessage(id, type, data) {
        ws.send(JSON.stringify({ id: id, type: type + '_response', data: data }));
    }

    // A wrapper function to request register message
    function requestRegister(callback) {
        requestMessage('register', {
            header: {
                originCountry: COUNTRY_CODE,
                originBank: BANK_CODE,
                receiveCountry: 'SU',
                receiveBank: 'GOSB'
            },
            body: {}
        }, callback);
    }

    // A wrapper function for requesting a balance message
    function requestBalance(account, pin, callback) {
        const toAccountParts = parseAccountParts(account);

        requestMessage('balance', {
            header: {
                originCountry: COUNTRY_CODE,
                originBank: BANK_CODE,
                receiveCountry: toAccountParts.country,
                receiveBank: toAccountParts.bank
            },
            body: {
                account: account,
                pin: pin
            }
        }, callback);
    }

    // A wrapper function for requesting a payment message
    function requestPayment(fromAccount, toAccount, pin, amount, callback) {
        const formAccountParts = parseAccountParts(fromAccount);
        const toAccountParts = parseAccountParts(toAccount);

        if (formAccountParts.bank !== BANK_CODE) {
            requestMessage('payment', {
                header: {
                    originCountry: COUNTRY_CODE,
                    originBank: BANK_CODE,
                    receiveCountry: formAccountParts.country,
                    receiveBank: formAccountParts.bank
                },
                body: {
                    fromAccount: fromAccount,
                    toAccount: toAccount,
                    pin: pin,
                    amount: amount
                }
            }, callback);
        }

        if (toAccountParts.bank !== BANK_CODE) {
            requestMessage('payment', {
                header: {
                    originCountry: COUNTRY_CODE,
                    originBank: BANK_CODE,
                    receiveCountry: toAccountParts.country,
                    receiveBank: toAccountParts.bank
                },
                body: {
                    fromAccount: fromAccount,
                    toAccount: toAccount,
                    pin: pin,
                    amount: amount
                }
            }, callback);
        }
    }

    // When connected to gosbank
    ws.on('open', function () {
        // Try to register
        requestRegister(function ({ body }) {
            if (body.code === 200) {
                console.log('Connected with Gosbank with bank code: ' + BANK_CODE);

                // Create local HTTP API for the Banq website API
                httpServer = http.createServer(function (req, res) {
                    const { pathname, query } = url.parse(req.url, true);

                    if (pathname === '/') {
                        res.writeHead(200, { 'Content-Type': 'text/html' });
                        res.end('<h1>Banq Gosbank Client Local API</h1>');
                    }

                    else if (pathname.startsWith('/api/gosbank/accounts/')) {
                        const account = pathname.replace('/api/gosbank/accounts/', '');
                        requestBalance(account, query.pin, function ({ body }) {
                            res.writeHead(200, { 'Content-Type': 'application/json' });
                            res.end(JSON.stringify(body));
                        });
                    }

                    else if (pathname === '/api/gosbank/transactions/create') {
                        requestPayment(query.from, query.to, query.pin, query.amount, function ({ body }) {
                            res.writeHead(200, { 'Content-Type': 'application/json' });
                            res.end(JSON.stringify(body));
                        });
                    }

                    else {
                        res.writeHead(200, { 'Content-Type': 'text/html' });
                        res.end('<h1>404 Not Found</h1>');
                    }
                });
                httpServer.listen(HTTP_SERVER_PORT);
            }
            else {
                console.log('Error with connecting to Gosbank, reason: ' + body.code);
            }
        });
    });

    // When we get a message
    ws.on('message', function (message) {
        // Parse the message
        const { id, type, data } = JSON.parse(message);

        // Try first to reslove a callback
        for (var i = 0; i < pendingCallbacks.length; i++) {
            if (pendingCallbacks[i].id === id && pendingCallbacks[i].type === type) {
                pendingCallbacks[i].callback(data);
                pendingCallbacks.splice(i--, 1);
            }
        }

        // On balance request
        if (type === 'balance') {
            console.log('Balance request for: ' + data.body.account);

            // Fetch balance info from database via API
            fetch(BANQ_API_URL + '/gosbank/accounts/' + data.body.account + '?key=' + BANQ_API_DEVICE_KEY + '&pincode=' + data.body.pincode, function (response) {
                responseMessage(id, 'balance', {
                    header: {
                        originCountry: COUNTRY_CODE,
                        originBank: BANK_CODE,
                        receiveCountry: data.header.originCountry,
                        receiveBank: data.header.originBank
                    },
                    body: response
                });
            });
        }

        // On payment request
        if (type === 'payment') {
            console.log('Payment request for: ' + data.body.toAccount);

            // Send to Banq API
            fetch(BANQ_API_URL + '/gosbank/transactions/create?key=' + BANQ_API_DEVICE_KEY + '&from=' + data.body.fromAccount + '&to=' + data.body.toAccount + '&pincode=' + data.body.pincode + '&amount=' + data.body.amount, function (reponse) {
                responseMessage(id, 'payment', {
                    header: {
                        originCountry: COUNTRY_CODE,
                        originBank: BANK_CODE,
                        receiveCountry: data.header.originCountry,
                        receiveBank: data.header.originBank
                    },
                    body: reponse
                });
            });
        }
    });

    // When closed try to reconnected
    ws.on('close', function () {
        // Close the server if open
        if (httpServer !== undefined) {
            httpServer.close();
        }

        // Try to reconnect
        console.log('Disconnected, try to reconnect in ' + Math.round(RECONNECT_TIMEOUT / 1000) + ' seconds!');
        setTimeout(connectToGosbank, RECONNECT_TIMEOUT);
    });

    // Ingnore connecting errors because reconnect in the close handler
    ws.on('error', function (error) {});
}

// Try to connect to gosbank
connectToGosbank();
