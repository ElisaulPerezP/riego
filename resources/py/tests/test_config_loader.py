# tests/test_config_loader.py

import unittest
from unittest.mock import patch, mock_open, MagicMock
import json
import os

from config_loader import ConfigLoader

class TestConfigLoader(unittest.TestCase):
    def setUp(self):
        self.loader = ConfigLoader()
        self.mock_os_path_isfile = patch('os.path.isfile').start()
        self.addCleanup(patch.stopall)

    def test_load_programa_actual_success(self):
        # Simular que el archivo existe
        self.mock_os_path_isfile.return_value = True

        # Contenido simulado del archivo
        programa_data = {'veces_por_dia': 3, 'volumen1': 100}
        mock_file = mock_open(read_data=json.dumps(programa_data))

        with patch('builtins.open', mock_file):
            result = self.loader.load_programa_actual()

        self.assertTrue(result)
        self.assertTrue(self.loader.flags['flagArchivoProgramaActual'])
        self.assertEqual(self.loader.programa_actual, programa_data)

    def test_load_programa_actual_file_not_found(self):
        # Simular que el archivo no existe
        self.mock_os_path_isfile.return_value = False

        result = self.loader.load_programa_actual()

        self.assertFalse(result)
        self.assertFalse(self.loader.flags['flagArchivoProgramaActual'])
        self.assertIn("Archivo inexistente", self.loader.error_messages[0])

    def test_load_programa_actual_json_decode_error(self):
        # Simular que el archivo existe
        self.mock_os_path_isfile.return_value = True

        # Contenido inválido
        mock_file = mock_open(read_data="invalid json")

        with patch('builtins.open', mock_file):
            result = self.loader.load_programa_actual()

        self.assertFalse(result)
        self.assertFalse(self.loader.flags['flagArchivoProgramaActual'])
        self.assertIn("Error al decodificar", self.loader.error_messages[0])

    def test_load_gpio_config_success(self):
        # Simular que los archivos existen
        self.mock_os_path_isfile.return_value = True

        # Contenido simulado de los archivos
        fluxometros_data = "20\n21\n"
        parada_data = "24\n"
        nivel_data = "22\n23\n"
        inyectores_data = "2\n3\n"
        camellones_neg_data = "10\n9\n27\n6\n5\n"
        camellones_data = "11\n14\n13\n12\n"
        valvula_tanques_data = "4\n"
        motobombas_data = "15\n16\n"

        # Diccionario para mapear nombres de archivos a sus contenidos
        file_contents = {
            'fluxometros.txt': fluxometros_data,
            'parada.txt': parada_data,
            'nivel.txt': nivel_data,
            'inyectoresLogicaNegativa.txt': inyectores_data,
            'camellonesLogicaNegativa.txt': camellones_neg_data,
            'camellones.txt': camellones_data,
            'valvulaTanquesLogicaNegativa.txt': valvula_tanques_data,
            'motobombas.txt': motobombas_data
        }

        def mock_file_side_effect(filename, *args, **kwargs):
            return mock_open(read_data=file_contents[filename]).return_value

        with patch('builtins.open', new_callable=mock_open) as mock_file:
            mock_file.side_effect = mock_file_side_effect
            result = self.loader.load_gpio_config()

        self.assertTrue(result)
        self.assertTrue(self.loader.flags['flagArchivoGPIO'])
        self.assertEqual(self.loader.gpio_config['fluxometros'], [20, 21])
        self.assertEqual(self.loader.gpio_config['parada'], 24)
        self.assertEqual(self.loader.gpio_config['nivel'], [22, 23])
        self.assertEqual(self.loader.gpio_config['inyectoresLogicaNegativa'], [2, 3])
        self.assertEqual(self.loader.gpio_config['camellonesLogicaNegativa'], [10, 9, 27, 6, 5])
        self.assertEqual(self.loader.gpio_config['camellones'], [11, 14, 13, 12])
        self.assertEqual(self.loader.gpio_config['valvulaTanquesLogicaNegativa'], 4)
        self.assertEqual(self.loader.gpio_config['motobombas'], [15, 16])

    def test_load_gpio_config_file_not_found(self):
        # Simular que los archivos no existen
        self.mock_os_path_isfile.return_value = False

        result = self.loader.load_gpio_config()

        self.assertFalse(result)
        self.assertFalse(self.loader.flags['flagArchivoGPIO'])
        self.assertIn("Error al cargar configuración GPIO", self.loader.error_messages[0])

if __name__ == '__main__':
    unittest.main()
