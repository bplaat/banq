// The serial I/O Controller
// -> Keypad input
// -> RFID read
// <- Beeper
// <- RFID write
// <- Printer commands
// <- Money dispencer commands

// https://lastminuteengineers.com/how-rfid-works-rc522-arduino-tutorial/

#include <Arduino.h>
#include <Keypad.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ArduinoJson.h>

#define KEYPAD_ROWS 4
#define KEYPAD_COLUMNS 4

char keypad_keys[KEYPAD_ROWS][KEYPAD_COLUMNS]= {
    { '1', '2', '3', 'A' },
    { '4', '5', '6', 'B' },
    { '7', '8', '9', 'C' },
    { '*', '0', '#', 'D' }
};

uint8_t keypad_row_pins[KEYPAD_ROWS] = { A1, 8, 7, 6 };
uint8_t keypad_column_pins[KEYPAD_COLUMNS] = { 5, 4, 3, 2 };

Keypad keypad = Keypad(makeKeymap(keypad_keys), keypad_row_pins, keypad_column_pins, KEYPAD_ROWS, KEYPAD_COLUMNS);

#define BEEPER_PIN 9

#define SS_PIN 10
#define RST_PIN A0

MFRC522 rfid(SS_PIN, RST_PIN);
MFRC522::MIFARE_Key key;

char json_buffer[64];
StaticJsonDocument<64> document;

void setup() {
    Serial.begin(9600);
    Serial.setTimeout(50);

    SPI.begin();
    rfid.PCD_Init();
    delay(4);

    for (byte i = 0; i < 6; i++) {
        key.keyByte[i] = 0xff;
    }
}

void loop() {
    if (Serial.available() > 0) {
        document.clear();
        String line = Serial.readString();
        deserializeJson(document, line);

        if (document["type"] == "beeper") {
            tone(BEEPER_PIN, document["frequency"], document["duration"]);
        }
    }

    char key = keypad.getKey();
    if (key != NO_KEY) {
        document.clear();
        document["type"] = "keypad";
        document["key"] = String(key);
        serializeJson(document, json_buffer);
        Serial.println(json_buffer);
    }

    if (rfid.PICC_IsNewCardPresent() && rfid.PICC_ReadCardSerial()) {
        String rfid_uid = "";
        for (byte i = 0; i < rfid.uid.size; i++) {
           rfid_uid += (rfid.uid.uidByte[i] < 0x10 ? "0" : "") + String(rfid.uid.uidByte[i], HEX);
        }
        rfid.PICC_HaltA();

        document.clear();
        document["type"] = "rfid_read";
        document["rfid_uid"] = rfid_uid;
        serializeJson(document, json_buffer);
        Serial.println(json_buffer);
    }
}
