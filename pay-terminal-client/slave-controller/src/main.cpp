#include <Arduino.h>

#include <Keypad.h>

#include <SPI.h>
#include <MFRC522.h>

#include <Wire.h>

// function prototypes
void getUid(const MFRC522& reader, char *buffer);
void rfidEndRead(MFRC522& reader);
bool readBlock(MFRC522& reader, MFRC522::MIFARE_Key& key, byte block, byte *buffer, byte bufferSize);
void sendData();

// constants
const char keypadLayout[4][4] = {
    {'1', '4', '7', '*'},
    {'2', '5', '8', '0'},
    {'3', '6', '9', '#'},
    {'A', 'B', 'C', 'D'}
};

const byte keypadRowPins[] = {5, 4, 3, 2};
const byte keypadColPins[] = {A0, 8, 7, 6};

// THIS STRUCT MUST BE THE EXACTLY THE SAME ON BOTH THE NANO AND ESP
struct CommunicationData {
    unsigned char blockData[18];
    char uid[8], key;
    bool newCard, newKey;

    void clear() {
        memset(uid, 0, 8);
        memset(blockData, 0, 18);
        key = 0;
        newCard = false;
        newKey = false;
    }
};

volatile CommunicationData communicationData;

// objects
Keypad keypad = Keypad( makeKeymap(keypadLayout),
                        const_cast<byte*>(keypadRowPins),
                        const_cast<byte*>(keypadColPins), 4, 4 );

MFRC522 reader(10, 9);
MFRC522::MIFARE_Key key;

void setup() {
    communicationData.clear();

    Serial.begin(9600);
    SPI.begin();
    Wire.begin(1);
    Wire.onRequest(sendData);

    reader.PCD_Init();

    memset(key.keyByte, 0xFF, 6);
}

void loop() {

    char k = keypad.getKey();
    if(k != 0) {
        Serial.println("input: " + (String)k);
        noInterrupts();
        communicationData.key = k;
        communicationData.newKey = true;
        interrupts();
    }

	// check for present cards
	if(reader.PICC_IsNewCardPresent()) {

		// check if the read was successful
		if(reader.PICC_ReadCardSerial()) {
            noInterrupts();
            getUid(reader, communicationData.uid);
			Serial.print("Found card with UID ");
            Serial.write((char*)communicationData.uid, 8);

            // and check if the block read was successful
            if(readBlock(reader, key, 1, communicationData.blockData, 18)) {

                // if everything is good, tell the esp that there is a new card
                communicationData.newCard = true;
            }

            interrupts();
            Serial.println("");
		}
	}
}

// function to get the uid from a MFRC522
// this function assumes that a card has been successfully read
// the result will be stored in buffer which should be at least 8 bytes long
void getUid(const MFRC522& reader, char *buffer) {
    String uid;
    uid.reserve(reader.uid.size);

	for(byte i = 0; i < reader.uid.size; i++) {

		// append 0 if the byte's value is lower than 0x10
		// so there are always two digits per byte
		if(reader.uid.uidByte[i] < 0x10) uid += '0';

		// append current byte and space
		uid += String(reader.uid.uidByte[i], HEX);
	}

    memcpy(buffer, uid.c_str(), 8);
}

void rfidEndRead(MFRC522& reader) {
  reader.PICC_HaltA(); // Halt PICC
  reader.PCD_StopCrypto1(); // Stop encryption on PCD
}

bool readBlock(MFRC522& reader, MFRC522::MIFARE_Key& key, byte block, byte *buffer, byte bufferSize) {

  // trailer block for block to be read
  int trailerBlock = (block / 4 * 4) + 3;

  // authentication
  auto err = reader.PCD_Authenticate(MFRC522::PICC_CMD_MF_AUTH_KEY_A, trailerBlock, &key, &reader.uid);
  if(err != MFRC522::STATUS_OK) {
    Serial.println("Authentication failed: " + (String)reader.GetStatusCodeName(err));
    rfidEndRead(reader);
    return false;
  }

  // read block
  err = reader.MIFARE_Read(block, buffer, &bufferSize);
  if(err != MFRC522::STATUS_OK) {
    Serial.println("Read failed: " + (String)reader.GetStatusCodeName(err));
    rfidEndRead(reader);
    return false;
  }

  rfidEndRead(reader);

  return true;
}

void sendData() {
    Serial.println("sending data");
    Wire.write((char*)&communicationData, sizeof(CommunicationData));
    communicationData.clear();
}