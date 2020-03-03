// A simple Arduino program that listens for keypad presses and writes them to the serial out
#include <Keypad.h>

#define KEYPAD_ROWS 4
#define KEYPAD_COLUMNS 4

char keypad_keys[KEYPAD_ROWS][KEYPAD_COLUMNS]= {
    { '1', '2', '3', 'A' },
    { '4', '5', '6', 'B' },
    { '7', '8', '9', 'C' },
    { '*', '0', '#', 'D' }
};

uint8_t keypad_row_pins[KEYPAD_ROWS] = { 9, 8, 7, 6 };
uint8_t keypad_column_pins[KEYPAD_COLUMNS] = { 5, 4, 3, 2 };

Keypad keypad = Keypad(makeKeymap(keypad_keys), keypad_row_pins, keypad_column_pins, KEYPAD_ROWS, KEYPAD_COLUMNS);

void setup() {
    Serial.begin(9600);
    Serial.print("Connected");
}

void loop() {
    char key = keypad.getKey();
    if (key != NO_KEY) {
        Serial.print(key);
    }
}
