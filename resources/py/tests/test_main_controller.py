# tests/test_main_controller.py

import unittest
from unittest.mock import MagicMock, patch

from main_controller import MainController

class TestMainController(unittest.TestCase):
    @patch('main_controller.ConfigLoader')
    @patch('main_controller.GPIOManager')
    @patch('main_controller.CommunicationManager')
    @patch('main_controller.Scheduler')
    def test_initialize_components(self, mock_scheduler, mock_comm_manager, mock_gpio_manager, mock_config_loader):
        # Configurar los mocks
        mock_loader_instance = MagicMock()
        mock_loader_instance.flags = {
            'flagArchivoProgramaActual': True,
            'flagArchivoCronogramaActividades': True,
            'flagArchivoCronogramaComunicaciones': True,
            'flagArchivoGPIO': True,
            'flagArchivoDirecciones': True,
            'flagArchivoLogs': True
        }
        mock_loader_instance.get_error_messages.return_value = []
        mock_config_loader.return_value = mock_loader_instance

        # Crear instancia de MainController
        controller = MainController()
        controller.initialize_components()

        # Verificar que los componentes se hayan inicializado
        mock_config_loader.assert_called_once()
        mock_gpio_manager.assert_called_once_with(mock_loader_instance.gpio_config)
        mock_comm_manager.assert_called_once_with(mock_loader_instance.api_config)
        mock_scheduler.assert_called_once_with(mock_loader_instance.programa_actual)

    @patch('main_controller.threading.Thread')
    def test_start(self, mock_thread):
        controller = MainController()
        controller.initialize_components = MagicMock()
        controller.flags['flagProgramaListo'] = True
        controller.flags['flagCronogramaListo'] = True

        # Parchear time.sleep para evitar demoras
        with patch('time.sleep', return_value=None):
            # Simular KeyboardInterrupt para salir del bucle infinito
            with patch('builtins.input', side_effect=KeyboardInterrupt):
                try:
                    controller.start()
                except KeyboardInterrupt:
                    pass

        # Verificar que se llamaron a los hilos
        self.assertTrue(mock_thread.called)

if __name__ == '__main__':
    unittest.main()
