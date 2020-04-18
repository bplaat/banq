#include "payment.hpp"

#include <Wire.h>
#include <LiquidCrystal.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClientSecure.h>
#include <ArduinoJson.h>

#include "config.hpp"

CommunicationData communication_data;
LiquidCrystal lcd(D0, D7, D6, D5, D4, D3);
String payment_amount;
String card_info;
String card_id;
String pin_code;

void CommunicationData::clear() {
    memset(uid, 0, 8);
    memset(block_data, 0, 18);
    key = '\0';
    new_card = false;
    new_key = false;
}

bool CommunicationData::key_pressed(char key) {
    return (new_key && this->key == key);
}

void lcd_print_payment_amount() {

    int printed_chars = 0;

    lcd.print("Amount: P");
    printed_chars += 9;
    
    int length = payment_amount.length();
    
    // start with a zero if there are only decimals
    if (length - 2 <= 0) {
        lcd.print('0');
        printed_chars++;
    }

    // if there are non-decimals, print them
    else {
        for (int i = 0; i < length - 2; i++) {
            lcd.print(payment_amount[i]);
            printed_chars++;
        }
    }
    
    lcd.print('.');
    printed_chars++;
    
    // print zeroes to pad decimals (if there are less than 2 decimals)
    for (int i = 0; i < max(0, 2 - length); i++) {
        lcd.print('0');
        printed_chars++;
    }
    
    // print decimals
    for (int i = max(0, length - 2); i < length; i++) {
        lcd.print(payment_amount[i]);
        printed_chars++;
    }

    // padding
    for (int i = printed_chars; i < 16; i++) {
        lcd.print(' ');
    }
    
}

State input_payment_amount() {

    // remove the last character in the string on backspace
    if (communication_data.key_pressed(KEY_BACKSPACE) && payment_amount.length() != 0) {
        payment_amount.remove(payment_amount.length() - 1, 1);
    }

    // on enter, the new state will be user_scan_card but only if the payment amount is not empty
    else if (communication_data.key_pressed(KEY_ENTER) && payment_amount != "") {
        return user_scan_card;
    }
    
    // else check if the last key is a digit, append it to the string if it's not full
    else if (communication_data.new_key && isdigit(communication_data.key) &&
             payment_amount.length() < PAYMENT_AMOUNT_LENGTH)
    {
        payment_amount += communication_data.key;
    }

    // if there are no special circumstances, remain in current state
    return user_input_payment_amount;
}

State scan_card_info() {

    // if a new card is found, read the data and go to the pin code state
    if (communication_data.new_card) {
        for (int i = 0; i < 16; i++) {
            card_info += (char)communication_data.block_data[i];
        }
        for (int i = 0; i < 8; i++) {
            card_id += (char)communication_data.uid[i];
        }

        return print_pin_code_info;
    }

    // if there are no special circumstances, remain in current state
    return user_scan_card;
}

State input_pin_code() {

    // remove the last character in the string on backspace
    if (communication_data.key_pressed(KEY_BACKSPACE) && pin_code.length() != 0) {
        pin_code.remove(pin_code.length() - 1, 1);
    }

    // on enter, call api (but only if the pin code is complete)
    else if (communication_data.key_pressed(KEY_ENTER) && pin_code.length() == PIN_CODE_LENGTH) {
        return calling_api;
    }

    // else check if the last key is a digit, append it to the string if it's not full
    else if (communication_data.new_key && isdigit(communication_data.key) &&
             pin_code.length() < PIN_CODE_LENGTH)
    {
        pin_code += communication_data.key;
    }

    // if there are no special circumstances, remain in current state
    return user_input_pin_code;
}

// call api
ApiResponse call_api() {

    // create new string with separator (original string should be kept in case
    // of payment failure)
    String payment_amount_with_separator = "";
    int length = payment_amount.length(); // easier to read + signed for max()

    // add non-decimals
    for (int i = 0; i < length - 2; i++) {
        payment_amount_with_separator += payment_amount[i];
    }

    payment_amount_with_separator += '.';

    // add decimals
    for (int i = max(0, length - 2); i < length; i++) {
        payment_amount_with_separator += payment_amount[i];
    }

    // wifi client with fingerprint
    WiFiClientSecure wifi_client;
    wifi_client.setFingerprint(fingerprint.c_str());

    // http client with api url
    HTTPClient http;
    http.begin(
        wifi_client,
        "https://banq.ml/api/atm/transactions/create?key=" + api_key +
        "&name=Payment%20Terminal%20Transaction" +
        "&from_account_id=" + card_info +
        "&to_account_id=" + account_id +
        "&rfid=" + card_id +
        "&pincode=" + pin_code +
        "&amount=" + payment_amount_with_separator
    );

    // get json from server
    String json_raw;
    int http_code = http.GET();
    Serial.println(http_code);
    if (http_code == HTTP_CODE_OK) {
        Serial.print("HTTP request response: ");
        json_raw = http.getString();
        Serial.println(json_raw);
    }
    
    // return error on failure
    else {
        Serial.print("HTTP request failed, error: ");
        Serial.println(http.errorToString(http_code));
        return error;
    }

    http.end();

    // deserialize json
    StaticJsonDocument<JSON_BUFFER_SIZE> document;
    

    DeserializationError err = deserializeJson(document, json_raw);

    // return error on failure
    if (err != DeserializationError::Ok) {
        Serial.print("json error: ");
        Serial.println(err.c_str());
        return ApiResponse::error;
    }

    // extract success and blocked booleans
    bool success = document["success"];
    bool blocked = document["blocked"];

    lcd.clear();
    lcd.setCursor(0, 0);
    lcd_print_payment_amount();
    lcd.setCursor(0, 1);

    Serial.println("API call made");
    Serial.println("API key: " + api_key);
    Serial.println("Rfid: " + card_id);
    Serial.println("Pin code: " + pin_code);
    Serial.println("Name: " + (String)"Payment terminal transaction");
    Serial.println("From account id: " + card_info);
    Serial.println("To account id: " + account_id);
    Serial.println("Amount: " + payment_amount + " ruble cents or whatever they're called");

    // return response depending on success and blocked
    if (success) {
        return ApiResponse::success;
    }

    if (blocked) {
        return ApiResponse::card_blocked;
    }

    return ApiResponse::wrong_pin_code;
}

void do_transaction() {
    static State state = begin;
    request_data();

    static unsigned long post_transaction_reset_time = 0;
    static ApiResponse api_response = success;

    static bool show_payment_amount = false, show_pin_code = false;

    // always reset on cancel
    if (communication_data.key_pressed(KEY_CANCEL)) {
        state = begin;
    }
    
    switch (state) {

        case begin:

            // reset data
            payment_amount = "";
            card_info = "";
            card_id = "";
            pin_code = "";
            show_payment_amount = false;
            show_pin_code = false;

            // print info and immediately go to next state
            lcd.clear();
            lcd.print("Please enter the");
            lcd.setCursor(0, 1);
            lcd.print("payment amount");
            state = user_input_payment_amount;
            break;

        case user_input_payment_amount:
            state = input_payment_amount();

            // print the amount when something happened to the string
            if (payment_amount != "") {
                show_payment_amount = true;
            }

            if (show_payment_amount) {
                lcd.clear();
                lcd.setCursor(0, 0);
                lcd_print_payment_amount();
            }
            break;

        case user_scan_card:
            lcd.setCursor(0, 0);
            lcd_print_payment_amount();
            lcd.setCursor(0, 1);
            lcd.print("Scan your card");
            state = scan_card_info();
            break;

        case print_pin_code_info:

            // print info and immediately go to next state
            lcd.setCursor(0, 0);
            lcd_print_payment_amount();
            lcd.setCursor(0, 1);
            lcd.print("Enter pin code");
            state = user_input_pin_code;
            break;

        case user_input_pin_code:
            state = input_pin_code();

            if (pin_code != "") {
                show_pin_code = true;
            }

            if (show_pin_code) {

                // print payment amount of first line
                lcd.clear();
                lcd.setCursor(0, 0);
                lcd_print_payment_amount();

                // print censored pin code on second line
                lcd.setCursor(0, 1);
                for(unsigned i = 0; i < pin_code.length(); i++) lcd.print("*");
                for(unsigned i = pin_code.length(); i < PIN_CODE_LENGTH; i++) lcd.print("-");
            }
            break;
        
        case calling_api:

            // call api and print result on lcd
            api_response = call_api();

            if (api_response == ApiResponse::success) {
                lcd.setCursor(0, 1);
                lcd.print("Success         ");
            } else {
                lcd.setCursor(0, 0);
                if (api_response == wrong_pin_code) {
                    lcd.print("Wrong pin code  ");
                } else if (api_response == card_blocked) {
                    lcd.print("Card blocked    ");
                } else {
                    lcd.print("API Error       ");
                }

                lcd.setCursor(0, 1);
                lcd.print("Press any key   ");
            }

            state = transaction_done_blocking;
            post_transaction_reset_time = millis();
            break;
            
        case transaction_done_blocking:

            // do nothing for BLOCKING_RESET_TIME ms
            if (millis() >= post_transaction_reset_time + BLOCKING_RESET_TIME) {
                state = transaction_done;
            }
            break;

        case transaction_done:

            // timeout after RESET_TIME ms, always goes back to start
            if (millis() >= post_transaction_reset_time + RESET_TIME) {
                state = begin;
            }

            // also reset if a key is pressed
            if (communication_data.new_key) {

                // go back to the beginning if the last payment was successful
                if (api_response == success) {
                    state = begin;
                }

                // go back to the card scan state if the last payment was not successful
                // and reset the pin code
                else {
                    pin_code = "";
                    show_pin_code = false;
                    state = user_scan_card;
                }
            }
            break;
    }
}

bool request_data() {

    // request new CommunicationData
    Wire.requestFrom(NANO_ADDR, sizeof(CommunicationData));

    // very short delay to give the nano time to respond
    delay(2);

    if (Wire.available()) {

        // replace old communicationdata with new data
        for (unsigned i = 0; i < sizeof(CommunicationData); i++) {
            *((char*)&communication_data + i) = Wire.read();
        }

    } else {
        return false;
    }

    return true;
}