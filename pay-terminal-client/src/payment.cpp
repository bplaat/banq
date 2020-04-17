#include "payment.hpp"

#include <Wire.h>
#include <LiquidCrystal.h>

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
    lcd.print("Amount: P");
    
    int length = payment_amount.length();
    
    // start with a zero if there are only decimals
    if(length - 2 <= 0) {
        lcd.print('0');
    
    // if there are non-decimals, print them
    } else {
        for(int i = 0; i < length - 2; i++) {
            lcd.print(payment_amount[i]);
        }
    }
    
    lcd.print('.');
    
    // print zeroes to pad decimals (if there are less than 2 decimals)
    for(int i = 0; i < max(0, 2 - length); i++) {
        lcd.print('0');
    }
    
    // print decimals
    for(int i = max(0, length - 2); i < length; i++) {
        lcd.print(payment_amount[i]);
    }
    
}

State input_payment_amount() {

    // remove the last character in the string on backspace
    if(communication_data.key_pressed(KEY_BACKSPACE) && payment_amount.length() != 0) {
        payment_amount.remove(payment_amount.length() - 1, 1);

    // on enter, the new state will be user_scan_card but only if the payment amount is not empty
    } else if(communication_data.key_pressed(KEY_ENTER) && payment_amount.compareTo("") != 0) {
        return user_scan_card;

    // else check if the last key is a digit, append it to the string if it's not full
    } else if(communication_data.new_key && isdigit(communication_data.key) &&
              payment_amount.length() < PAYMENT_AMOUNT_LENGTH)
    {
        payment_amount += communication_data.key;
    }

    // if there are no special circumstances, remain in current state
    return user_input_payment_amount;
}

State scan_card_info() {

    // if a new card is found, read the data and go to the pin code state
    if(communication_data.new_card) {
        for(int i = 0; i < 16; i++) {
            card_info += (char)communication_data.block_data[i];
        }
        for(int i = 0; i < 8; i++) {
            card_id += (char)communication_data.uid[i];
        }

        return print_pin_code_info;
    }

    // if there are no special circumstances, remain in current state
    return user_scan_card;
}

State input_pin_code() {

    // remove the last character in the string on backspace
    if(communication_data.key_pressed(KEY_BACKSPACE) && pin_code.length() != 0) {
        pin_code.remove(pin_code.length() - 1, 1);

    // on enter, call api (but only if the pin code is complete)
    } else if(communication_data.key_pressed(KEY_ENTER) && pin_code.length() == PIN_CODE_LENGTH) {
        return calling_api;

    // else check if the last key is a digit, append it to the string if it's not full
    } else if(communication_data.new_key && isdigit(communication_data.key) &&
              pin_code.length() < PIN_CODE_LENGTH)
    {
        pin_code += communication_data.key;
    }

    // if there are no special circumstances, remain in current state
    return user_input_pin_code;
}

// call api, dummy function for now
bool call_api() {
    lcd.clear();
    lcd.setCursor(0, 0);
    lcd_print_payment_amount();
    lcd.setCursor(0, 1);

    Serial.println("API call should be made");
    Serial.println("API key: " + api_key);
    Serial.println("Rfid: " + card_id);
    Serial.println("Pin code: " + pin_code);
    Serial.println("Name: " + (String)"Payment terminal transaction");
    Serial.println("From account id: " + card_info);
    Serial.println("To account id: " + account_id);
    Serial.println("Amount: " + payment_amount + " ruble cents or whatever they're called");

    return !pin_code.compareTo("1235");
}

void do_transaction() {
    static State state = begin;
    request_data();

    static unsigned long post_transaction_reset_time = 0;
    static bool payment_success = true;

    // always reset on cancel
    if(communication_data.key_pressed(KEY_CANCEL)) {
        state = begin;
    }
    
    switch(state) {

        case begin:

            // reset data
            payment_amount = "";
            card_info = "";
            card_id = "";
            pin_code = "";

            // print info and immediately go to next state
            lcd.clear();
            lcd.print("Please enter the");
            lcd.setCursor(0, 1);
            lcd.print("payment amount");
            state = user_input_payment_amount;
            break;

        case user_input_payment_amount:
            state = input_payment_amount();

            // print the amount when the string is not empty
            if(payment_amount.compareTo("") != 0) {
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

            if(pin_code.compareTo("") != 0) {

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
            payment_success = call_api();
            lcd.print(payment_success ? "Success" : "Failure");

            state = transaction_done_blocking;
            post_transaction_reset_time = millis();
            break;

        case transaction_done_blocking:

            // do nothing for BLOCKING_RESET_TIME ms
            if(millis() >= post_transaction_reset_time + BLOCKING_RESET_TIME) {
                state = transaction_done;
            }
            break;

        case transaction_done:

            // timeout after RESET_TIME ms, always goes back to start
            if(millis() >= post_transaction_reset_time + RESET_TIME) {
                state = begin;
            }

            // also reset if a key is pressed
            if(communication_data.new_key) {

                // go back to the beginning if the last payment was successful
                if(payment_success) {
                    state = begin;

                // go back to the card scan state if the last payment was not successful
                } else {
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

    if(Wire.available()) {

        // replace old communicationdata with new data
        for(unsigned i = 0; i < sizeof(CommunicationData); i++) {
            *((char*)&communication_data + i) = Wire.read();
        }

    } else {
        return false;
    }

    return true;
}