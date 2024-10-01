# tests/test_scheduler.py

import unittest
from unittest.mock import MagicMock, patch
from datetime import datetime, timedelta

from scheduler import Scheduler

class TestScheduler(unittest.TestCase):
    def setUp(self):
        self.programa_actual = {
            'veces_por_dia': 3,
            'volumen1': 100,
            'volumen2': 200,
            'volumen3': 300,
            'volumen4': 400,
            'volumen5': 0,
            'volumen6': 0,
            'volumen7': 0,
            'volumen8': 0,
            'volumen9': 0,
            'volumen10': 0,
            'volumen11': 0,
            'volumen12': 0,
            'volumen13': 0,
            'volumen14': 0,
            'fertilizante1_1': 10,
            'fertilizante1_2': 20,
            'fertilizante1_3': 30,
            'fertilizante1_4': 40,
            'fertilizante1_5': 0,
            # Agrega los demás fertilizantes si es necesario
        }
        self.scheduler = Scheduler(self.programa_actual)

    def test_calculate_time_slots(self):
        self.scheduler.calculate_time_slots()
        franjas = self.scheduler.franjas_horarias

        self.assertEqual(len(franjas), 3)
        self.assertEqual(franjas[1]['inicio'], '00:00')
        self.assertEqual(franjas[2]['inicio'], '08:00')
        self.assertEqual(franjas[3]['inicio'], '16:00')

    def test_calculate_volumes_percentage(self):
        self.scheduler.calculate_volumes_percentage()
        porcentajes = self.scheduler.porcentajes

        volumen_total = 1000  # Suma de volúmenes de volumen1 a volumen4
        expected_porcentajes = [100/volumen_total, 200/volumen_total, 300/volumen_total, 400/volumen_total] + [0]*10

        for i in range(14):
            self.assertAlmostEqual(porcentajes[i], expected_porcentajes[i])

    @patch('scheduler.datetime')
    def test_assign_activities_to_slots(self, mock_datetime):
        # Simular datetime para tener un control preciso sobre los tiempos
        base_time = datetime(2023, 1, 1, 0, 0)
        mock_datetime.strptime.side_effect = lambda *args, **kw: datetime.strptime(*args, **kw)

        self.scheduler.calculate_time_slots()
        self.scheduler.calculate_volumes_percentage()
        self.scheduler.assign_activities_to_slots()
        actividades = self.scheduler.cronograma_actividades

        # Verificar que se hayan asignado actividades
        self.assertTrue(len(actividades) > 0)

        # Verificar que las actividades tengan los campos correctos
        for actividad in actividades:
            self.assertIn('inicio', actividad)
            self.assertIn('fin', actividad)
            self.assertIn('accion', actividad)
            accion = actividad['accion']
            self.assertIn('camellon', accion)
            self.assertIn('volumen', accion)
            self.assertIn('fertilizante1', accion)
            self.assertIn('fertilizante2', accion)

    def test_save_cronograma_actividades(self):
        self.scheduler.cronograma_actividades = [{'inicio': '00:00', 'fin': '01:00', 'accion': {'camellon': 1}}]

        with patch('builtins.open', new_callable=mock_open()) as mock_file:
            self.scheduler.save_cronograma_actividades()
            mock_file.assert_called_with('cronograma_actividades.json', 'w')

    def test_run_method(self):
        # Debido a que el método run entra en un bucle infinito, no podemos probarlo directamente
        # En su lugar, podemos verificar que se llama a execute_action cuando corresponde
        self.scheduler.cronograma_actividades = [
            {'inicio': '00:00', 'fin': '00:10', 'accion': {'camellon': 1}}
        ]

        # Simular datetime.now() para que coincida con el inicio de la actividad
        with patch('scheduler.datetime') as mock_datetime:
            mock_now = datetime.strptime('00:00', '%H:%M')
            mock_datetime.now.return_value = mock_now
            mock_datetime.strptime.side_effect = lambda *args, **kw: datetime.strptime(*args, **kw)

            self.scheduler.execute_action = MagicMock()
            self.scheduler.run = MagicMock(side_effect=KeyboardInterrupt)  # Forzar la salida del bucle

            try:
                self.scheduler.run()
            except KeyboardInterrupt:
                pass

            self.scheduler.execute_action.assert_called_once_with({'camellon': 1})

if __name__ == '__main__':
    unittest.main()
