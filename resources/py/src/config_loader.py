# config_loader.py

import json
import os
from datetime import datetime


class ConfigLoader:
    def __init__(self):
        # Variables para almacenar los datos cargados
        self.programa_actual = None
        self.cronograma_actividades = None
        self.cronograma_comunicaciones = None
        self.gpio_config = None
        self.api_config = None
        self.logs = None

        # Banderas de control
        self.flags = {
            'flagArchivoProgramaActual': False,
            'flagArchivoCronogramaActividades': False,
            'flagArchivoCronogramaComunicaciones': False,
            'flagArchivoGPIO': False,
            'flagArchivoDirecciones': False,
            'flagArchivoLogs': False
        }

        # Mensajes de error
        self.error_messages = []

    def load_programa_actual(self):
        """
        Intenta cargar el archivo del programa actual de riego.
        """
        filename = 'programa_actual.json'
        if os.path.isfile(filename):
            try:
                with open(filename, 'r') as f:
                    self.programa_actual = json.load(f)
                self.flags['flagArchivoProgramaActual'] = True
                return True
            except json.JSONDecodeError as e:
                self.flags['flagArchivoProgramaActual'] = False
                self.error_messages.append(f"Error al decodificar {filename}: {e}")
                return False
        else:
            self.flags['flagArchivoProgramaActual'] = False
            self.error_messages.append(f"Archivo inexistente: {filename}")
            return False

    def save_programa_actual(self, programa):
        """
        Guarda el programa actual en un archivo local.
        """
        filename = 'programa_actual.json'
        try:
            with open(filename, 'w') as f:
                json.dump(programa, f, indent=4)
            self.programa_actual = programa
            self.flags['flagArchivoProgramaActual'] = True
            print("Programa actual guardado exitosamente.")
        except IOError as e:
            self.flags['flagArchivoProgramaActual'] = False
            self.error_messages.append(f"Error al guardar {filename}: {e}")

    def load_cronograma_actividades(self):
        """
        Intenta cargar y validar el cronograma de actividades.
        """
        filename = 'cronograma_actividades.json'
        if os.path.isfile(filename):
            try:
                with open(filename, 'r') as f:
                    data = json.load(f)
                # Validar el contenido del cronograma de actividades
                if self.validate_cronograma_actividades(data):
                    self.cronograma_actividades = data
                    self.flags['flagArchivoCronogramaActividades'] = True
                    return True
                else:
                    self.flags['flagArchivoCronogramaActividades'] = False
                    self.error_messages.append(f"Contenido inválido en {filename}")
                    return False
            except json.JSONDecodeError as e:
                self.flags['flagArchivoCronogramaActividades'] = False
                self.error_messages.append(f"Error al decodificar {filename}: {e}")
                return False
        else:
            self.flags['flagArchivoCronogramaActividades'] = False
            self.error_messages.append(f"Archivo inexistente: {filename}")
            return False

    def validate_cronograma_actividades(self, data):
        """
        Valida que el contenido del cronograma de actividades tenga la estructura y datos correctos.
        """
        # Verificar que `data` sea una lista y que no esté vacía
        if not isinstance(data, list) or not data:
            print("El cronograma de actividades debe ser una lista no vacía.")
            return False

        for index, actividad in enumerate(data):
            # Validar que cada actividad sea un diccionario
            if not isinstance(actividad, dict):
                print(f"Actividad en índice {index} no es un diccionario.")
                return False

            # Verificar que tenga las claves 'inicio', 'fin', 'accion'
            required_keys = ['inicio', 'fin', 'accion']
            for key in required_keys:
                if key not in actividad:
                    print(f"Falta la clave '{key}' en actividad en índice {index}.")
                    return False

            # Validar formatos de 'inicio' y 'fin'
            try:
                datetime.strptime(actividad['inicio'], '%H:%M')
                datetime.strptime(actividad['fin'], '%H:%M')
            except ValueError:
                print(f"Formato de hora inválido en actividad en índice {index}.")
                return False

            # Validar que 'accion' es un diccionario con las claves necesarias
            accion = actividad['accion']
            if not isinstance(accion, dict):
                print(f"'accion' en actividad en índice {index} no es un diccionario.")
                return False

            accion_required_keys = ['camellon', 'volumen', 'fertilizante1', 'fertilizante2']
            for key in accion_required_keys:
                if key not in accion:
                    print(f"Falta la clave '{key}' en 'accion' de actividad en índice {index}.")
                    return False

                # Validar que los valores sean del tipo correcto
                if key == 'camellon':
                    if not isinstance(accion[key], int) or accion[key] <= 0:
                        print(f"Valor inválido para 'camellon' en actividad en índice {index}.")
                        return False
                else:
                    if not isinstance(accion[key], (int, float)) or accion[key] < 0:
                        print(f"Valor inválido para '{key}' en actividad en índice {index}.")
                        return False

        # Si todas las validaciones pasan
        return True


        
    def load_cronograma_comunicaciones(self):
        """
        Intenta cargar el cronograma de comunicaciones.
        """
        filename = 'cronograma_comunicaciones.json'
        if os.path.isfile(filename):
            try:
                with open(filename, 'r') as f:
                    self.cronograma_comunicaciones = json.load(f)
                self.flags['flagArchivoCronogramaComunicaciones'] = True
                return True
            except json.JSONDecodeError as e:
                self.flags['flagArchivoCronogramaComunicaciones'] = False
                self.error_messages.append(f"Error al decodificar {filename}: {e}")
                return False
        else:
            self.flags['flagArchivoCronogramaComunicaciones'] = False
            self.error_messages.append(f"Archivo inexistente: {filename}")
            return False

    def load_gpio_config(self):
        """
        Intenta cargar la configuración de GPIO desde los archivos de texto.
        """
        try:
            self.gpio_config = {}

            # Cargar fluxometros
            with open('fluxometros.txt', 'r') as f:
                lines = f.read().splitlines()
                self.gpio_config['fluxometros'] = [int(pin) for pin in lines if pin.strip()]

            # Cargar parada de emergencia
            with open('parada.txt', 'r') as f:
                line = f.readline().strip()
                self.gpio_config['parada'] = int(line)

            # Cargar sensores de nivel
            with open('nivel.txt', 'r') as f:
                lines = f.read().splitlines()
                self.gpio_config['nivel'] = [int(pin) for pin in lines if pin.strip()]

            # Cargar inyectores de lógica negativa
            with open('inyectoresLogicaNegativa.txt', 'r') as f:
                lines = f.read().splitlines()
                self.gpio_config['inyectoresLogicaNegativa'] = [int(pin) for pin in lines if pin.strip()]

            # Cargar camellones de lógica negativa
            with open('camellonesLogicaNegativa.txt', 'r') as f:
                lines = f.read().splitlines()
                self.gpio_config['camellonesLogicaNegativa'] = [int(pin) for pin in lines if pin.strip()]
                self.gpio_config['camellonesLogicaNegativa_indices'] = list(range(1, len(self.gpio_config['camellonesLogicaNegativa']) + 1))

            # Cargar camellones de lógica positiva
            with open('camellones.txt', 'r') as f:
                lines = f.read().splitlines()
                self.gpio_config['camellones'] = [int(pin) for pin in lines if pin.strip()]
                offset = len(self.gpio_config['camellonesLogicaNegativa_indices'])
                self.gpio_config['camellones_indices'] = list(range(offset + 1, offset + len(self.gpio_config['camellones']) + 1))

            # Cargar válvula de tanques de lógica negativa
            with open('valvulaTanquesLogicaNegativa.txt', 'r') as f:
                line = f.readline().strip()
                self.gpio_config['valvulaTanquesLogicaNegativa'] = int(line)

            # Cargar motobombas
            with open('motobombas.txt', 'r') as f:
                lines = f.read().splitlines()
                self.gpio_config['motobombas'] = [int(pin) for pin in lines if pin.strip()]

            self.flags['flagArchivoGPIO'] = True
            return True
        except (IOError, ValueError) as e:
            self.flags['flagArchivoGPIO'] = False
            self.error_messages.append(f"Error al cargar configuración GPIO: {e}")
            return False

    def load_api_config(self):
        """
        Intenta cargar la configuración de las direcciones de API.
        """
        filename = 'api_config.txt'
        if os.path.isfile(filename):
            try:
                with open(filename, 'r') as f:
                    self.api_config = json.load(f)
                self.flags['flagArchivoDirecciones'] = True
                return True
            except json.JSONDecodeError as e:
                self.flags['flagArchivoDirecciones'] = False
                self.error_messages.append(f"Error al decodificar {filename}: {e}")
                return False
        else:
            self.flags['flagArchivoDirecciones'] = False
            self.error_messages.append(f"Archivo inexistente: {filename}")
            return False

    def load_logs(self):
        """
        Intenta cargar el archivo de logs (si es necesario).
        """
        filename = 'logs.txt'
        if os.path.isfile(filename):
            try:
                with open(filename, 'r') as f:
                    self.logs = f.read()
                self.flags['flagArchivoLogs'] = True
                return True
            except IOError as e:
                self.flags['flagArchivoLogs'] = False
                self.error_messages.append(f"Error al leer {filename}: {e}")
                return False
        else:
            self.flags['flagArchivoLogs'] = False
            self.error_messages.append(f"Archivo inexistente: {filename}")
            return False

    def get_error_messages(self):
        """
        Devuelve la lista de mensajes de error acumulados.
        """
        return self.error_messages

    def load_all_configs(self):
        """
        Método auxiliar para cargar todas las configuraciones.
        """
        self.load_programa_actual()
        self.load_cronograma_actividades()
        self.load_cronograma_comunicaciones()
        self.load_gpio_config()
        self.load_api_config()
        self.load_logs()
