# utils.py

import logging
from datetime import datetime, timedelta
import json

def setup_logging():
    """
    Configura los ajustes de registro para la aplicación.
    """
    logging.basicConfig(
        filename='application.log',
        level=logging.DEBUG,
        format='%(asctime)s %(levelname)s: %(message)s',
        datefmt='%Y-%m-%d %H:%M:%S'
    )

def log_error(message):
    """
    Registra un mensaje de error.
    """
    logging.error(message)

def log_info(message):
    """
    Registra un mensaje informativo.
    """
    logging.info(message)

def parse_time_string(time_str):
    """
    Analiza una cadena de tiempo en el formato 'HH:MM' y devuelve un objeto datetime.time.
    """
    return datetime.strptime(time_str, '%H:%M').time()

def time_in_range(start_time, end_time, current_time=None):
    """
    Verifica si la hora actual está dentro del rango de tiempo especificado.
    """
    if current_time is None:
        current_time = datetime.now().time()
    if start_time <= end_time:
        return start_time <= current_time <= end_time
    else:  # Pasa la medianoche
        return current_time >= start_time or current_time <= end_time

def compare_programs(programa1, programa2):
    """
    Compara dos programas de riego para determinar si son iguales.
    """
    return programa1 == programa2

def calculate_irrigation_time(volumen, flow_rate):
    """
    Calcula el tiempo de riego basado en el volumen y la tasa de flujo.
    :param volumen: Volumen de agua a regar (litros)
    :param flow_rate: Tasa de flujo (litros por segundo)
    :return: Tiempo en segundos
    """
    if flow_rate <= 0:
        raise ValueError("La tasa de flujo debe ser positiva.")
    return volumen / flow_rate

def load_json_file(filename):
    """
    Carga un archivo JSON y devuelve su contenido.
    """
    with open(filename, 'r') as f:
        return json.load(f)

def save_json_file(filename, data):
    """
    Guarda datos en un archivo JSON.
    """
    with open(filename, 'w') as f:
        json.dump(data, f, indent=4)
