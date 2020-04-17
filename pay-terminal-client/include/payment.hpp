#pragma once

#include <Arduino.h>
#include <LiquidCrystal.h>

// keypad keybinds
#define KEY_BACKSPACE '#'
#define KEY_ENTER 'D'
#define KEY_CANCEL '*'

// max length
#define PIN_CODE_LENGTH 4
#define PAYMENT_AMOUNT_LENGTH 6 // length of 'Amount: Pxxxx.xx' = 16 so 6 digits

// how long to wait before resetting after transaction
#define RESET_TIME 10000
#define BLOCKING_RESET_TIME 500

#define NANO_ADDR 1

enum State {
    begin,
    user_input_payment_amount,
    user_scan_card,
    print_pin_code_info,
    user_input_pin_code,
    calling_api,
    transaction_done_blocking,
    transaction_done
};

struct CommunicationData {
    unsigned char block_data[18];
    char uid[8], key;
    bool new_card, new_key;

    void clear();
    bool key_pressed(char key);
};

void lcd_print_payment_amount();

State input_payment_amount();

State scan_card_info();

State input_pin_code();

bool call_api();

void do_transaction();

bool request_data();

extern CommunicationData communication_data;
extern LiquidCrystal lcd;
extern String payment_amount;
extern String card_info;
extern String card_id;
extern String pin_code;