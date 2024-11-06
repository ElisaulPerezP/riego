import os
import time
import subprocess

# Definir los pines y sus categorías
pins_info = {
    # Camellones (Lógica Positiva)
    11: {'nombre': 'camellones11', 'logica': 'positiva'},
    14: {'nombre': 'camellones14', 'logica': 'positiva'},
    13: {'nombre': 'camellones13', 'logica': 'positiva'},
    12: {'nombre': 'camellones12', 'logica': 'positiva'},
    # Camellones (Lógica Negativa)
    10: {'nombre': 'camellonesLogicaNegativa10', 'logica': 'negativa'},
    9: {'nombre': 'camellonesLogicaNegativa9', 'logica': 'negativa'},
    27: {'nombre': 'camellonesLogicaNegativa27', 'logica': 'negativa'},
    6: {'nombre': 'camellonesLogicaNegativa6', 'logica': 'negativa'},
    5: {'nombre': 'camellonesLogicaNegativa5', 'logica': 'negativa'},
    # Fluxómetros (Lógica Positiva)
    20: {'nombre': 'Fluxometros20', 'logica': 'positiva'},
    21: {'nombre': 'Fluxometros21', 'logica': 'positiva'},
    # Inyectores (Lógica Negativa)
    2: {'nombre': 'inyectoresLogicaNegativa2', 'logica': 'negativa'},
    3: {'nombre': 'inyectoresLogicaNegativa3', 'logica': 'negativa'},
    # Motobombas (Lógica Positiva)
    15: {'nombre': 'motobombas15', 'logica': 'positiva'},
    16: {'nombre': 'motobombas16', 'logica': 'positiva'},
    # Nivel (Lógica Positiva)
    22: {'nombre': 'nivel22', 'logica': 'positiva'},
    23: {'nombre': 'nivel23', 'logica': 'positiva'},
    # Parada (Lógica Positiva)
    24: {'nombre': 'parada24', 'logica': 'positiva'},
}

def limpiar_consola():
    # Limpiar la consola (funciona en Windows y Unix)
    os.system('cls' if os.name == 'nt' else 'clear')

def obtener_estado_gpio():
    # Ejecutar el comando 'raspi-gpio get' y obtener la salida
    resultado = subprocess.check_output(['raspi-gpio', 'get'], text=True)
    lines = resultado.strip().split('\n')
    gpio_states = {}
    for line in lines:
        # Ejemplo de línea: "GPIO 2: level=1 fsel=0 func=INPUT"
        partes = line.split()
        if len(partes) >= 2:
            pin_str = partes[1]
            if pin_str.endswith(':'):
                pin_num = int(pin_str[:-1])
                if pin_num in pins_info:
                    # Extraer 'level=' y 'func='
                    nivel = None
                    tipo = None
                    for parte in partes:
                        if parte.startswith('level='):
                            nivel = int(parte.split('=')[1])
                        if parte.startswith('func='):
                            func = parte.split('=')[1]
                            if func == 'OUTPUT':
                                tipo = 'OUT'
                            elif func == 'INPUT':
                                tipo = 'IN'
                    if nivel is not None and tipo is not None:
                        gpio_states[pin_num] = {'nivel': nivel, 'tipo': tipo}
    return gpio_states

def determinar_estado(valor, logica):
    if logica == 'positiva':
        return 'ON' if valor == 1 else 'OFF'
    elif logica == 'negativa':
        return 'OFF' if valor == 1 else 'ON'
    else:
        return 'Desconocido'

def mostrar_tabla(gpio_states):
    # Imprimir los datos en forma de tabla
    print(f"{'Pin':<5} {'Nombre':<30} {'Tipo':<5} {'Valor':<5} {'Estado'}")
    print("-" * 60)
    for pin_num, info in pins_info.items():
        nombre = info['nombre']
        logica = info['logica']
        pin_state = gpio_states.get(pin_num)
        if pin_state:
            valor = pin_state['nivel']
            tipo = pin_state['tipo']
            estado = determinar_estado(valor, logica)
        else:
            valor = 'N/A'
            tipo = 'N/A'
            estado = 'No disponible'
        print(f"{pin_num:<5} {nombre:<30} {tipo:<5} {valor:<5} {estado}")

try:
    while True:
        limpiar_consola()
        estado_gpio = obtener_estado_gpio()
        mostrar_tabla(estado_gpio)
        time.sleep(1)
except KeyboardInterrupt:
    print("\nEjecución detenida por el usuario.")
