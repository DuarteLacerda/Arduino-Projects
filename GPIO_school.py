import time
import requests
import board
import busio
import RPi.GPIO as GPIO
from adafruit_sht31d import SHT31D
from gpiozero import Servo
from gpiozero.pins.pigpio import PiGPIOFactory

# === Configuração WiFi não necessária em Raspberry Pi (já vem ligado via SO) ===

# === Configuração dos pinos ===
TRIG_PIN = 18
ECHO_PIN = 19
SERVO_PIN = 23

RED_PIN = 13
GREEN_PIN = 12
BLUE_PIN = 14

# Inicializar GPIO
GPIO.setmode(GPIO.BCM)
GPIO.setup(TRIG_PIN, GPIO.OUT)
GPIO.setup(ECHO_PIN, GPIO.IN)
GPIO.setup(RED_PIN, GPIO.OUT)
GPIO.setup(GREEN_PIN, GPIO.OUT)
GPIO.setup(BLUE_PIN, GPIO.OUT)

# Inicializar I2C e SHT31
i2c = busio.I2C(board.SCL, board.SDA)
sht31 = SHT31D(i2c)

# Inicializar servo
factory = PiGPIOFactory()
servo = Servo(SERVO_PIN, pin_factory=factory)

# URL do servidor
SERVER_URL = "https://iot.dei.estg.ipleiria.pt/ti/ti007/ti/api/api.php"

def measure_distance():
    GPIO.output(TRIG_PIN, False)
    time.sleep(0.0002)
    GPIO.output(TRIG_PIN, True)
    time.sleep(0.00001)
    GPIO.output(TRIG_PIN, False)

    while GPIO.input(ECHO_PIN) == 0:
        pulse_start = time.time()
    while GPIO.input(ECHO_PIN) == 1:
        pulse_end = time.time()

    pulse_duration = pulse_end - pulse_start
    distance = (pulse_duration * 34300) / 2
    return distance

def control_rgb_by_humidity(humidity):
    if 40 <= humidity <= 60:
        GPIO.output(RED_PIN, False)
        GPIO.output(GREEN_PIN, True)
        GPIO.output(BLUE_PIN, False)
        return 1
    elif (30 <= humidity < 40) or (60 < humidity <= 70):
        GPIO.output(RED_PIN, True)
        GPIO.output(GREEN_PIN, True)
        GPIO.output(BLUE_PIN, False)
        return 2
    else:
        GPIO.output(RED_PIN, True)
        GPIO.output(GREEN_PIN, False)
        GPIO.output(BLUE_PIN, False)
        return 3

def control_gate(distance):
    if distance < 10:
        servo.value = -0.5  # posição aproximada para 50°
        return 50
    else:
        servo.value = 0.5   # posição aproximada para 160°
        return 160

def send_to_server(name, value):
    try:
        data = {'nome': name, 'valor': str(value)}
        response = requests.post(SERVER_URL, data=data)
        print(f"Enviado '{name}': {value} (HTTP {response.status_code})")
    except Exception as e:
        print(f"Erro ao enviar '{name}': {e}")

try:
    while True:
        dist = measure_distance()
        angle = control_gate(dist)
        
        try:
            temp = sht31.temperature
            hum = sht31.relative_humidity
            color = control_rgb_by_humidity(hum)

            send_to_server("distancia", dist)
            send_to_server("cancela", angle)
            send_to_server("temperatura", temp)
            send_to_server("humidade", hum)
            send_to_server("led_rgb", color)
        except Exception as e:
            print("Erro ao ler temperatura/humidade:", e)

        time.sleep(1)

except KeyboardInterrupt:
    print("A terminar...")

finally:
    GPIO.cleanup()
