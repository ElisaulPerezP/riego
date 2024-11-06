# pin_mapping.py

# Diccionario de mapeo de pines para diferentes componentes
PIN_MAPPING = {
    'camellones': {
        1: {'pin': 11, 'logic': 'positive'},
        2: {'pin': 14, 'logic': 'positive'},
        3: {'pin': 13, 'logic': 'positive'},
        4: {'pin': 12, 'logic': 'positive'},
        5: {'pin': 10, 'logic': 'negative'},
    },
    'motobombas': [21, 22], 
    'inyectoresLogicaNegativa': [23, 24], 
    'valvulaTanquesLogicaNegativa': 25, 
    'fluxometros': [16, 18], 
    'nivel': [26, 27], 
    'parada': 19 
}
