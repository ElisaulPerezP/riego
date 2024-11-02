# scheduler.py

from datetime import datetime, timedelta
import json
import time

class Scheduler:
    def __init__(self, programa_actual):
        self.programa_actual = programa_actual
        self.cronograma_actividades = {}
        self.franjas_horarias = {}
        self.porcentajes = []
        self.volumen_total = 0
        self.intervalo_irrigacion_minutos = 5  # Puedes ajustar este valor si es necesario
        self.maximo_irrigaciones_hora = 5  # Puedes ajustar este valor si es necesario

    def generate_cronograma(self):
        """
        Genera el cronograma de actividades de riego basado en el programa actual.
        """
        # Paso 1: Calcular las franjas horarias según veces_por_dia
        self.calculate_time_slots()

        # Paso 2: Calcular los porcentajes de volumen para cada camellón
        self.calculate_volumes_percentage()

        # Paso 3: Asignar actividades a las franjas horarias
        self.assign_activities_to_slots()

        # Paso 4: Guardar el cronograma en un archivo (opcional)
        self.save_cronograma_actividades()

    def calculate_time_slots(self):
        """
        Calcula las franjas horarias según 'veces_por_dia'.
        """
        veces_por_dia = self.programa_actual.get('veces_por_dia', 1)
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
            volumen = self.programa_actual.get(f'volumen{i}', 0)
            self.volumen_total += volumen

        if self.volumen_total == 0:
            print("Advertencia: El volumen total es 0.")
            self.porcentajes = [0] * 14
            return

        for i in range(1, 15):
            volumen = self.programa_actual.get(f'volumen{i}', 0)
            porcentaje = volumen / self.volumen_total
            self.porcentajes.append(porcentaje)

    def assign_activities_to_slots(self):
        """
        Asigna actividades de riego a cada franja horaria y camellón.
        """
        self.cronograma_actividades = []
        for slot_id, franja in self.franjas_horarias.items():
            inicio_franja = datetime.strptime(franja['inicio'], '%H:%M')
            fin_franja = datetime.strptime(franja['fin'], '%H:%M')
            duracion_franja = (fin_franja - inicio_franja).total_seconds()
            tiempo_agendado = 0

            for i, porcentaje in enumerate(self.porcentajes):
                if porcentaje == 0:
                    continue

                duracion_riego = duracion_franja * porcentaje
                inicio_riego = inicio_franja + timedelta(seconds=tiempo_agendado)
                fin_riego = inicio_riego + timedelta(seconds=duracion_riego)

                # Obtener fertilizantes para el camellón actual
                fertilizante1 = self.programa_actual.get(f'fertilizante1_{i+1}', 0)
                fertilizante2 = self.programa_actual.get(f'fertilizante2_{i+1}', 0)

                actividad = {
                    'inicio': inicio_riego.strftime('%H:%M'),
                    'fin': fin_riego.strftime('%H:%M'),
                    'accion': {
                        'camellon': i + 1,
                        'volumen': self.programa_actual.get(f'volumen{i+1}', 0),
                        'fertilizante1': fertilizante1,
                        'fertilizante2': fertilizante2
                    }
                }

                self.cronograma_actividades.append(actividad)
                tiempo_agendado += duracion_riego

            tiempo_agendado = 0  # Reiniciar para la siguiente franja horaria

    def save_cronograma_actividades(self):
        """
        Guarda el cronograma de actividades en un archivo JSON.
        """
        try:
            with open('cronograma_actividades.json', 'w') as f:
                json.dump(self.cronograma_actividades, f, indent=4)
            print("Cronograma de actividades guardado exitosamente.")
        except IOError as e:
            print(f"Error al guardar el cronograma de actividades: {e}")

    def load_cronograma_actividades(self):
        """
        Carga el cronograma de actividades desde un archivo JSON.
        """
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
        while True:
            now = datetime.now().strftime('%H:%M')
            for actividad in self.cronograma_actividades:
                if actividad['inicio'] == now:
                    # Ejecutar la acción programada
                    self.execute_action(actividad['accion'])
            # Esperar un minuto antes de volver a comprobar
            time.sleep(60)

    def execute_action(self, accion):
        """
        Ejecuta una acción de riego en un camellón específico.
        """
        camellon = accion['camellon']
        volumen = accion['volumen']
        fertilizante1 = accion['fertilizante1']
        fertilizante2 = accion['fertilizante2']

        # Aquí llamarías a métodos del GPIOManager para controlar los pines
        # Por ejemplo:
        # self.gpio_manager.control_valve(camellon, 'ON')
        # time.sleep(duracion_riego)
        # self.gpio_manager.control_valve(camellon, 'OFF')

        print(f"Ejecutando riego en camellón {camellon} con volumen {volumen}.")

    def get_last_event(self):
        """
        Obtiene el último evento de riego ejecutado para reportarlo.
        """
        # Esta función debería obtener los datos del último riego para reportar
        # Deberías mantener un registro de los eventos ejecutados
        pass
