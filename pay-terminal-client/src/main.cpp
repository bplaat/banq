#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>
#include <EEPROM.h>
#include <ArduinoJson.h>
#include "config.hpp"
#include "website.hpp"

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
    serializeJson(document, buffer);

    // Write the buffer to the EEPROM
    for (int i = 0; i < STRING_BUFFER_SIZE; i++) {
        EEPROM.write(i, buffer[i]);
    }
    EEPROM.commit();
    Serial.print("[EEPROM] Write to EEPROM: ");
    Serial.println(buffer);
}

// Local wifi network ip addresses
IPAddress local_ip(192,168,1,1);
IPAddress gateway(192,168,1,1);
IPAddress subnet(255,255,255,0);

// A function which inits the local wifi network
// Default IP address = 192.168.1.1
void local_wifi_init() {
    Serial.print("[LOCAL WIFI] Setting up local wifi network configuration ... ");
    Serial.println(WiFi.softAPConfig(local_ip, gateway, subnet) ? "Ready" : "Failed!");

    Serial.print("[LOCAL WIFI] Setting up local wifi network ... ");
    Serial.println(WiFi.softAP(local_wifi_ssid, local_wifi_password) ? "Ready" : "Failed");

    Serial.print("[LOCAL WIFI] Local wifi IP address = ");
    Serial.println(WiFi.softAPIP());
}

// A function which connects to a wifi network
void wifi_connect() {
    Serial.print("[WIFI] Connecting to ");
    Serial.print(wifi_ssid);

    WiFi.begin(wifi_ssid, wifi_password);
    while (WiFi.status() != WL_CONNECTED) {
        Serial.print(".");
        delay(500);
    }

    Serial.print("\n[WIFI] Wifi IP address: ");
    Serial.println(WiFi.localIP());
}

// The web server object
ESP8266WebServer webserver(80);

// A function that inits the webs server
void webserver_init() {
    // Return the web interface when you go to the root path
    webserver.on("/", []() {
        Serial.println("[WEB] /");
        webserver.send(200, "text/html", src_website_html, src_website_html_len);
    });

    // The api read config
    webserver.on("/api/config", []() {
        Serial.print("[WEB] /api/config ");

        // Sterialize the config to a buffer
        char buffer[STRING_BUFFER_SIZE];
        StaticJsonDocument<JSON_BUFFER_SIZE> document;
        document["local_wifi_ssid"] = local_wifi_ssid;
        document["local_wifi_password"] = local_wifi_password;
        document["wifi_ssid"] = wifi_ssid;
        document["wifi_password"] = wifi_password;
        serializeJson(document, buffer);
        Serial.println(buffer);

        // Return the data to the client
        webserver.send(200, "application/json", buffer);
    });

    // The api edit dconfig
    webserver.on("/api/config/edit", []() {
        Serial.println("[WEB] /api/config/edit");

        // Set the config variables when given
        if (webserver.arg("local_wifi_ssid") != "") {
            local_wifi_ssid = webserver.arg("local_wifi_ssid");
        }
        if (webserver.arg("local_wifi_password") != "") {
            local_wifi_password = webserver.arg("local_wifi_password");
        }
        if (webserver.arg("wifi_ssid") != "") {
            wifi_ssid = webserver.arg("wifi_ssid");
        }
        if (webserver.arg("wifi_password") != "") {
            wifi_password = webserver.arg("wifi_password");
        }

        // Return a confirmation message
        webserver.send(200, "application/json", "{\"message\":\"The config has been edited succesfully\"}");

        // Saves the config
        save_config();

        // Reinit the local wifi network
        local_wifi_init();

        // Disconect to wifi when connected
        if (WiFi.status() == WL_CONNECTED) {
            WiFi.disconnect();
        }

        // Connect to wifi when ssid and password are not empty
        if (wifi_ssid != "" && wifi_password != "") {
            wifi_connect();
        }
    });

    // Begin the web server
    webserver.begin();
}

// The start of our program
void setup() {
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
    webserver_init();
}

// The program loop
void loop() {
    // Handle any http clients
    webserver.handleClient();
}
