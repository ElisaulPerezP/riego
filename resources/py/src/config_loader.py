# config_loader.py

import json
import os

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
        Intenta cargar el cronograma de actividades.
        """
        filename = 'cronograma_actividades.json'
        if os.path.isfile(filename):
            try:
                with open(filename, 'r') as f:
                    self.cronograma_actividades = json.load(f)
                self.flags['flagArchivoCronogramaActividades'] = True
                return True
            except json.JSONDecodeError as e:
                self.flags['flagArchivoCronogramaActividades'] = False
                self.error_messages.append(f"Error al decodificar {filename}: {e}")
                return False
        else:
            self.flags['flagArchivoCronogramaActividades'] = False
            self.error_messages.append(f"Archivo inexistente: {filename}")
            return False

    def save_cronograma_actividades(self, cronograma):
        """
        Guarda el cronograma de actividades en un archivo local.
        """
        filename = 'cronograma_actividades.json'
        try:
            with open(filename, 'w') as f:
                json.dump(cronograma, f, indent=4)
            self.cronograma_actividades = cronograma
            self.flags['flagArchivoCronogramaActividades'] = True
            print("Cronograma de actividades guardado exitosamente.")
        except IOError as e:
            self.flags['flagArchivoCronogramaActividades'] = False
            self.error_messages.append(f"Error al guardar {filename}: {e}")

def load_cronograma_comunicaciones(self):
    filename = 'cronograma_comunicaciones.json'
    if os.path.isfile(filename):
        try:
            with open(filename, 'r') as f:
                self.cronograma_comunicaciones = json.load(f)
            print("Cronograma de comunicaciones cargado:", self.cronograma_comunicaciones)  # Depuración
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
