// To compile code on Windows:
// MinGW: gcc -mwindows keypad.c -o keypad.exe -lgdi32 && ./keypad
// TCC: tcc keypad.c && ./keypad

// Information Links:
// http://www.winprog.org/tutorial/simple_window.html
// https://www.xanthium.in/Serial-Port-Programming-using-Win32-API

#include <windows.h>
#include <stdio.h>
#include <stdlib.h>
#include <time.h>

#define WINDOW_WIDTH 800
#define WINDOW_HEIGHT 600

char className[] = "keypadTest";
char windowTitle[] = "Keypad Serial Demo";
char versionLabel[] = "v0.1";
char fontName[] = "Tahoma";
char footerLabel[] = "Made by Bastiaan van der Plaat";

char infoLabel[256] = "Connecting...";
HWND hwnd = NULL;
HANDLE serialFile = NULL;
HFONT largeFont = NULL;
HFONT smallFont = NULL;

int rand_range (int min, int max) {
    return rand() % ((max - min) + 1) + min;
}

DWORD WINAPI serialReadThread(LPVOID lpParameter) {
    char serialBuffer[256];
    for (;;) {
        DWORD length = 0;
        ReadFile(serialFile, &serialBuffer, 255, &length, NULL);
        if (length > 0) {
            serialBuffer[length] = 0;
            strcpy(infoLabel, serialBuffer);
            InvalidateRect(hwnd, NULL, TRUE);
        }
    }
    return 0;
}

LRESULT CALLBACK WndProc(HWND hwnd, UINT msg, WPARAM wParam, LPARAM lParam) {
    if (msg == WM_CREATE) {
        RECT clientRect;
        GetClientRect(hwnd, &clientRect);
        int width = WINDOW_WIDTH + (WINDOW_WIDTH - clientRect.right);
        int height = WINDOW_HEIGHT + (WINDOW_HEIGHT - clientRect.bottom);
        SetWindowPos(hwnd, HWND_TOP, (GetSystemMetrics(SM_CXSCREEN) - width) / 2, (GetSystemMetrics(SM_CYSCREEN) - height) / 2, width, height, SWP_SHOWWINDOW);

        largeFont = CreateFont(width * 10 / 100, 0, 0, 0, FW_BOLD, 0, 0, 0, 0, 0, 0, 0, 0, fontName);
        smallFont = CreateFont(width * 3 / 100, 0, 0, 0, FW_BOLD, 0, 0, 0, 0, 0, 0, 0, 0, fontName);

        for (int i = 1; i <= 255; i++) {
            char comFileName[32];
            sprintf(comFileName, "COM%d", i);
            serialFile = CreateFile(comFileName, GENERIC_READ, 0, NULL, OPEN_EXISTING, FILE_ATTRIBUTE_NORMAL, NULL);

            if (serialFile != INVALID_HANDLE_VALUE) {
                DCB dcbSerialParams = { 0 };
                dcbSerialParams.DCBlength = sizeof(dcbSerialParams);
                dcbSerialParams.BaudRate = CBR_9600;
                dcbSerialParams.ByteSize = 8;
                dcbSerialParams.StopBits = ONESTOPBIT;
                dcbSerialParams.Parity = NOPARITY;
                SetCommState(serialFile, &dcbSerialParams);

                COMMTIMEOUTS timeouts = { 0 };
                timeouts.ReadIntervalTimeout = 50;
                timeouts.ReadTotalTimeoutConstant = 50;
                timeouts.ReadTotalTimeoutMultiplier = 10;
                timeouts.WriteTotalTimeoutConstant = 50;
                timeouts.WriteTotalTimeoutMultiplier = 10;
                SetCommTimeouts(serialFile, &timeouts);

                CreateThread(0, 0, serialReadThread, NULL, 0, NULL);

                strcpy(infoLabel, "Connected");
                return 0;
            }

            CloseHandle(serialFile);
        }

        strcpy(infoLabel, "No serial port");
        return 0;
    }

    else if (msg == WM_SIZE) {
        int width = LOWORD(lParam);
        DeleteObject(largeFont);
        largeFont = CreateFont(width * 10 / 100, 0, 0, 0, FW_BOLD, 0, 0, 0, 0, 0, 0, 0, 0, fontName);
        DeleteObject(smallFont);
        smallFont = CreateFont(width * 3 / 100, 0, 0, 0, FW_BOLD, 0, 0, 0, 0, 0, 0, 0, 0, fontName);
    }

    else if (msg == WM_PAINT) {
        PAINTSTRUCT ps;
        HDC hdc = BeginPaint(hwnd, &ps);

        SetBkMode(hdc, TRANSPARENT);

        RECT clientRect;
        GetClientRect(hwnd, &clientRect);
        int width = clientRect.right;
        int height = clientRect.bottom;
        int padding = width * 3 / 100;

        SelectObject(hdc, smallFont);
        SetTextAlign(hdc, TA_LEFT);
        TextOut(hdc, padding, padding, windowTitle, strlen(windowTitle));

        SetTextAlign(hdc, TA_RIGHT);
        TextOut(hdc, width - padding, padding, versionLabel, strlen(versionLabel));

        SelectObject(hdc, largeFont);
        SetTextAlign(hdc, TA_CENTER);
        TextOut(hdc, width / 2, height / 2 - width * 5 / 100, infoLabel, strlen(infoLabel));

        SelectObject(hdc, smallFont);
        SetTextAlign(hdc, TA_CENTER | TA_BOTTOM);
        TextOut(hdc, width / 2, height - padding, footerLabel, strlen(footerLabel));

        EndPaint(hwnd, &ps);
        return 0;
    }

    else if (msg == WM_DESTROY) {
        DeleteObject(largeFont);
        DeleteObject(smallFont);
        CloseHandle(serialFile);
        PostQuitMessage(0);
        return 0;
    }

    else {
        return DefWindowProc(hwnd, msg, wParam, lParam);
    }
}

int WINAPI WinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, LPSTR lpCmdLine, int nCmdShow) {
    srand(time(NULL));

    WNDCLASSEX wc = { 0 };
    wc.cbSize = sizeof(WNDCLASSEX);
    wc.style = CS_HREDRAW | CS_VREDRAW;
    wc.lpfnWndProc = WndProc;
    wc.hInstance = hInstance;
    wc.hIcon = LoadIcon(NULL, IDI_APPLICATION);
    wc.hCursor = LoadCursor(NULL, IDC_ARROW);
    wc.hbrBackground = CreateSolidBrush(RGB(rand_range(128, 255), rand_range(128, 255), rand_range(128, 255)));
    wc.lpszClassName = className;
    wc.hIconSm = LoadIcon(NULL, IDI_APPLICATION);
    RegisterClassEx(&wc);

    hwnd = CreateWindow(className, windowTitle, WS_OVERLAPPEDWINDOW, CW_USEDEFAULT, CW_USEDEFAULT, WINDOW_WIDTH, WINDOW_HEIGHT, NULL, NULL, hInstance, NULL);

    MSG msg;
    while (GetMessage(&msg, NULL, 0, 0) > 0) {
        TranslateMessage(&msg);
        DispatchMessage(&msg);
    }
    return msg.wParam;
}
