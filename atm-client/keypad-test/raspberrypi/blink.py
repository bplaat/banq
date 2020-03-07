from gpiozero import LED
from time import sleep

led = LED(17)

while True:
    print("Led on")
    led.on()
    sleep(1)

    print("Led off")
    led.off()
    sleep(1)
