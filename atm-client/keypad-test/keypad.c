// To compile code on Windows:
// MinGW: gcc -mwindows keypad.c -o keypad.exe -lgdi32 && ./keypad
// TCC: tcc keypad.c && ./keypad

// Information Links:
// http://www.winprog.org/tutorial/simple_window.html
// https://www.xanthium.in/Serial-Port-Programming-using-Win32-API

#include <windows.h>
#include <stdlib.h>
#include <time.h>

#define COM_FILE "COM4"

const char g_szClassName[] = "keypadTest";
WNDCLASSEX wc;
HWND hwnd;
HANDLE hComm;
char infoString[256] = "Connecting...";
HFONT font;

DWORD WINAPI serialReadThread(LPVOID lpParameter) {
    char serialBuffer[256] = { 0 };
    for (;;) {
        DWORD length = 0;
        ReadFile(hComm, &serialBuffer, 255, &length, NULL);
        if (length > 0) {
            serialBuffer[length] = 0;
            strcpy(infoString, serialBuffer);
            InvalidateRect(hwnd, NULL, TRUE);
        }
    }
    return 0;
}


LRESULT CALLBACK WndProc(HWND hwnd, UINT msg, WPARAM wParam, LPARAM lParam) {
    switch(msg) {
        case WM_CREATE:
            font = CreateFont(48, 0, 0, 0, FW_BOLD, 0, 0, 0, 0, 0, 0, 0, 0, "Arial");

            hComm = CreateFile(COM_FILE,               // Port name
                      GENERIC_READ | GENERIC_WRITE, // Read/Write
                      0,                            // No Sharing
                      NULL,                         // No Security
                      OPEN_EXISTING,                // Open existing port only
                      0,                            // Non Overlapped I/O
                      NULL);                        // Null for Comm Devices

            if (hComm != INVALID_HANDLE_VALUE) {
                DCB dcbSerialParams = { 0 };            // Initializing DCB structure
                dcbSerialParams.DCBlength = sizeof(dcbSerialParams);
                dcbSerialParams.BaudRate = CBR_9600;    // Setting BaudRate = 9600
                dcbSerialParams.ByteSize = 8;           // Setting ByteSize = 8
                dcbSerialParams.StopBits = ONESTOPBIT;  // Setting StopBits = 1
                dcbSerialParams.Parity   = NOPARITY;    // Setting Parity = None
                SetCommState(hComm, &dcbSerialParams);

                COMMTIMEOUTS timeouts = { 0 };
                timeouts.ReadIntervalTimeout         = 50; // in milliseconds
                timeouts.ReadTotalTimeoutConstant    = 50; // in milliseconds
                timeouts.ReadTotalTimeoutMultiplier  = 10; // in milliseconds
                timeouts.WriteTotalTimeoutConstant   = 50; // in milliseconds
                timeouts.WriteTotalTimeoutMultiplier = 10; // in milliseconds
                SetCommTimeouts(hComm, &timeouts);

                CreateThread(0, 0, serialReadThread, NULL, 0, NULL);

                strcpy(infoString, "Connected");
            }

            else {
                strcpy(infoString, "No serial port");
            }
        break;
        case WM_PAINT: {
            PAINTSTRUCT ps;
            HDC hdc = BeginPaint(hwnd, &ps);

            RECT rect;
            GetClientRect(hwnd, &rect);
            SelectObject(hdc, font);
            SetBkMode(hdc, TRANSPARENT);
            DrawText(hdc, infoString, -1, &rect, DT_SINGLELINE | DT_CENTER | DT_VCENTER);

            EndPaint(hwnd, &ps);
        }
        break;
        case WM_DESTROY:
            DeleteObject(font);
            CloseHandle(hComm);
            DeleteObject(wc.hbrBackground);
            PostQuitMessage(0);
        break;
        default:
            return DefWindowProc(hwnd, msg, wParam, lParam);
    }
    return 0;
}

int WINAPI WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, LPSTR lpCmdLine, int nCmdShow) {
    srand(time(NULL));

    wc.cbSize        = sizeof(WNDCLASSEX);
    wc.style         = CS_HREDRAW | CS_VREDRAW;
    wc.lpfnWndProc   = WndProc;
    wc.cbClsExtra    = 0;
    wc.cbWndExtra    = 0;
    wc.hInstance     = hInstance;
    wc.hIcon         = LoadIcon(NULL, IDI_APPLICATION);
    wc.hCursor       = LoadCursor(NULL, IDC_ARROW);
    wc.hbrBackground = CreateSolidBrush(RGB((rand() % 127) + 127, (rand() % 127) + 127, (rand() % 127) + 127));
    wc.lpszMenuName  = NULL;
    wc.lpszClassName = g_szClassName;
    wc.hIconSm       = LoadIcon(NULL, IDI_APPLICATION);
    RegisterClassEx(&wc);

    hwnd = CreateWindow(g_szClassName, "Keypad Serial Demo", WS_VISIBLE | WS_OVERLAPPEDWINDOW, CW_USEDEFAULT, CW_USEDEFAULT, 800, 600, NULL, NULL, hInstance, NULL);

    MSG msg;
    while (GetMessage(&msg, NULL, 0, 0) > 0) {
        TranslateMessage(&msg);
        DispatchMessage(&msg);
    }
    return msg.wParam;
}
