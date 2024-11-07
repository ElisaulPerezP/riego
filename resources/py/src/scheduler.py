from datetime import datetime, timedelta
import json
import threading
import time
import logging


# Configuración del sistema de logging
logging.basicConfig(
    filename='scheduler.log',  # Archivo donde se guardarán los logs
    filemode='a',        # 'a' para anexar, 'w' para sobrescribir
    level=logging.DEBUG,  # Nivel mínimo de mensajes a registrar
    format='%(asctime)s - %(levelname)s - %(message)s'  # Formato de salida
)


class Scheduler:
    def __init__(self, programa_actual, gpio_manager):
        self.programa_actual = programa_actual
        self.gpio_manager = gpio_manager  # Añadimos gpio_manager
        self.cronograma_actividades = []
        self.franjas_horarias = {}
        self.porcentajes = []
        self.volumen_total = 0
        self.intervalo_irrigacion_minutos = 5  # Puedes ajustar este valor si es necesario
        self.maximo_irrigaciones_hora = 5  # Puedes ajustar este valor si es necesario
        self.last_event = None  # Para registrar el último evento
        self.payload_event = {
            # Campos de volumen para los 14 surcos
            **{f'volumen{i}': 0 for i in range(1, 15)},
            # Campos de tiempo para los 14 surcos
            **{f'tiempo{i}': 0 for i in range(1, 15)},
            # Campos de fertilizantes para los 14 surcos (último valor registrado)
            **{f'fertilizante1_{i}': 0 for i in range(1, 15)},
            **{f'fertilizante2_{i}': 0 for i in range(1, 15)},
            # Campos de mensaje para los 14 surcos (inicialmente vacíos)
            **{f'mensaje{i}': '' for i in range(1, 15)}
        }
        logging.debug("Scheduler iniciado mediante su constructor")

    def generate_cronograma(self):
        """
        Genera el cronograma de actividades de riego basado en el programa actual.
        """
        try:
            # Paso 1: Calcular las franjas horarias según veces_por_dia
            self.calculate_time_slots()

            # Paso 2: Calcular los porcentajes de volumen para cada camellón
            self.calculate_volumes_percentage()

            # Paso 3: Asignar actividades a las franjas horarias
            self.assign_activities_to_slots()

            # Paso 4: Guardar el cronograma en un archivo (opcional)
            self.save_cronograma_actividades()

            logging.debug(f"Se genero el cronograma de actividades: {self.cronograma_actividades}")

            # Si todos los pasos se ejecutaron correctamente
            return True

        except Exception as e:
            logging.debug("no se genero el cronograma de actividades")

            print(f"Error al generar el cronograma de actividades: {e}")
            return False

    def calculate_time_slots(self):
        """
        Calcula las franjas horarias según 'veces_por_dia'.
        """
        veces_por_dia = self.programa_actual.get('programa_riego', {}).get('veces_por_dia', 1)
        interval_hours = 24 / veces_por_dia
        self.franjas_horarias = {}

        for i in range(veces_por_dia):
            inicio = datetime.strptime('00:00', '%H:%M') + timedelta(hours=interval_hours * i)
            fin = inicio + timedelta(hours=interval_hours - 0.01)  # Restamos un pequeño valor para evitar solapamiento
            franja = {
                'inicio': inicio.strftime('%H:%M'),
                'fin': fin.strftime('%H:%M')
            }
            self.franjas_horarias[i + 1] = franja

    def calculate_volumes_percentage(self):
        """
        Calcula el porcentaje de volumen de riego para cada camellón.
        """
        self.volumen_total = 0
        for i in range(1, 15):  # Camellones 1 a 14
            volumen = self.programa_actual.get('programa_riego', {}).get(f'volumen{i}', 0)
            self.volumen_total += volumen

        if self.volumen_total == 0:
            print("Advertencia: El volumen total es 0.")
            self.porcentajes = [0] * 14
            return

        for i in range(1, 15):
            volumen = self.programa_actual.get('programa_riego', {}).get(f'volumen{i}', 0)
            porcentaje = volumen / self.volumen_total
            self.porcentajes.append(porcentaje)

    def assign_activities_to_slots(self):
        """
        Asigna actividades de riego a cada franja horaria y camellón sin solapamientos.
        """
        self.cronograma_actividades = []
        veces_por_dia = self.programa_actual.get('programa_riego', {}).get('veces_por_dia', 1)

        for slot_id, franja in self.franjas_horarias.items():
            inicio_franja = datetime.strptime(franja['inicio'], '%H:%M')
            fin_franja = datetime.strptime(franja['fin'], '%H:%M')
            duracion_franja = fin_franja - inicio_franja

            # Obtener camellones a regar en esta franja
            camellones_a_regar = []
            for i in range(1, 15):  # Camellones 1 a 14
                volumen_total = self.programa_actual.get('programa_riego', {}).get(f'volumen{i}', 0)
                if volumen_total > 0:
                    camellones_a_regar.append(i)

            num_camellones = len(camellones_a_regar)
            if num_camellones == 0:
                continue  # No hay camellones para regar en esta franja horaria

            # Calcular duración por camellón
            duracion_por_camellon = duracion_franja / num_camellones

            for idx, i in enumerate(camellones_a_regar):
                volumen_total = self.programa_actual.get('programa_riego', {}).get(f'volumen{i}', 0)
                # Calcular volumen y fertilizantes por cada riego
                volumen_por_vez = volumen_total / veces_por_dia
                fertilizante1_total = self.programa_actual.get('programa_riego', {}).get(f'fertilizante1_{i}', 0)
                fertilizante2_total = self.programa_actual.get('programa_riego', {}).get(f'fertilizante2_{i}', 0)
                fertilizante1_por_vez = fertilizante1_total
                fertilizante2_por_vez = fertilizante2_total
                
                # Calcular inicio y fin para este camellón
                inicio_riego = inicio_franja + duracion_por_camellon * idx
                fin_riego = inicio_riego + duracion_por_camellon

                actividad = {
                    'inicio': inicio_riego.strftime('%H:%M'),
                    'fin': fin_riego.strftime('%H:%M'),
                    'accion': {
                        'camellon': i,
                        'volumen': volumen_por_vez,
                        'fertilizante1': fertilizante1_por_vez,
                        'fertilizante2': fertilizante2_por_vez
                    }
                }

                self.cronograma_actividades.append(actividad)

    def save_cronograma_actividades(self):
        """
        Guarda el cronograma de actividades en un archivo JSON y muestra su contenido.
        """
        try:
            with open('cronograma_actividades.json', 'w') as f:
                json.dump(self.cronograma_actividades, f, indent=4)
            print("Cronograma de actividades guardado exitosamente.")
            print("Contenido guardado en cronograma_actividades.json:")
            print(json.dumps(self.cronograma_actividades, indent=4))  # Muestra el contenido guardado en formato JSON
        
        except IOError as e:
            print(f"Error al guardar el cronograma de actividades: {e}")


    def load_cronograma_actividades(self):
        """
        Carga el cronograma de actividades desde un archivo JSON.
        """
        logging.debug("ingreso a load_cronograma_actividades")

        try:
            with open('cronograma_actividades.json', 'r') as f:
                self.cronograma_actividades = json.load(f)
            print("Cronograma de actividades cargado exitosamente.")
            return True
        except IOError:
            print("Error al cargar el cronograma de actividades.")
            return False


    def run(self):
        """
        Ejecuta las actividades programadas en el cronograma.
        """
        last_load_time = datetime.now() - timedelta(minutes=10)

        while True:
            now = datetime.now()
            current_time_str = now.strftime('%H:%M')

            # Verificar si han pasado al menos 10 minutos desde la última carga
            if (now - last_load_time).total_seconds() >= 600:
                self.load_cronograma_actividades()
                last_load_time = now
                logging.debug("Cronograma de actividades recargado en el Scheduler.")

            for actividad in self.cronograma_actividades:
                if actividad['inicio'] == current_time_str:
                    logging.debug("Se detectó el inicio de una actividad en el scheduler")
                    # Ejecutar la acción programada en un hilo separado
                    threading.Thread(target=self.execute_action, args=(actividad['accion'], actividad['fin'])).start()

            # Esperar un minuto antes de volver a comprobar
            time.sleep(60)


    def execute_action(self, accion, fin_time_str):
        """
        Ejecuta una acción de riego en un camellón específico y acumula la información en el payload.
        """
        logging.debug("Se está ejecutando una acción en el scheduler")

        camellon = accion['camellon']
        volumen = accion['volumen']
        fertilizante1 = accion['fertilizante1']
        fertilizante2 = accion['fertilizante2']

        try:
            # Ejecutar la acción de riego completa usando gpio_manager y pasar fin_time
            result = self.gpio_manager.accion_riego_completa(camellon, volumen, fertilizante1, fertilizante2, fin_time_str)

            # Obtener el tiempo y volumen reales
            volumen_real = result.get('volumen_actual', 0)
            tiempo_riego = result.get('tiempo_riego', 0)

            # Acumular los valores en el payload correspondiente al surco
            surco_index = camellon  # camellon se corresponde con el número de surco

            # Acumular volumen en el surco correspondiente
            self.payload_event[f'volumen{surco_index}'] += volumen_real
            # Sumar tiempo total en el surco correspondiente
            self.payload_event[f'tiempo{surco_index}'] += tiempo_riego
            # Actualizar fertilizante con el último valor usado
            self.payload_event[f'fertilizante1_{surco_index}'] = fertilizante1
            self.payload_event[f'fertilizante2_{surco_index}'] = fertilizante2

            logging.debug(f"Acción ejecutada exitosamente en camellón {camellon}")

        except Exception as e:
            logging.error(f"Error al ejecutar la acción en camellón {camellon}: {e}")

    def get_payload_event(self):
        """
        Devuelve una copia del payload actual que contiene los volúmenes, tiempos y fertilizantes por surco.
        
        :return: Diccionario con el payload actual.
        """
        return self.payload_event.copy()

    def delete_payload_event(self):
        """
        Restablece todos los valores del payload a sus valores iniciales.
        """
        self.payload_event = {
            # Campos de volumen para los 14 surcos
            **{f'volumen{i}': 0 for i in range(1, 15)},
            # Campos de tiempo para los 14 surcos
            **{f'tiempo{i}': 0 for i in range(1, 15)},
            # Campos de fertilizantes para los 14 surcos (último valor registrado)
            **{f'fertilizante1_{i}': 0 for i in range(1, 15)},
            **{f'fertilizante2_{i}': 0 for i in range(1, 15)},
            # Campos de mensaje para los 14 surcos (inicialmente vacíos)
            **{f'mensaje{i}': '' for i in range(1, 15)}
        }
        logging.debug("Payload de eventos eliminado y restablecido a valores iniciales.")
