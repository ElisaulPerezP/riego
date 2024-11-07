# communication_manager.py

import requests
import json
from datetime import datetime
import logging


# Configuración del sistema de logging
logging.basicConfig(
    filename='comunication.log',  # Archivo donde se guardarán los logs
    filemode='a',        # 'a' para anexar, 'w' para sobrescribir
    level=logging.DEBUG,  # Nivel mínimo de mensajes a registrar
    format='%(asctime)s - %(levelname)s - %(message)s'  # Formato de salida
)


class CommunicationManager:
    def __init__(self, api_config):
        self.api_config = api_config  # Configuración de las direcciones de API
        self.programa_obtenido = None
        self.flagProgramaObtenido = False

    def obtain_programa_actual(self):
        """
        Intenta obtener el programa actual de riego desde el servidor remoto.
        """
        try:
            # Construir la URL completa para obtener el programa
            url = f"{self.api_config['base_url']}{self.api_config['endpoints']['url_obtener_programa']}"
            headers = {'Content-Type': 'application/json'}
            response = requests.get(url, headers=headers, timeout=10)
            if response.status_code == 200:
                self.programa_obtenido = response.json()
                self.save_programa_actual(self.programa_obtenido )
                self.flagProgramaObtenido = True
                return True
            else:
                print(f"Error al obtener el programa: Código {response.status_code}")
                self.flagProgramaObtenido = False
                return False
        except requests.exceptions.RequestException as e:
            print(f"Excepción en la comunicación: {e}")
            self.flagProgramaObtenido = False
            return False

    def report_event(self, evento_riego):
        """
        Reporta un evento de riego al servidor remoto.
        """
        if evento_riego is None:
            print("Advertencia: No hay evento de riego para reportar.")
            return False

        try:
            url = f"{self.api_config['base_url']}{self.api_config['endpoints']['reportes_store']}"
            payload = self.construct_event_payload(evento_riego)
            logging.debug(f"el payload es:  {payload}")
            # Enviar la solicitud con los datos en formato JSON
            response = requests.post(url, json=payload)

            if response.status_code == 201:
                logging.debug(f"Evento reportado con éxito, payload: {payload}")
                return True
            else:
                logging.error(f"Error al reportar el evento: código de respuesta {response}")
                logging.error(f"Error en los headers de la redireccion son: {response.headers}")
                logging.error(f"Error en el texto de la redireccion es: {response.text}")
                return False
        except requests.exceptions.RequestException as e:
            logging.error(f"Error al reportar el evento {e}")
            return False


    def construct_event_payload(self, evento_riego):
        """
        Construye el payload del evento de riego para enviarlo al servidor.

        :param evento_riego: Diccionario con los datos del evento.
        :return: Diccionario con el payload listo para enviar.
        """
        payload = {
            # Campos de volumen para los 14 surcos
            'volumen1': evento_riego.get('volumen1', 0),
            'volumen2': evento_riego.get('volumen2', 0),
            'volumen3': evento_riego.get('volumen3', 0),
            'volumen4': evento_riego.get('volumen4', 0),
            'volumen5': evento_riego.get('volumen5', 0),
            'volumen6': evento_riego.get('volumen6', 0),
            'volumen7': evento_riego.get('volumen7', 0),
            'volumen8': evento_riego.get('volumen8', 0),
            'volumen9': evento_riego.get('volumen9', 0),
            'volumen10': evento_riego.get('volumen10', 0),
            'volumen11': evento_riego.get('volumen11', 0),
            'volumen12': evento_riego.get('volumen12', 0),
            'volumen13': evento_riego.get('volumen13', 0),
            'volumen14': evento_riego.get('volumen14', 0),

            # Campos de tiempo para los 14 surcos
            'tiempo1': evento_riego.get('tiempo1', 0),
            'tiempo2': evento_riego.get('tiempo2', 0),
            'tiempo3': evento_riego.get('tiempo3', 0),
            'tiempo4': evento_riego.get('tiempo4', 0),
            'tiempo5': evento_riego.get('tiempo5', 0),
            'tiempo6': evento_riego.get('tiempo6', 0),
            'tiempo7': evento_riego.get('tiempo7', 0),
            'tiempo8': evento_riego.get('tiempo8', 0),
            'tiempo9': evento_riego.get('tiempo9', 0),
            'tiempo10': evento_riego.get('tiempo10', 0),
            'tiempo11': evento_riego.get('tiempo11', 0),
            'tiempo12': evento_riego.get('tiempo12', 0),
            'tiempo13': evento_riego.get('tiempo13', 0),
            'tiempo14': evento_riego.get('tiempo14', 0),

            # Campos de mensaje para los 14 surcos
            'mensaje1': evento_riego.get('mensaje1', ''),
            'mensaje2': evento_riego.get('mensaje2', ''),
            'mensaje3': evento_riego.get('mensaje3', ''),
            'mensaje4': evento_riego.get('mensaje4', ''),
            'mensaje5': evento_riego.get('mensaje5', ''),
            'mensaje6': evento_riego.get('mensaje6', ''),
            'mensaje7': evento_riego.get('mensaje7', ''),
            'mensaje8': evento_riego.get('mensaje8', ''),
            'mensaje9': evento_riego.get('mensaje9', ''),
            'mensaje10': evento_riego.get('mensaje10', ''),
            'mensaje11': evento_riego.get('mensaje11', ''),
            'mensaje12': evento_riego.get('mensaje12', ''),
            'mensaje13': evento_riego.get('mensaje13', ''),
            'mensaje14': evento_riego.get('mensaje14', ''),
        }
        return payload


    def construct_programa_payload(self, programa_data):
        """
        Construye el payload del programa de riego para guardarlo o compararlo.

        :param programa_data: Diccionario con los datos del programa.
        :return: Diccionario con el programa estructurado.
        """
        programa = {
            'veces_por_dia': programa_data.get('veces_por_dia', 1),
            # Volúmenes
            'volumen1': programa_data.get('volumen1', 0),
            'volumen2': programa_data.get('volumen2', 0),
            'volumen3': programa_data.get('volumen3', 0),
            'volumen4': programa_data.get('volumen4', 0),
            'volumen5': programa_data.get('volumen5', 0),
            'volumen6': programa_data.get('volumen6', 0),
            'volumen7': programa_data.get('volumen7', 0),
            'volumen8': programa_data.get('volumen8', 0),
            'volumen9': programa_data.get('volumen9', 0),
            'volumen10': programa_data.get('volumen10', 0),
            'volumen11': programa_data.get('volumen11', 0),
            'volumen12': programa_data.get('volumen12', 0),
            'volumen13': programa_data.get('volumen13', 0),
            'volumen14': programa_data.get('volumen14', 0),
            # Fertilizantes 1
            'fertilizante1_1': programa_data.get('fertilizante1_1', 0),
            'fertilizante1_2': programa_data.get('fertilizante1_2', 0),
            'fertilizante1_3': programa_data.get('fertilizante1_3', 0),
            'fertilizante1_4': programa_data.get('fertilizante1_4', 0),
            'fertilizante1_5': programa_data.get('fertilizante1_5', 0),
            'fertilizante1_6': programa_data.get('fertilizante1_6', 0),
            'fertilizante1_7': programa_data.get('fertilizante1_7', 0),
            'fertilizante1_8': programa_data.get('fertilizante1_8', 0),
            'fertilizante1_9': programa_data.get('fertilizante1_9', 0),
            'fertilizante1_10': programa_data.get('fertilizante1_10', 0),
            'fertilizante1_11': programa_data.get('fertilizante1_11', 0),
            'fertilizante1_12': programa_data.get('fertilizante1_12', 0),
            'fertilizante1_13': programa_data.get('fertilizante1_13', 0),
            'fertilizante1_14': programa_data.get('fertilizante1_14', 0),
            # Fertilizantes 2
            'fertilizante2_1': programa_data.get('fertilizante2_1', 0),
            'fertilizante2_2': programa_data.get('fertilizante2_2', 0),
            'fertilizante2_3': programa_data.get('fertilizante2_3', 0),
            'fertilizante2_4': programa_data.get('fertilizante2_4', 0),
            'fertilizante2_5': programa_data.get('fertilizante2_5', 0),
            'fertilizante2_6': programa_data.get('fertilizante2_6', 0),
            'fertilizante2_7': programa_data.get('fertilizante2_7', 0),
            'fertilizante2_8': programa_data.get('fertilizante2_8', 0),
            'fertilizante2_9': programa_data.get('fertilizante2_9', 0),
            'fertilizante2_10': programa_data.get('fertilizante2_10', 0),
            'fertilizante2_11': programa_data.get('fertilizante2_11', 0),
            'fertilizante2_12': programa_data.get('fertilizante2_12', 0),
            'fertilizante2_13': programa_data.get('fertilizante2_13', 0),
            'fertilizante2_14': programa_data.get('fertilizante2_14', 0),
        }
        return programa

    def compare_programs(self, programa1, programa2):
        """
        Compara dos programas de riego para determinar si son iguales.

        :param programa1: Diccionario del primer programa.
        :param programa2: Diccionario del segundo programa.
        :return: True si son iguales, False si son diferentes.
        """
        return programa1 == programa2

    def save_programa_actual(self, programa):
        """
        Guarda el programa actual en un archivo local.

        :param programa: Diccionario con el programa de riego.
        """
        try:
            with open('programa_actual.json', 'w') as f:
                json.dump(programa, f)
            print("Programa actual guardado localmente.")
        except IOError as e:
            print(f"Error al guardar el programa actual: {e}")

    def load_programa_actual(self):
        """
        Carga el programa actual desde un archivo local.

        :return: Diccionario con el programa de riego o None si falla.
        """
        try:
            with open('programa_actual.json', 'r') as f:
                programa = json.load(f)
            return programa
        except IOError:
            return None

    def update_programa_actual(self, programa_nuevo):
        """
        Actualiza el programa actual localmente.

        :param programa_nuevo: Diccionario con el nuevo programa de riego.
        """
        self.save_programa_actual(programa_nuevo)

    def get_programa_obtenido(self):
        """
        Devuelve el programa obtenido después de una comunicación exitosa.

        :return: Diccionario con el programa de riego o None.
        """
        return self.programa_obtenido

    def get_flag_programa_obtenido(self):
        """
        Devuelve el estado de la bandera de programa obtenido.

        :return: True si el programa fue obtenido, False en caso contrario.
        """
        return self.flagProgramaObtenido
