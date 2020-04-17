#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <EEPROM.h>
#include <ArduinoJson.h>
#include <Wire.h>

#include "config.hpp"
#include "storage.hpp"
#include "wifi.hpp"
#include "payment.hpp"

/*  Problems
    - Any account ID can be used in the settings page,
        there is no check to see if it's correct
    - include/website.hpp should start #pragma once but idk where its generated
    - current API key is not displayed on settings page

*/

// The web server object
ESP8266WebServer webserver(80);

// The start of our program
void setup() {

    communication_data.clear();
    Wire.begin();
    lcd.begin(16, 2);

    // Init the serial output
    Serial.begin(9600);

    // Setup the EEPROM
    EEPROM.begin(512);

    // Load the config from EEPROM
    load_config();

    // Init the local wifi
    local_wifi_init();

    // Connect to wifi with ssid and password when not empty
    if (wifi_ssid != "" && wifi_password != "") {
        wifi_connect();
    }

    // Init the webserver
    webserver_init(webserver);
}

// The program loop
void loop() {
    // Handle any http clients
    webserver.handleClient();

    do_transaction();
}
