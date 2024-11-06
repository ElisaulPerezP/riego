# gpio_manager.py

import pigpio
import threading
import time
import logging

# Configuración del sistema de logging
logging.basicConfig(
    filename='gpio.log',  # Archivo donde se guardarán los logs
    filemode='a',         # 'a' para anexar, 'w' para sobrescribir
    level=logging.DEBUG,  # Nivel mínimo de mensajes a registrar
    format='%(asctime)s - %(levelname)s - %(message)s'  # Formato de salida
)

class GPIOManager:
    def __init__(self, gpio_config):
        self.gpio_config = gpio_config  # Diccionario con las configuraciones de pines
        self.pi = pigpio.pi()

        # Inicializar bloqueos para seguridad de hilos
        self.flow_lock = threading.Lock()
        self.level_lock = threading.Lock()
        self.emergency_lock = threading.Lock()

        if not self.pi.connected:
            logging.error("Error: No se pudo conectar con el daemon de pigpio.")
            exit()

        # Diccionarios para almacenar el estado de los sensores y actuadores
        self.flow_counts = [0, 0]  # Contadores para los dos sensores de flujo
        self.level_states = [False, False]  # Estados de los dos sensores de nivel
        self.emergency_stop = False  # Estado del botón de parada de emergencia

        # Configuración de pines
        self.setup_pins()

        # Callbacks para los sensores de flujo
        self.flow_callbacks = []
        self.setup_flow_sensors()
        logging.debug("GPIOManager iniciado mediante su constructor")

    def setup_pins(self):
        """
        Configura los pines GPIO según la configuración proporcionada.
        """
        # Configurar pines de actuadores (salidas)
        # Electrovalvulas
        for pin in self.gpio_config['camellones']:
            self.pi.set_mode(pin, pigpio.OUTPUT)
            self.pi.write(pin, 0)  # Inicialmente apagado

        # Electrovalvulas de lógica negativa
        for pin in self.gpio_config['camellonesLogicaNegativa']:
            self.pi.set_mode(pin, pigpio.OUTPUT)
            self.pi.write(pin, 1)  # Inicialmente apagado (lógica negativa)

        # Bombas de inyección
        for pin in self.gpio_config['inyectoresLogicaNegativa']:
            self.pi.set_mode(pin, pigpio.OUTPUT)
            self.pi.write(pin, 1)  # Inicialmente apagado (lógica negativa)

        # Motobombas
        for pin in self.gpio_config['motobombas']:
            self.pi.set_mode(pin, pigpio.OUTPUT)
            self.pi.write(pin, 0)  # Inicialmente apagado

        # Válvula de llenado de tanques (lógica negativa)
        tanque_pin = self.gpio_config['valvulaTanquesLogicaNegativa']
        self.pi.set_mode(tanque_pin, pigpio.OUTPUT)
        self.pi.write(tanque_pin, 1)  # Inicialmente apagado

        # Configurar pines de sensores (entradas)
        # Sensores de flujo
        for pin in self.gpio_config['fluxometros']:
            self.pi.set_mode(pin, pigpio.INPUT)
            self.pi.set_pull_up_down(pin, pigpio.PUD_DOWN)

        # Sensores de nivel
        for pin in self.gpio_config['nivel']:
            self.pi.set_mode(pin, pigpio.INPUT)
            self.pi.set_pull_up_down(pin, pigpio.PUD_DOWN)

        # Botón de parada de emergencia
        parada_pin = self.gpio_config['parada']
        self.pi.set_mode(parada_pin, pigpio.INPUT)
        self.pi.set_pull_up_down(parada_pin, pigpio.PUD_UP)

    # Resto de los métodos de la clase GPIOManager...

# Asegúrate de que todos los métodos estén correctamente indentados dentro de la clase.


    def accion_riego_completa(self, camellon, volumen, fertilizante1, fertilizante2, fin_time_str):
        """
        Ejecuta la acción de riego completa en un camellón específico con control de volumen y fertilizante.
        """
        logging.debug(f"Iniciando riego en camellón {camellon}")
        self.control_valve(camellon, 'ON')
        volumen_actual = 0
        FACTOR_CONVERSION_FLUJO = 0.1  # Ajusta según sea necesario

        # Convertir fin_time_str a un objeto datetime
        from datetime import datetime, timedelta

        # Convertir fin_time_str a un objeto datetime considerando el día actual
        fin_time = datetime.strptime(fin_time_str, '%H:%M').time()
        now = datetime.now()
        fin_datetime = datetime.combine(now.date(), fin_time)

        # Si fin_datetime es anterior o igual a now, significa que la hora de fin es al día siguiente
        if fin_datetime <= now:
            fin_datetime += timedelta(days=1)

        # Controlar flujo y fertilizantes
        while volumen_actual < volumen:
            flujo = self.read_flow_counts()[0]  # Leer el flujo correspondiente
            volumen_actual += flujo * FACTOR_CONVERSION_FLUJO
            time.sleep(1)

            # Verificar si se alcanzó la hora de finalización
            now = datetime.now()
            if now >= fin_datetime:
                logging.debug(f"Tiempo de riego alcanzado para camellón {camellon}. Volumen alcanzado: {volumen_actual}")
                break

        # Apagar la válvula del camellón
        self.control_valve(camellon, 'OFF')

        logging.debug(f"Riego en camellón {camellon} completado. Volumen alcanzado: {volumen_actual}")

        # Manejo de fertilizantes
        # Asumiendo que los fertilizantes deben ser aplicados incluso si el tiempo de riego ha terminado
        if fertilizante1 > 0:
            logging.debug(f"Iniciando aplicación de fertilizante 1 en camellón {camellon}")
            self.control_injector(1, 'ON')
            time.sleep(fertilizante1)
            self.control_injector(1, 'OFF')
            logging.debug(f"Aplicación de fertilizante 1 completada en camellón {camellon}")

        if fertilizante2 > 0:
            logging.debug(f"Iniciando aplicación de fertilizante 2 en camellón {camellon}")
            self.control_injector(2, 'ON')
            time.sleep(fertilizante2)
            self.control_injector(2, 'OFF')
            logging.debug(f"Aplicación de fertilizante 2 completada en camellón {camellon}")

    def setup_flow_sensors(self):
        """
        Configura los callbacks para los sensores de flujo.
        """
        for i, pin in enumerate(self.gpio_config['fluxometros']):
            cb = self.pi.callback(pin, pigpio.RISING_EDGE, self.flow_callback_factory(i))
            self.flow_callbacks.append(cb)

    def flow_callback_factory(self, index):
        """
        Crea una función de callback para un sensor de flujo específico.
        """
        def _flow_callback(gpio, level, tick):
            with self.flow_lock:
                self.flow_counts[index] += 1
        return _flow_callback

    def read_flow_counts(self):
        """
        Devuelve los contadores actuales de los sensores de flujo.
        """
        with self.flow_lock:
            counts = self.flow_counts.copy()
            self.flow_counts = [0, 0]  # Reiniciar contadores después de leer
        return counts

    def monitor_level_sensors(self):
        """
        Monitorea los sensores de nivel en un hilo separado.
        """
        while True:
            with self.level_lock:
                for i, pin in enumerate(self.gpio_config['nivel']):
                    self.level_states[i] = self.pi.read(pin)
            time.sleep(1)  # Ajusta el intervalo según sea necesario

    def monitor_emergency_stop(self):
        """
        Monitorea el botón de parada de emergencia en un hilo separado.
        """
        while True:
            with self.emergency_lock:
                self.emergency_stop = not self.pi.read(self.gpio_config['parada'])  # Botón con PULL_UP
                if self.emergency_stop:
                    print("¡Parada de emergencia activada!")
                    self.stop_all_actuators()
            time.sleep(0.1)  # Verificar con alta frecuencia

    def stop_all_actuators(self):
        """
        Apaga todos los actuadores inmediatamente.
        """
        # Apagar electrovalvulas
        for pin in self.gpio_config['camellones']:
            self.pi.write(pin, 0)
        for pin in self.gpio_config['camellonesLogicaNegativa']:
            self.pi.write(pin, 1)

        # Apagar bombas de inyección
        for pin in self.gpio_config['inyectoresLogicaNegativa']:
            self.pi.write(pin, 1)

        # Apagar motobombas
        for pin in self.gpio_config['motobombas']:
            self.pi.write(pin, 0)

        # Cerrar válvula de llenado de tanques
        self.pi.write(self.gpio_config['valvulaTanquesLogicaNegativa'], 1)


    def control_valve(self, camellon_number, action):
        """
        Controla una electrovalvula específica.
        """
        pin = None
        logic = None

        if camellon_number in self.gpio_config['camellones_indices']:
            index = self.gpio_config['camellones_indices'].index(camellon_number)
            pin = self.gpio_config['camellones'][index]
            logic = 'positive'
        elif camellon_number in self.gpio_config['camellonesLogicaNegativa_indices']:
            index = self.gpio_config['camellonesLogicaNegativa_indices'].index(camellon_number)
            pin = self.gpio_config['camellonesLogicaNegativa'][index]
            logic = 'negative'
        else:
            logging.error(f"Camellón {camellon_number} no encontrado en la configuración.")
            return

        if action == 'ON':
            if logic == 'positive':
                self.pi.write(pin, 1)
            else:
                self.pi.write(pin, 0)
        elif action == 'OFF':
            if logic == 'positive':
                self.pi.write(pin, 0)
            else:
                self.pi.write(pin, 1)
        else:
            logging.error(f"Acción inválida para control de válvula: {action}")


    def control_pump(self, pump_number, action):
        """
        Controla una motobomba específica.

        :param pump_number: Número de la bomba (1 o 2)
        :param action: 'ON' para encender, 'OFF' para apagar
        """
        if pump_number in [1, 2]:
            pin = self.gpio_config['motobombas'][pump_number - 1]
            if action == 'ON':
                self.pi.write(pin, 1)
            elif action == 'OFF':
                self.pi.write(pin, 0)
            else:
                print("Acción inválida para control de bomba.")
        else:
            print("Número de bomba inválido.")

    def control_injector(self, injector_number, action):
        """
        Controla una bomba de inyección específica.

        :param injector_number: Número del inyector (1 o 2)
        :param action: 'ON' para encender, 'OFF' para apagar
        """
        if injector_number in [1, 2]:
            pin = self.gpio_config['inyectoresLogicaNegativa'][injector_number - 1]
            if action == 'ON':
                self.pi.write(pin, 0)  # Lógica negativa
            elif action == 'OFF':
                self.pi.write(pin, 1)  # Lógica negativa
            else:
                print("Acción inválida para control de inyector.")
        else:
            print("Número de inyector inválido.")

    def control_tank_valve(self, action):
        """
        Controla la válvula de llenado de tanques.

        :param action: 'ON' para abrir, 'OFF' para cerrar
        """
        pin = self.gpio_config['valvulaTanquesLogicaNegativa']
        if action == 'ON':
            self.pi.write(pin, 0)  # Lógica negativa
        elif action == 'OFF':
            self.pi.write(pin, 1)  # Lógica negativa
        else:
            print("Acción inválida para control de válvula de tanques.")

    def get_level_states(self):
        """
        Devuelve los estados actuales de los sensores de nivel.
        """
        with self.level_lock:
            return self.level_states.copy()



    def run(self):
        """
        Método principal para ejecutar en un hilo separado si es necesario.
        """
        # Iniciar hilos para monitorear sensores
        threading.Thread(target=self.monitor_level_sensors, daemon=True).start()
        threading.Thread(target=self.monitor_emergency_stop, daemon=True).start()
        # El monitoreo de sensores de flujo ya está manejado por callbacks

        # Mantener el hilo en ejecución
        while True:
            time.sleep(1)

    def cleanup(self):
        """
        Limpia los recursos y cierra la conexión con pigpio.
        """
        for cb in self.flow_callbacks:
            cb.cancel()
        self.pi.stop()


if __name__ == '__main__':
    controller = GPIOManager()
    controller.setup_pins()