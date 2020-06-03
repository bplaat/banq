#pragma once

#include <ESP8266WiFi.h>
#include <ESP8266WebServer.h>

// A function which inits the local wifi network
// Default IP address = 192.168.1.1
void local_wifi_init();

// A function which connects to a wifi network
void wifi_connect();

// A function that inits the webs server
void webserver_init(ESP8266WebServer& webserver);

// Local wifi network ip addresses
extern IPAddress local_ip;
extern IPAddress gateway;
extern IPAddress subnet;
