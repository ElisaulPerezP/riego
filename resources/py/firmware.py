import requests
import json
from datetime import datetime

# Definir la URL de la API
url = "http://127.0.0.1:8000/api/reportes"

# Definir los datos que vamos a enviar al endpoint POST
data = {
    "volumen1": 10,
    "volumen2": 20,
    "volumen3": 30,
    "volumen4": 40,
    "volumen5": 50,
    "volumen6": 60,
    "volumen7": 70,
    "volumen8": 80,
    "volumen9": 90,
    "volumen10": 100,
    "volumen11": 110,
    "volumen12": 120,
    "volumen13": 130,
    "volumen14": 140,
  
    "tiempo1": "12:00:00",
    "tiempo2": "12:15:00",
    "tiempo3": "12:30:00",
    "tiempo4": "12:45:00",
    "tiempo5": "13:00:00",
    "tiempo6": "13:15:00",
    "tiempo7": "13:30:00",
    "tiempo8": "13:45:00",
    "tiempo9": "14:00:00",
    "tiempo10": "14:15:00",
    "tiempo11": "14:30:00",
    "tiempo12": "14:45:00",
    "tiempo13": "15:00:00",
    "tiempo14": "15:15:00",
  
    "mensaje1": "Mensaje de prueba 1",
    "mensaje2": "Mensaje de prueba 2",
    "mensaje3": "Mensaje de prueba 3",
    "mensaje4": "Mensaje de prueba 4",
    "mensaje5": "Mensaje de prueba 5",
    "mensaje6": "Mensaje de prueba 6",
    "mensaje7": "Mensaje de prueba 7",
    "mensaje8": "Mensaje de prueba 8",
    "mensaje9": "Mensaje de prueba 9",
    "mensaje10": "Mensaje de prueba 10",
    "mensaje11": "Mensaje de prueba 11",
    "mensaje12": "Mensaje de prueba 12",
    "mensaje13": "Mensaje de prueba 13",
    "mensaje14": "Mensaje de prueba 14",

    # Fechas automáticas en formato ISO8601
    "created_at": datetime.utcnow().isoformat() + "Z",
    "updated_at": datetime.utcnow().isoformat() + "Z"
}

# Hacer la solicitud POST al endpoint
response = requests.post(url, json=data)

# Mostrar el resultado
if response.status_code == 201:
    print("Reporte creado con éxito:", response.json())
else:
    print(f"Error al crear el reporte. Código de estado: {response.status_code}")
    print("Respuesta:", response.text)
