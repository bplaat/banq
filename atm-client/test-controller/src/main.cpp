// The serial I/O test Controller for an Arduino Uno

// Load all the libraries
#include <Arduino.h>
#include <Keypad.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ArduinoJson.h>

// Keypad
#define KEYPAD_ROWS 4
#define KEYPAD_COLUMNS 4

char keypad_keys[KEYPAD_ROWS][KEYPAD_COLUMNS]= {
    { '1', '2', '3', 'A' },
    { '4', '5', '6', 'B' },
    { '7', '8', '9', 'C' },
    { '*', '0', '#', 'D' }
};

uint8_t keypad_row_pins[KEYPAD_ROWS] = { 3, 4, 5, 6 };
uint8_t keypad_column_pins[KEYPAD_COLUMNS] = { 7, 8, 9, 10 };

Keypad keypad = Keypad(makeKeymap(keypad_keys), keypad_row_pins, keypad_column_pins, KEYPAD_ROWS, KEYPAD_COLUMNS);

// Beeper
#define BEEPER_PIN 2

// RFID
#define SS_PIN A0
#define RST_PIN A1

MFRC522 mfrc522(SS_PIN, RST_PIN);
MFRC522::MIFARE_Key mfrc522_keyA;

#define ACCOUNT_ID_DATA_BLOCK 1
#define ACCOUNT_ID_TRAILER_BLOCK 3
#define ACOUNT_ID_LENGTH 16

// JSON
StaticJsonDocument<512> document;

// Setup
void setup() {
    // Init computer serial com
    Serial.begin(9600);
    Serial.setTimeout(50);

    // Init RFID
    SPI.begin();
    mfrc522.PCD_Init();
    for (uint8_t i = 0; i < 6; i++) {
        mfrc522_keyA.keyByte[i] = 0xff;
    }
}

// Loop
void loop() {
    // Check if the computer send a message
    if (Serial.available() > 0) {
        // Parse the JSON message
        deserializeJson(document, Serial);

        // Beeper command
        if (document["type"] == "beeper") {
            tone(BEEPER_PIN, document["frequency"], document["duration"]);
        }

        // Money command
        if (document["type"] == "money") {
            // Do nothing
            delay(1000);

            // Send money done message
            document.clear();
            document["type"] = "money";
            serializeJson(document, Serial);
            Serial.println();
        }

        // Printer command
        if (document["type"] == "printer") {
            // Do nothing
            delay(1000);

            // Send printer done message
            document.clear();
            document["type"] = "printer";
            serializeJson(document, Serial);
            Serial.println();
        }

        // RFID write command
        if (document["type"] == "rfid_write") {
            Serial.println(F("[INFO] Waiting for card..."));
            while (!(mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()));

            String rfid_uid = "";
            for (uint8_t i = 0; i < mfrc522.uid.size; i++) {
                rfid_uid += (mfrc522.uid.uidByte[i] < 0x10 ? "0" : "") + String(mfrc522.uid.uidByte[i], HEX);
            }

            String account_id = document["account_id"];
            if (account_id.length() == ACOUNT_ID_LENGTH) {
                MFRC522::StatusCode status = mfrc522.PCD_Authenticate(MFRC522::PICC_CMD_MF_AUTH_KEY_A, ACCOUNT_ID_TRAILER_BLOCK, &mfrc522_keyA, &(mfrc522.uid));
                if (status == MFRC522::STATUS_OK) {
                    uint8_t write_account_id[ACOUNT_ID_LENGTH] = { 0 };
                    for (uint8_t i = 0; i < ACOUNT_ID_LENGTH; i++) {
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
                                serializeJson(document, Serial);
                                Serial.println();
                            } else {
                                Serial.println(F("[ERROR] The RFID write is not the same"));
                            }
                        } else {
                            Serial.print(F("[ERROR] MIFARE_Read() failed: "));
                            Serial.println(mfrc522.GetStatusCodeName(status));
                        }
                    } else {
                        Serial.print(F("[ERROR] MIFARE_Write() failed: "));
                        Serial.println(mfrc522.GetStatusCodeName(status));
                    }
                } else {
                    Serial.print(F("[ERROR] PCD_Authenticate() failed: "));
                    Serial.println(mfrc522.GetStatusCodeName(status));
                }
            } else {
                Serial.println(F("[ERROR] The account id is not 16 characters long"));
            }

            mfrc522.PICC_HaltA();
            mfrc522.PCD_StopCrypto1();
        }
    }

    // Read the keypad and if a key is pressed send a message
    char key = keypad.getKey();
    if (key != NO_KEY) {
        document.clear();
        document["type"] = "keypad";
        document["key"] = String(key);
        serializeJson(document, Serial);
        Serial.println();
    }

    // Check for new RFID card read and if so send a message
    if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {
        String rfid_uid = "";
        for (uint8_t i = 0; i < mfrc522.uid.size; i++) {
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
                serializeJson(document, Serial);
                Serial.println();
            } else {
                Serial.print(F("[ERROR] MIFARE_Read() failed: "));
                Serial.println(mfrc522.GetStatusCodeName(status));
            }
        } else {
            Serial.print(F("[ERROR] PCD_Authenticate() failed: "));
            Serial.println(mfrc522.GetStatusCodeName(status));
        }

        mfrc522.PICC_HaltA();
        mfrc522.PCD_StopCrypto1();
    }
}
