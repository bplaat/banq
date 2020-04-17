#include "storage.hpp"

#include <EEPROM.h>
#include <ArduinoJson.h>

#include "wifi.hpp"
#include "config.hpp"

// Load config from EEPROM
void load_config() {
    // Read the contents of the EEPROM to a buffer
    char buffer[STRING_BUFFER_SIZE];
    for (int i = 0; i < STRING_BUFFER_SIZE; i++) {
        buffer[i] = EEPROM.read(i);
    }
    Serial.print("\n[EEPROM] Read from EEPROM: ");
    Serial.println(buffer);

    // Try to parse and read the JSON config
    if (buffer[0] == '{') {
        StaticJsonDocument<JSON_BUFFER_SIZE> document;
        DeserializationError error = deserializeJson(document, buffer);
        if (error == DeserializationError::Ok) {
            local_wifi_ssid = String((const char *)document["local_wifi_ssid"]);
            local_wifi_password = String((const char *)document["local_wifi_password"]);
            wifi_ssid = String((const char *)document["wifi_ssid"]);
            wifi_password = String((const char *)document["wifi_password"]);
            account_id = String((const char*)document["account_id"]);
            api_key = String((const char*)document["api_key"]);
        }
    }
}

// Saves the config to the EEPROM
void save_config() {
    char buffer[STRING_BUFFER_SIZE];
    StaticJsonDocument<JSON_BUFFER_SIZE> document;

    // Strinify the default values JSON to the buffer
    document["local_wifi_ssid"] = local_wifi_ssid;
    document["local_wifi_password"] = local_wifi_password;
    document["wifi_ssid"] = wifi_ssid;
    document["wifi_password"] = wifi_password;
    document["account_id"] = account_id;
    document["api_key"] = api_key;
    serializeJson(document, buffer);

    // Write the buffer to the EEPROM
    for (int i = 0; i < STRING_BUFFER_SIZE; i++) {
        EEPROM.write(i, buffer[i]);
    }
    EEPROM.commit();
    Serial.print("[EEPROM] Write to EEPROM: ");
    Serial.println(buffer);
}