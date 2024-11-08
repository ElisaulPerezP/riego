import matplotlib.pyplot as plt
import numpy as np
import json
import sys
import os

def generar_graficas_reporte(json_data, output_path):
    # Convertir el argumento de cadena JSON a una lista de diccionarios (reportes de riego)
    reportes = json.loads(json_data)

    # Definir el número de surcos
    num_surcos = 14

    # Preparar una figura con 14 subplots (2 filas x 7 columnas)
    fig, axs = plt.subplots(2, 7, figsize=(20, 10))
    axs = axs.flatten()

    # Definir las etiquetas
    fechas = [reporte["created_at"] for reporte in reportes]

    # Iterar por cada surco y generar su gráfica
    for i in range(1, num_surcos + 1):
        volumen_key = f'volumen{i}'
        tiempo_key = f'tiempo{i}'

        # Extraer los datos de volumen y tiempo para el surco actual
        volumenes = [reporte[volumen_key] for reporte in reportes]
        tiempos = []

        # Convertir el tiempo H:i:s a minutos decimales
        for reporte in reportes:
            tiempo_str = reporte[tiempo_key]
            horas, minutos, segundos = tiempo_str.split(':')
            horas = int(horas)
            minutos = int(minutos)
            segundos = float(segundos)  # Cambiar a float para manejar los milisegundos
            total_minutos = horas * 60 + minutos + (segundos / 60)
            tiempos.append(total_minutos)

        # Crear la gráfica para el surco actual
        ax = axs[i - 1]
        ax.plot(fechas, volumenes, label='Volumen (lts)', color='b', marker='o')
        ax.plot(fechas, tiempos, label='Tiempo (mins)', color='r', marker='x')

        # Establecer el título de cada gráfica
        ax.set_title(f'Surco {i}')

        # Quitar etiquetas del eje X (fechas)
        ax.set_xticks([])

        # Etiquetas del eje Y
        ax.set_ylabel('Valores')

        # Mostrar leyenda
        ax.legend(loc='upper right')

    # Ajustar el layout para que no se superpongan los subplots
    plt.tight_layout()

    # Verificar si la ruta de salida existe, si no, crearla
    if not os.path.exists(output_path):
        os.makedirs(output_path)

    # Definir el nombre de la imagen de salida
    output_file = os.path.join(output_path, 'graficas_reporte_riego.png')

    # Guardar la figura completa como una imagen
    plt.savefig(output_file, dpi=300)

    # Mostrar confirmación de la creación de la imagen
    print(f'La imagen con las gráficas de los 14 surcos se ha guardado como {output_file}')

if __name__ == "__main__":
    # Obtener los argumentos de la línea de comandos (el JSON de la colección ReporteRiego y la ruta de salida)
    if len(sys.argv) > 2:
        json_data = sys.argv[1]
        output_path = sys.argv[2]
        generar_graficas_reporte(json_data, output_path)
    else:
        print("Por favor, proporciona los datos JSON de ReporteRiego y la ruta de salida como argumentos.")
