# gpio_manager.py

import pigpio
import threading
import time
from datetime import datetime, timedelta
import logging
import os
from dotenv import load_dotenv

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
        
           # Validar el camellón antes de iniciar los hilos
        if not self.is_valid_camellon(camellon):
            logging.error(f"Camellón {camellon} no encontrado en la configuración.")
            return  # O puedes lanzar una excepción si lo prefieres
        # Crear un evento para señalizar cuándo detener los hilos
        stop_event = threading.Event()

        # Convertir fin_time_str a un objeto datetime
        fin_time = datetime.strptime(fin_time_str, '%H:%M').time()
        now = datetime.now()
        fin_datetime = datetime.combine(now.date(), fin_time)
        # Si fin_datetime es anterior o igual a now, significa que la hora de fin es al día siguiente
        if fin_datetime <= now:
            fin_datetime += timedelta(days=1)

        # Diccionario para almacenar los resultados
        result = {}

        # Iniciar hilos
        valve_thread = threading.Thread(target=self.valve_control_thread, args=(camellon, stop_event))
        flow_thread = threading.Thread(target=self.flow_counting_thread, args=(volumen, stop_event, fin_datetime, result))
        fertilizer_thread = threading.Thread(target=self.fertilizer_injection_thread, args=(fertilizante1, fertilizante2, stop_event))

        # Iniciar los hilos
        valve_thread.start()
        flow_thread.start()
        fertilizer_thread.start()

        # Esperar a que el hilo de conteo de flujo termine (volumen alcanzado o tiempo finalizado)
        flow_thread.join()

        # Señalar a los otros hilos que deben detenerse
        stop_event.set()

        # Esperar a que los demás hilos terminen
        valve_thread.join()
        fertilizer_thread.join()

        logging.debug(f"Riego en camellón {camellon} finalizado.")

        # Devolver los resultados
        return result


    def is_valid_camellon(self, camellon_number):
        """
        Verifica si el camellon_number es válido según la configuración.
        """
        if 'camellones_indices' in self.gpio_config and camellon_number in self.gpio_config['camellones_indices']:
            return True
        elif 'camellonesLogicaNegativa_indices' in self.gpio_config and camellon_number in self.gpio_config['camellonesLogicaNegativa_indices']:
            return True
        else:
            return False 
            
    def valve_control_thread(self, camellon, stop_event):
        """
        Hilo que controla la válvula del camellón.
        """
        self.control_valve(camellon, 'ON')
        logging.debug(f"Válvula del camellón {camellon} abierta.")
        stop_event.wait()  # Espera hasta que se establezca el evento de parada
        self.control_valve(camellon, 'OFF')
        logging.debug(f"Válvula del camellón {camellon} cerrada.")

    def flow_counting_thread(self, volumen_objetivo, stop_event, fin_datetime, result):
        """
        Hilo que cuenta el flujo y establece el evento de parada cuando se alcanza el volumen o el tiempo.
        """
        volumen_actual = 0
        try:
            FACTOR_CONVERSION_FLUJO = float(os.environ.get('FACTOR_CONVERSION_FLUJO', '0.04'))
        except ValueError:
            logging.error("FACTOR_CONVERSION_FLUJO no es un número válido. Usando valor por defecto 0.04")
            FACTOR_CONVERSION_FLUJO = 0.04

        logging.debug(f"Inicio de conteo de flujo. Volumen objetivo: {volumen_objetivo}.")

        # Capturar el tiempo de inicio
        start_time = datetime.now()

        while not stop_event.is_set():
            flujos = self.read_flow_counts()
            flujo = flujos[0] + flujos[1]  # Leer el flujo correspondiente
            volumen_actual += flujo * FACTOR_CONVERSION_FLUJO
            logging.debug(f"Volumen actual: {volumen_actual}.")

            # Verificar si se alcanzó el volumen objetivo
            if volumen_actual >= volumen_objetivo:
                logging.debug(f"Volumen objetivo alcanzado: {volumen_actual}.")
                break

            # Verificar si se alcanzó la hora de finalización
            now = datetime.now()
            if now >= fin_datetime:
                logging.debug(f"Tiempo de riego alcanzado. Hora actual: {now}.")
                break

            time.sleep(1)

        # Capturar el tiempo de finalización
        end_time = datetime.now()
        tiempo_riego = (end_time - start_time).total_seconds()

        # Almacenar los resultados en el diccionario compartido
        result['volumen_actual'] = volumen_actual
        result['tiempo_riego'] = tiempo_riego

        # Señalar a los otros hilos que deben detenerse
        stop_event.set()

    def fertilizer_injection_thread(self, fertilizante1, fertilizante2, stop_event):
        """
        Hilo que controla la inyección de fertilizantes durante el riego.
        """
        # Dividir fertilizante1 y fertilizante2 entre 10 para definir los ciclos de trabajo
        duty_cycle1 = fertilizante1 / 10.0  # Convertir a un valor entre 0.0 y 1.0
        duty_cycle2 = fertilizante2 / 10.0  # Convertir a un valor entre 0.0 y 1.0

        # Cargar frecuencias PWM desde las variables de entorno o usar valores por defecto
        try:
            frequency1 = float(os.environ.get('PWM_FERTILIZER1_FREQUENCY', '0.1'))
        except ValueError:
            logging.error("Valor de frecuencia de fertilizante 1 no válido. Usando valor por defecto.")
            frequency1 = 1.0

        try:
            frequency2 = float(os.environ.get('PWM_FERTILIZER2_FREQUENCY', '0.1'))
        except ValueError:
            logging.error("Valor de frecuencia de fertilizante 2 no válido. Usando valor por defecto.")
            frequency2 = 1.0

        # Cálculo de periodos
        period1 = 1.0 / frequency1 if frequency1 > 0 else 1.0
        on_time1 = duty_cycle1 * period1
        off_time1 = period1 - on_time1

        period2 = 1.0 / frequency2 if frequency2 > 0 else 1.0
        on_time2 = duty_cycle2 * period2
        off_time2 = period2 - on_time2

        logging.debug(f"Iniciando inyección de fertilizantes con PWM.")

        # Definir funciones internas para cada fertilizante
        def pwm_fertilizer1():
            if fertilizante1 > 0:
                logging.debug("Iniciando PWM para fertilizante 1.")
                while not stop_event.is_set():
                    self.control_injector(1, 'ON')
                    time.sleep(on_time1)
                    self.control_injector(1, 'OFF')
                    time.sleep(off_time1)
                # Asegurar que el inyector esté apagado
                self.control_injector(1, 'OFF')
                logging.debug("Finalizando PWM para fertilizante 1.")

        def pwm_fertilizer2():
            if fertilizante2 > 0:
                logging.debug("Iniciando PWM para fertilizante 2.")
                while not stop_event.is_set():
                    self.control_injector(2, 'ON')
                    time.sleep(on_time2)
                    self.control_injector(2, 'OFF')
                    time.sleep(off_time2)
                # Asegurar que el inyector esté apagado
                self.control_injector(2, 'OFF')
                logging.debug("Finalizando PWM para fertilizante 2.")

        # Iniciar hilos para cada fertilizante
        threads = []
        if fertilizante1 > 0:
            thread1 = threading.Thread(target=pwm_fertilizer1)
            threads.append(thread1)
            thread1.start()
        if fertilizante2 > 0:
            thread2 = threading.Thread(target=pwm_fertilizer2)
            threads.append(thread2)
            thread2.start()

        # Esperar a que se establezca el evento de parada
        stop_event.wait()

        # Esperar a que los hilos PWM terminen
        for t in threads:
            t.join()

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
            self.control_tank_valve("ON")
            if logic == 'positive':
                self.pi.write(pin, 1)
            else:
                self.pi.write(pin, 0)
        elif action == 'OFF':
            self.control_tank_valve("OFF")
            if logic == 'positive':
                self.pi.write(pin, 0)
            else:
                self.pi.write(pin, 1)
        else:
            self.control_tank_valve("OFF")
            logging.error(f"Acción inválida para control de válvula: {action}")
            raise ValueError(f"Camellón {camellon_number} no encontrado en la configuración.")



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