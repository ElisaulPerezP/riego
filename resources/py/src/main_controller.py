# main_controller.py

import threading
import time
from config_loader import ConfigLoader
from communication_manager import CommunicationManager
from scheduler import Scheduler
from gpio_manager import GPIOManager
from datetime import datetime


class MainController:
    def __init__(self):
        # Inicializar componentes
        self.config_loader = ConfigLoader()
        self.communication_manager = None
        self.gpio_manager = None
        self.flags = {
            'flagArchivoProgramaActual': False,
            'flagArchivoCronogramaActividades': False,
            'flagArchivoCronogramaComunicaciones': False,
            'flagArchivoGPIO': False,
            'flagArchivoDirecciones': False,
            'flagArchivoLogs': False,
            'flagProgramaObtenido': False,
            'flagProgramaListo': False,
            'flagCronogramaListo': False
        }
        self.scheduler = Scheduler(self.config_loader.programa_actual)


    def start(self):
        # Inicializar los componentes
        self.initialize_components()

        # Verificar que el programa y el cronograma estén listos
        if not self.flags['flagProgramaListo']:
            print("Error: El programa no está listo. No se puede iniciar el controlador.")
            return

        if not self.flags['flagCronogramaListo']:
            print("Error: El cronograma no está listo. No se puede iniciar el controlador.")
            return

        # Iniciar los hilos
        hilo_gpio = threading.Thread(target=self.gpio_manager.run)
        hilo_comunicacion = threading.Thread(target=self.communication_loop)
        hilo_scheduler = threading.Thread(target=self.scheduler.run)
        hilo_emergency_stop = threading.Thread(target=self.gpio_manager.monitor_emergency_stop)

        # Establecer los hilos como demonios
        hilo_gpio.daemon = True
        hilo_comunicacion.daemon = True
        hilo_scheduler.daemon = True
        hilo_emergency_stop.daemon = True

        # Iniciar los hilos
        hilo_gpio.start()
        hilo_comunicacion.start()
        hilo_scheduler.start()
        hilo_emergency_stop.start()

        # Mantener el programa en ejecución
        try:
            while True:
                time.sleep(1)
        except KeyboardInterrupt:
            print("Deteniendo el controlador principal.")

    def initialize_components(self):
        # Intentar cargar los archivos de configuración
        self.flags['flagArchivoProgramaActual'] = self.config_loader.load_programa_actual()
        self.flags['flagArchivoCronogramaActividades'] = self.config_loader.load_cronograma_actividades()
        self.flags['flagArchivoCronogramaComunicaciones'] = self.config_loader.load_cronograma_comunicaciones()
        self.flags['flagArchivoGPIO'] = self.config_loader.load_gpio_config()
        self.flags['flagArchivoDirecciones'] = self.config_loader.load_api_config()
        self.flags['flagArchivoLogs'] = self.config_loader.load_logs()
        
        # Inicializar GPIOManager si se cargó la configuración GPIO
        if self.flags['flagArchivoGPIO']:
            self.gpio_manager = GPIOManager(self.config_loader.gpio_config)
        else:
            print("Error: No se pudo cargar la configuración de GPIO.")

        # Inicializar CommunicationManager si se cargó la configuración de direcciones
        if self.flags['flagArchivoDirecciones']:
            self.communication_manager = CommunicationManager(self.config_loader.api_config)
        else:
            print("Error: No se pudo cargar la configuración de direcciones.")

        # Determinar algun programa para gestionar el riego, si encuetra un archivo lo usa
        if self.flags['flagArchivoProgramaActual']:
            self.programa_actual = self.config_loader.programa_actual
        else:
            # Intenta obtener el programa de la aplicacion
            self.attempt_communication()

        # Preparar las banderas según la lógica definida
        self.prepare_flags()

        # Inicializar el Scheduler
        self.scheduler = Scheduler(self.programa_actual)

        # Generar el cronograma si es necesario
        if not self.flags['flagCronogramaListo'] and self.flags['flagArchivoProgramaActual']:
            self.flags['flagCronogramaListo'] = self.scheduler.generate_cronograma()



    def attempt_communication(self):
        # Intentar establecer comunicación para obtener el programa actual
        self.flags['flagProgramaObtenido'] = self.communication_manager.obtain_programa_actual()
        if self.flags['flagProgramaObtenido']:
            self.programa_actual = self.communication_manager.programa_obtenido
        else:
            print("Error: No se pudo obtener el programa actual de riego.")

    def prepare_flags(self):
        # Preparación de banderas según los casos especificados
        flagArchivoProgramaActual = self.flags['flagArchivoProgramaActual']
        flagProgramaObtenido = self.flags['flagProgramaObtenido']
        programa_actual = self.config_loader.programa_actual
        programa_obtenido = self.communication_manager.programa_obtenido

        if flagArchivoProgramaActual and flagProgramaObtenido:
            if programa_actual == programa_obtenido:
                self.flags['flagProgramaListo'] = True
                if self.flags['flagArchivoCronogramaActividades']:
                    self.flags['flagCronogramaListo'] = True
                else:
                    self.flags['flagCronogramaListo'] = False
            else:
                # Actualizar programa_actual con programa_obtenido
                self.config_loader.programa_actual = programa_obtenido
                self.flags['flagProgramaListo'] = True

                self.flags['flagCronogramaListo'] = False       
        elif flagArchivoProgramaActual and not flagProgramaObtenido:
            self.flags['flagProgramaListo'] = True

            if self.flags['flagArchivoCronogramaActividades']:
                self.flags['flagCronogramaListo'] = True
            else:
                self.flags['flagCronogramaListo'] = False
        elif not flagArchivoProgramaActual and flagProgramaObtenido:
            # Actualizar programa_actual con programa_obtenido
            self.config_loader.programa_actual = programa_obtenido
            self.flags['flagProgramaListo'] = True
            self.flags['flagCronogramaListo'] = False
        else:
            print("Error: No se pudo obtener o cargar el programa actual de riego.")

    def communication_loop(self):
        # Bucle para manejar las comunicaciones según el cronograma
        while True:
            current_time = datetime.now().strftime("%H:%M")
            for evento in self.config_loader.cronograma_comunicaciones:
                inicio = evento['inicio']
                finalizacion = evento['finalizacion']
                accion = evento['accion']
                if inicio <= current_time <= finalizacion:
                    if accion == "consultarPrograma":
                        self.attempt_communication()
                        self.prepare_flags()
                    elif accion == "reportarRiego":
                        # Obtener el último evento de riego y reportarlo
                        evento_riego = self.scheduler.get_last_event()
                        self.communication_manager.report_event(evento_riego)
            time.sleep(60)  # Esperar un minuto antes de volver a comprobar
            
if __name__ == '__main__':
    controller = MainController()
    controller.start()