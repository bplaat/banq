// The serial I/O Controller
// -> Keypad input
// -> RFID read
// <- Beeper
// <- RFID write
// <- Printer commands
// <- Money dispencer commands

#include <Arduino.h>
#include <Keypad.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ArduinoJson.h>
#include <Adafruit_Thermal.h>
#include "printer_logo.h"

#define KEYPAD_ROWS 4
#define KEYPAD_COLUMNS 4

char keypad_keys[KEYPAD_ROWS][KEYPAD_COLUMNS]= {
    { '1', '2', '3', 'A' },
    { '4', '5', '6', 'B' },
    { '7', '8', '9', 'C' },
    { '*', '0', '#', 'D' }
};

uint8_t keypad_row_pins[KEYPAD_ROWS] = { 22, 23, 24, 25 };
uint8_t keypad_column_pins[KEYPAD_COLUMNS] = { 26, 27, 28, 29 };

Keypad keypad = Keypad(makeKeymap(keypad_keys), keypad_row_pins, keypad_column_pins, KEYPAD_ROWS, KEYPAD_COLUMNS);

#define BEEPER_PIN 2

#define SS_PIN 53
#define RST_PIN 49

MFRC522 mfrc522(SS_PIN, RST_PIN);
MFRC522::MIFARE_Key mfrc522_keyA;

#define ACCOUNT_ID_DATA_BLOCK 1
#define ACCOUNT_ID_TRAILER_BLOCK 3
#define ACOUNT_ID_LENGTH 16

Adafruit_Thermal printer(&Serial1);

char json_buffer[512];
StaticJsonDocument<512> document;

void setup() {
    Serial.begin(9600);
    Serial.setTimeout(50);

    Serial1.begin(9600);
    printer.begin();
    printer.sleep();

    SPI.begin();
    mfrc522.PCD_Init();

    for (byte i = 0; i < 6; i++) {
        mfrc522_keyA.keyByte[i] = 0xff;
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

        if (document["type"] == "printer") {
            printer.wake();
            printer.printBitmap(256, 64, printer_logo);
            JsonArray lines = document["lines"];
            for (uint8_t i = 0; i < lines.size(); i++) {
                printer.println((char *)lines[i]);
            }
            printer.sleep();
        }

        if (document["type"] == "rfid_write") {
            Serial.println("[INFO] Waiting for card...");
            while (!(mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()));

            String rfid_uid = "";
            for (byte i = 0; i < mfrc522.uid.size; i++) {
                rfid_uid += (mfrc522.uid.uidByte[i] < 0x10 ? "0" : "") + String(mfrc522.uid.uidByte[i], HEX);
            }

            String account_id = document["account_id"];
            if (account_id.length() == ACOUNT_ID_LENGTH) {
                MFRC522::StatusCode status = mfrc522.PCD_Authenticate(MFRC522::PICC_CMD_MF_AUTH_KEY_A, ACCOUNT_ID_TRAILER_BLOCK, &mfrc522_keyA, &(mfrc522.uid));
                if (status == MFRC522::STATUS_OK) {
                    uint8_t write_account_id[ACOUNT_ID_LENGTH] = { 0 };
                    for (int i = 0; i < ACOUNT_ID_LENGTH; i++) {
                        write_account_id[i] = account_id.charAt(i);
                    }

                    status = mfrc522.MIFARE_Write(ACCOUNT_ID_DATA_BLOCK, write_account_id, ACOUNT_ID_LENGTH);
                    if (status == MFRC522::STATUS_OK) {
                        uint8_t read_account_id[18] = { 0 };
                        uint8_t size = sizeof(read_account_id);

                        status = mfrc522.MIFARE_Read(ACCOUNT_ID_DATA_BLOCK, read_account_id, &size);
                        if (status == MFRC522::STATUS_OK) {
                            bool same = true;
                            for (uint8_t i = 0; i < ACOUNT_ID_LENGTH; i++) {
                                if (read_account_id[i] != write_account_id[i]) {
                                    same = false;
                                    break;
                                }
                            }

                            if (same) {
                                document.clear();
                                document["type"] = "rfid_write";
                                document["success"] = true;
                                document["rfid_uid"] = rfid_uid;
                                document["account_id"] = account_id;
                                serializeJson(document, json_buffer);
                                Serial.println(json_buffer);
                            } else {
                                Serial.println("[ERROR] The RFID write is not the same");
                            }
                        } else {
                            Serial.print("[ERROR] MIFARE_Read() failed: ");
                            Serial.println(mfrc522.GetStatusCodeName(status));
                        }
                    } else {
                        Serial.print("[ERROR] MIFARE_Write() failed: ");
                        Serial.println(mfrc522.GetStatusCodeName(status));
                    }
                } else {
                    Serial.print("[ERROR] PCD_Authenticate() failed: ");
                    Serial.println(mfrc522.GetStatusCodeName(status));
                }
            } else {
                Serial.println("[ERROR] The account id is not 16 characters long");
            }

            mfrc522.PICC_HaltA();
            mfrc522.PCD_StopCrypto1();
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

    if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
        String rfid_uid = "";
        for (byte i = 0; i < mfrc522.uid.size; i++) {
           rfid_uid += (mfrc522.uid.uidByte[i] < 0x10 ? "0" : "") + String(mfrc522.uid.uidByte[i], HEX);
        }

        MFRC522::StatusCode status = mfrc522.PCD_Authenticate(MFRC522::PICC_CMD_MF_AUTH_KEY_A, ACCOUNT_ID_TRAILER_BLOCK, &mfrc522_keyA, &(mfrc522.uid));
        if (status == MFRC522::STATUS_OK) {
            uint8_t account_id[18] = { 0 };
            uint8_t size = sizeof(account_id);
            status = mfrc522.MIFARE_Read(ACCOUNT_ID_DATA_BLOCK, account_id, &size);
            if (status == MFRC522::STATUS_OK) {
                account_id[ACOUNT_ID_LENGTH] = 0;
                document.clear();
                document["type"] = "rfid_read";
                document["rfid_uid"] = rfid_uid;
                document["account_id"] = account_id;
                serializeJson(document, json_buffer);
                Serial.println(json_buffer);
            } else {
                Serial.print("[ERROR] MIFARE_Read() failed: ");
                Serial.println(mfrc522.GetStatusCodeName(status));
            }
        } else {
            Serial.print("[ERROR] PCD_Authenticate() failed: ");
            Serial.println(mfrc522.GetStatusCodeName(status));
        }

        mfrc522.PICC_HaltA();
        mfrc522.PCD_StopCrypto1();
    }
}
