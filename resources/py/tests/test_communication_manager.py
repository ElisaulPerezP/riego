# tests/test_communication_manager.py

import unittest
from unittest.mock import patch, MagicMock
import requests

from communication_manager import CommunicationManager

class TestCommunicationManager(unittest.TestCase):
    def setUp(self):
        self.api_config = {
            'url_obtener_programa': 'http://example.com/api/obtener_programa',
            'url_reportar_evento': 'http://example.com/api/reportar_evento'
        }
        self.comm_manager = CommunicationManager(self.api_config)

    @patch('requests.get')
    def test_obtain_programa_actual_success(self, mock_get):
        # Simular una respuesta exitosa
        mock_response = MagicMock()
        mock_response.status_code = 200
        mock_response.json.return_value = {'veces_por_dia': 3, 'volumen1': 100}
        mock_get.return_value = mock_response

        result = self.comm_manager.obtain_programa_actual()

        self.assertTrue(result)
        self.assertTrue(self.comm_manager.flagProgramaObtenido)
        self.assertEqual(self.comm_manager.programa_obtenido, {'veces_por_dia': 3, 'volumen1': 100})

    @patch('requests.get')
    def test_obtain_programa_actual_failure(self, mock_get):
        # Simular una respuesta con error
        mock_response = MagicMock()
        mock_response.status_code = 500
        mock_get.return_value = mock_response

        result = self.comm_manager.obtain_programa_actual()

        self.assertFalse(result)
        self.assertFalse(self.comm_manager.flagProgramaObtenido)

    @patch('requests.get')
    def test_obtain_programa_actual_exception(self, mock_get):
        # Simular una excepción durante la solicitud
        mock_get.side_effect = requests.exceptions.RequestException("Network error")

        result = self.comm_manager.obtain_programa_actual()

        self.assertFalse(result)
        self.assertFalse(self.comm_manager.flagProgramaObtenido)

    @patch('requests.post')
    def test_report_event_success(self, mock_post):
        # Simular una respuesta exitosa
        mock_response = MagicMock()
        mock_response.status_code = 200
        mock_post.return_value = mock_response

        evento_riego = {'volumen1': 100, 'tiempo1': 60, 'mensaje1': 'OK'}
        result = self.comm_manager.report_event(evento_riego)

        self.assertTrue(result)

    @patch('requests.post')
    def test_report_event_failure(self, mock_post):
        # Simular una respuesta con error
        mock_response = MagicMock()
        mock_response.status_code = 500
        mock_post.return_value = mock_response

        evento_riego = {'volumen1': 100, 'tiempo1': 60, 'mensaje1': 'OK'}
        result = self.comm_manager.report_event(evento_riego)

        self.assertFalse(result)

    @patch('requests.post')
    def test_report_event_exception(self, mock_post):
        # Simular una excepción durante la solicitud
        mock_post.side_effect = requests.exceptions.RequestException("Network error")

        evento_riego = {'volumen1': 100, 'tiempo1': 60, 'mensaje1': 'OK'}
        result = self.comm_manager.report_event(evento_riego)

        self.assertFalse(result)

if __name__ == '__main__':
    unittest.main()
