import sys
import qrcode
from PIL import Image, ImageDraw

def generate_qr_image(uuid_list, base_url, output_image_path):
    qr_size = 300
    columns = 3
    rows = (len(uuid_list) + columns - 1) // columns
    canvas_width = qr_size * columns
    canvas_height = qr_size * rows
    canvas = Image.new('RGB', (canvas_width, canvas_height), 'white')

    x, y = 0, 0
    for index, uuid in enumerate(uuid_list):
        url = f"{base_url}/{uuid}"
        qr = qrcode.QRCode(
            version=1,
            error_correction=qrcode.constants.ERROR_CORRECT_L,
            box_size=10,
            border=4,
        )
        qr.add_data(url)
        qr.make(fit=True)
        img_qr = qr.make_image(fill='black', back_color='white').resize((qr_size, qr_size))
        canvas.paste(img_qr, (x, y))
        x += qr_size
        if (index + 1) % columns == 0:
            x = 0
            y += qr_size

    # Guardar la imagen en la ruta especificada
    canvas.save(output_image_path)

if __name__ == '__main__':
    if len(sys.argv) != 4:
        print("Uso: python script.py 'uuid1,uuid2,uuid3' 'https://baseurl.com' 'ruta_imagen_salida'")
        sys.exit(1)

    uuid_list = sys.argv[1].split(',')
    base_url = sys.argv[2]
    output_image_path = sys.argv[3]

    generate_qr_image(uuid_list, base_url, output_image_path)
    print(f"Imagen generada: {output_image_path}")
