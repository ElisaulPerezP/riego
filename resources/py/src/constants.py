# constants.py

# Intervalos de tiempo en segundos
SENSOR_MONITOR_INTERVAL = 1          # Intervalo para monitorear sensores
EMERGENCY_STOP_CHECK_INTERVAL = 0.1  # Intervalo para verificar el botón de parada de emergencia
COMMUNICATION_CHECK_INTERVAL = 60    # Intervalo para verificar eventos de comunicación
LEVEL_SENSOR_CHECK_INTERVAL = 1      # Intervalo para verificar sensores de nivel
SCHEDULER_CHECK_INTERVAL = 60        # Intervalo para verificar el programador

# Nombres predeterminados de archivos de configuración
PROGRAM_FILE = 'programa_actual.json'
CRONOGRAMA_ACTIVIDADES_FILE = 'cronograma_actividades.json'
CRONOGRAMA_COMUNICACIONES_FILE = 'cronograma_comunicaciones.json'
GPIO_CONFIG_FILES = {
    'fluxometros': 'fluxometros.txt',
    'parada': 'parada.txt',
    'nivel': 'nivel.txt',
    'inyectoresLogicaNegativa': 'inyectoresLogicaNegativa.txt',
    'inyectores': 'inyectores.txt',
    'camellonesLogicaNegativa': 'camellonesLogicaNegativa.txt',
    'camellones': 'camellones.txt',
    'valvulaTanquesLogicaNegativa': 'valvulaTanquesLogicaNegativa.txt',
    'motobombas': 'motobombas.txt'
}
API_CONFIG_FILE = 'api_config.json'
LOGS_FILE = 'logs.txt'

# Mensajes de error
ERROR_MSG_FILE_NOT_FOUND = "Archivo inexistente: {}"
ERROR_MSG_FILE_DECODE = "Error al decodificar {}: {}"
ERROR_MSG_IO = "Error al leer {}: {}"

# Otros constantes
MAX_RETRIES = 3   # Número máximo de reintentos para la comunicación
TIMEOUT = 10      # Tiempo de espera para solicitudes HTTP en segundos
