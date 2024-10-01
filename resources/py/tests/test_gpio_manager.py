# tests/test_gpio_manager.py

import unittest
from unittest.mock import MagicMock, patch

from gpio_manager import GPIOManager

class TestGPIOManager(unittest.TestCase):
    @patch('pigpio.pi')
    def setUp(self, mock_pigpio_pi):
        # Crear un mock para pigpio.pi()
        self.mock_pi = MagicMock()
        self.mock_pi.connected = True
        mock_pigpio_pi.return_value = self.mock_pi

        # Configuración GPIO simulada
        self.gpio_config = {
            'fluxometros': [20, 21],
            'parada': 24,
            'nivel': [22, 23],
            'inyectoresLogicaNegativa': [2, 3],
            'camellonesLogicaNegativa': [10, 9, 27, 6, 5],
            'camellones': [11, 14, 13, 12],
            'valvulaTanquesLogicaNegativa': 4,
            'motobombas': [15, 16]
        }

        self.gpio_manager = GPIOManager(self.gpio_config)


    def test_setup_pins(self):
        # Verificar que se hayan configurado los pines correctamente
        calls = [((pin, ), {}) for pin in self.gpio_config['camellones'] + self.gpio_config['camellonesLogicaNegativa']]
        self.mock_pi.set_mode.assert_has_calls(calls, any_order=True)

    def test_control_valve_positive_logic(self):
        # Controlar una válvula de lógica positiva
        self.gpio_manager.control_valve(6, 'ON')
        pin = self.gpio_config['camellones'][0]
        self.mock_pi.write.assert_any_call(pin, 1)

        self.gpio_manager.control_valve(6, 'OFF')
        self.mock_pi.write.assert_any_call(pin, 0)

    def test_control_valve_negative_logic(self):
        # Controlar una válvula de lógica negativa
        self.gpio_manager.control_valve(1, 'ON')
        pin = self.gpio_config['camellonesLogicaNegativa'][0]
        self.mock_pi.write.assert_any_call(pin, 0)

        self.gpio_manager.control_valve(1, 'OFF')
        self.mock_pi.write.assert_any_call(pin, 1)

    def test_monitor_emergency_stop(self):
        # Simular el estado del botón de parada de emergencia
        self.mock_pi.read.return_value = 0  # Botón presionado (PULL_UP)

        # Limitar el número de veces que se monitorea el botón
        with patch('threading.Thread') as mock_thread:
            # Forzamos que el monitoreo ocurra solo una vez
            self.gpio_manager.monitor_emergency_stop = MagicMock(side_effect=lambda: self.mock_pi.read.return_value == 0)
            self.gpio_manager.monitor_emergency_stop()

            # Verificar que el estado de emergencia cambie solo una vez
            self.assertTrue(self.gpio_manager.emergency_stop)

            # Verificar que se haya impreso el mensaje esperado
            self.gpio_manager.monitor_emergency_stop.assert_called_once()

    def test_read_flow_counts(self):
        # Simular incrementos en los contadores de flujo
        self.gpio_manager.flow_counts = [10, 15]
        counts = self.gpio_manager.read_flow_counts()
        self.assertEqual(counts, [10, 15])
        self.assertEqual(self.gpio_manager.flow_counts, [0, 0])

    def test_stop_all_actuators(self):
        self.gpio_manager.stop_all_actuators()
        # Verificar que se haya llamado a write para todos los actuadores
        for pin in self.gpio_config['camellones']:
            self.mock_pi.write.assert_any_call(pin, 0)
        for pin in self.gpio_config['camellonesLogicaNegativa']:
            self.mock_pi.write.assert_any_call(pin, 1)
        for pin in self.gpio_config['inyectoresLogicaNegativa']:
            self.mock_pi.write.assert_any_call(pin, 1)
        for pin in self.gpio_config['motobombas']:
            self.mock_pi.write.assert_any_call(pin, 0)
        self.mock_pi.write.assert_any_call(self.gpio_config['valvulaTanquesLogicaNegativa'], 1)

if __name__ == '__main__':
    unittest.main()
