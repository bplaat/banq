// ########### CLIENT CONFIG ###########

// Your country code always 'SU'
const COUNTRY_CODE = 'SU';

// Your bank code
const BANK_CODE = 'BANQ';

// When disconnect try to reconnect timeout (in ms)
const RECONNECT_TIMEOUT = 2 * 1000;

// ########### CLIENT CODE ###########
const http = require('http');
const WebSocket = require('ws');

// A wrapper function to do a http request and return the data
function request(url, callback) {
    http.get(url, function (response) {
        let body = '';
        response.on('data', function (data) {
            body += data;
        });
        response.on('end', function () {
            callback(body);
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

    // A wrapper function for requesting a balance
    function requestBalance(account, pin, callback) {
        const to_account_parts = parseAccountParts(account);

        requestMessage('balance', {
            header: {
                originCountry: COUNTRY_CODE,
                originBank: BANK_CODE,
                receiveCountry: to_account_parts.country,
                receiveBank: to_account_parts.bank
            },
            body: {
                account: account,
                pin: pin
            }
        }, callback);
    }

    // A wrapper function for requesting a payment
    function requestPayment(from_account, to_account, pin, amount, callback) {
        const form_account_parts = parseAccountParts(from_account);
        const to_account_parts = parseAccountParts(to_account);

        if (form_account_parts.bank !== BANK_CODE) {
            requestMessage('payment', {
                header: {
                    originCountry: COUNTRY_CODE,
                    originBank: BANK_CODE,
                    receiveCountry: form_account_parts.country,
                    receiveBank: form_account_parts.bank
                },
                body: {
                    from_account: from_account,
                    to_account: to_account,
                    pin: pin,
                    amount: amount
                }
            }, callback);
        }

        if (to_account_parts.bank !== BANK_CODE) {
            requestMessage('payment', {
                header: {
                    originCountry: COUNTRY_CODE,
                    originBank: BANK_CODE,
                    receiveCountry: to_account_parts.country,
                    receiveBank: to_account_parts.bank
                },
                body: {
                    from_account: from_account,
                    to_account: to_account,
                    pin: pin,
                    amount: amount
                }
            }, callback);
        }
    }

    // When connected to gosbank
    ws.on('open', function () {
        // Try to register
        requestMessage('register', {
            header: {
                originCountry: COUNTRY_CODE,
                originBank: BANK_CODE,
                receiveCountry: 'SU',
                receiveBank: 'GOSB'
            },
            body: {}
        }, function (data) {
            if (data.body.code == 200) {
                console.log('Connected with Gosbank with bank code: ' + BANK_CODE);

                // Create HTTP API for the Banq website
            }
            else {
                console.log('Error with connecting to Gosbank, reason: ' + data.body.code);
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

        if (type === 'balance') {
            console.log('Balance request for: ' + data.body.account);

            // Fetch balance info from database via API

            setTimeout(function () {
                responseMessage(id, 'balance', {
                    header: {
                        originCountry: COUNTRY_CODE,
                        originBank: BANK_CODE,
                        receiveCountry: data.header.originCountry,
                        receiveBank: data.header.originBank
                    },
                    body: {
                        code: 200,
                        balance: parseFloat((Math.random() * 10000).toFixed(2))
                    }
                });
            }, Math.random() * 2000 + 500);
        }

        if (type === 'payment') {
            console.log('Payment request for: ' + data.body.to_account);

            // Add payment to database via API

            setTimeout(function () {
                responseMessage(id, 'payment', {
                    header: {
                        originCountry: COUNTRY_CODE,
                        originBank: BANK_CODE,
                        receiveCountry: data.header.originCountry,
                        receiveBank: data.header.originBank
                    },
                    body: {
                        code: 200
                    }
                });
            }, Math.random() * 2000 + 500);
        }
    });

    // When closed try to reconnected
    ws.on('close', function () {
        console.log('Disconnected, try to reconnect in ' + (RECONNECT_TIMEOUT / 1000).toFixed(0) + ' seconds!');
        setTimeout(connectToGosbank, RECONNECT_TIMEOUT);
    });

    // Ingnore connecting errors because reconnect in the close handler
    ws.on('error', function (error) {});
}

// Try to connect to gosbank
connectToGosbank();
