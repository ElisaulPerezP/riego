#!/bin/bash
# install.sh - Instalador del proyecto Riego para Producción
# Este script configura el entorno, clona el repositorio (si es necesario),
# instala PHP 8.2, Composer, Apache, MariaDB y Node.js, configura Git, genera la clave SSH,
# configura la base de datos, compila los assets del frontend y prepara la aplicación para producción.
#
# Procedimiento de despliegue en producción:
#   • composer install --optimize-autoloader --no-dev
#   • php artisan config:cache
#   • php artisan event:cache
#   • php artisan route:cache
#   • php artisan view:cache
#
# El instalador se ejecuta como sudo (por el usuario "arandanos") y, al finalizar,
# se entrega la propiedad del directorio del proyecto a "www-data" para que Apache lo gestione.

# ───────────────────────────────────────────────────────────────
# 1️⃣ Verificar que el script se ejecuta como root/sudo
if [ "$EUID" -ne 0 ]; then
    echo "❌ ERROR: Este script debe ejecutarse con permisos de superusuario."
    echo "👉 Usa: sudo bash install.sh"
    exit 1
fi

# ───────────────────────────────────────────────────────────────
# 2️⃣ Definir variables
USER_HOME="/home/arandanos"
PROJECT_DIR="$USER_HOME/riego"
REPO_URL="git@github.com:ElisaulPerezP/riego.git"

echo "🚀 Iniciando instalación del proyecto Riego en producción..."
echo "============================================"

# ───────────────────────────────────────────────────────────────
# 3️⃣ Actualización de paquetes y daemons
echo "🔄 Actualizando lista de paquetes..."
apt update -y
apt install -y avahi-daemon
systemctl enable avahi-daemon
systemctl start avahi-daemon

# ───────────────────────────────────────────────────────────────
# 4️⃣ Instalación de Git y configuración global
if ! command -v git &> /dev/null; then
    echo "📥 Instalando Git..."
    apt install -y git
else
    echo "✅ Git ya está instalado. Omitiendo..."
fi

read -p "Ingrese su nombre de usuario global para Git: " git_username
read -p "Ingrese su correo electrónico global para Git: " git_email
sudo -u arandanos git config --global user.name "$git_username"
sudo -u arandanos git config --global user.email "$git_email"
echo "✅ Configuración global de Git establecida: $git_username <$git_email>"

# ───────────────────────────────────────────────────────────────
# 5️⃣ Configuración de clave SSH para GitHub
echo "🔐 Verificando clave SSH para GitHub..."
SSH_KEY="$USER_HOME/.ssh/id_ed25519"
if [ ! -f "$SSH_KEY" ]; then
    echo "No se encontró clave SSH."
    read -p "¿Desea generar una nueva clave SSH (ed25519) para GitHub? (s/n): " generate_key
    if [[ "$generate_key" =~ ^[Ss] ]]; then
        if [ ! -d "$USER_HOME/.ssh" ]; then
            mkdir "$USER_HOME/.ssh"
            chmod 700 "$USER_HOME/.ssh"
        fi
        read -p "Ingrese su email para la clave SSH: " user_email
        ssh-keygen -t ed25519 -C "$user_email" -f "$SSH_KEY" -N ""
        chown arandanos:arandanos "$SSH_KEY" "$SSH_KEY.pub"
        eval "$(ssh-agent -s)"
        ssh-add "$SSH_KEY"
        echo "✅ Clave SSH generada y agregada al agente."
        echo "Su clave pública es:"
        cat "$SSH_KEY.pub"
        echo "Por favor, agréguela a su cuenta de GitHub antes de continuar."
        read -p "Presione Enter para continuar..."
    else
        echo "❌ No se generó una clave SSH. La clonación del repositorio podría fallar."
    fi
else
    echo "✅ Clave SSH encontrada."
    chown arandanos:arandanos "$SSH_KEY" "$SSH_KEY.pub"
    eval "$(ssh-agent -s)"
    ssh-add "$SSH_KEY"
fi

# ───────────────────────────────────────────────────────────────
# 6️⃣ Clonar el repositorio (si no existe)
SCRIPT_DIR=$(dirname "$(realpath "$0")")
if [[ "$SCRIPT_DIR" == "$PROJECT_DIR" ]]; then
    echo "✅ El script se ejecuta dentro del repositorio clonado. Omitiendo clonación."
else
    if [ -d "$PROJECT_DIR" ]; then
        echo "⚠️ El directorio $PROJECT_DIR ya existe. Omitiendo clonación."
    else
        echo "📂 Clonando el repositorio en $PROJECT_DIR..."
        git clone "$REPO_URL" "$PROJECT_DIR"
    fi
fi

# ───────────────────────────────────────────────────────────────
# 7️⃣ Configurar permisos temporales para la instalación (propiedad: arandanos)
echo "🔧 Configurando permisos temporales para la instalación..."
chown -R arandanos:arandanos "$PROJECT_DIR"
chmod -R 755 "$PROJECT_DIR"
if [ -d "$PROJECT_DIR/storage" ] && [ -d "$PROJECT_DIR/bootstrap/cache" ]; then
    chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"
fi
echo "✅ Permisos temporales configurados (propiedad: arandanos)."

# ───────────────────────────────────────────────────────────────
# 8️⃣ Instalar PHP 8.2 y extensiones necesarias usando el repositorio de sury.org
echo "📥 Instalando PHP 8.2 y extensiones necesarias..."
apt install -y apt-transport-https lsb-release ca-certificates curl software-properties-common
curl -fsSL https://packages.sury.org/php/apt.gpg | gpg --dearmor -o /usr/share/keyrings/php-archive-keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/php-archive-keyring.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list
apt update -y
apt install -y php8.2 php8.2-cli php8.2-common php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-mysql php8.2-dom
echo "🔍 Verificando versión de PHP..."
update-alternatives --set php /usr/bin/php8.2
update-alternatives --set phar /usr/bin/phar8.2
php -v

# ───────────────────────────────────────────────────────────────
# 9️⃣ Instalar y verificar Composer
if ! command -v composer &> /dev/null; then
    echo "📥 Instalando Composer..."
    apt install -y curl php-cli unzip
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
else
    echo "✅ Composer ya está instalado. Omitiendo..."
fi
echo "🔍 Verificando instalación de Composer..."
composer --version

# ───────────────────────────────────────────────────────────────
# 🔟 Instalar y configurar Apache
if ! systemctl is-active --quiet apache2; then
    echo "📥 Instalando Apache..."
    apt install -y apache2
    echo "✅ Apache instalado correctamente."
else
    echo "✅ Apache ya está instalado. Omitiendo..."
fi
echo "🔧 Habilitando mod_rewrite en Apache..."
a2enmod rewrite
systemctl enable apache2
systemctl restart apache2
echo "✅ Apache configurado correctamente."

# ───────────────────────────────────────────────────────────────
# 1️⃣1️⃣ Instalar dependencias del proyecto con Composer (producción)
echo "📦 Instalando dependencias del proyecto (producción) con Composer..."
cd "$PROJECT_DIR"
composer install --optimize-autoloader --no-dev || composer update --optimize-autoloader --no-dev

echo "📦 Instalando dependencias del proyecto (firmware) con pip de python3"
apt install -y python3-pip
python3 -m pip install -r "$PROJECT_DIR/resources/py/requirements.txt"

# ───────────────────────────────────────────────────────────────
# 1️⃣2️⃣ Configurar Apache para servir la aplicación
echo "📂 Configurando Apache para servir la aplicación..."
a2dissite 000-default.conf
systemctl reload apache2

if [ -f "$PROJECT_DIR/config/apache/riego.conf" ]; then
    cp "$PROJECT_DIR/config/apache/riego.conf" /etc/apache2/sites-available/riego.conf
    echo "✅ Archivo de configuración copiado a /etc/apache2/sites-available."
else
    echo "❌ ERROR: No se encontró el archivo $PROJECT_DIR/config/apache/riego.conf."
    exit 1
fi

a2ensite riego.conf
systemctl reload apache2
echo "✅ Sitio 'riego' habilitado y Apache recargado."

# ───────────────────────────────────────────────────────────────
# 1️⃣3️⃣ Configurar Git para considerar el directorio seguro
echo "🔧 Configurando Git para considerar el directorio seguro..."
sudo -u arandanos git config --global --add safe.directory "$PROJECT_DIR"

# ───────────────────────────────────────────────────────────────
# 1️⃣4️⃣ Configurar la base de datos (MariaDB) y actualizar .env
echo "📥 Instalando y configurando MariaDB..."
apt install -y mariadb-server

echo "🔧 Creando la base de datos 'laravel'..."
mysql -e "CREATE DATABASE IF NOT EXISTS laravel;"

# Generar una contraseña aleatoria para MariaDB root (12 bytes en hexadecimal)
MYSQL_PASSWORD=$(openssl rand -hex 12)
echo "🔑 Contraseña generada para MariaDB root: $MYSQL_PASSWORD"

# Actualizar la contraseña en MariaDB para el usuario root usando SET PASSWORD
sudo mysql -e "SET PASSWORD FOR 'root'@'localhost' = PASSWORD('${MYSQL_PASSWORD}');"

# Si no existe el archivo .env, se copia desde .env.example
if [ ! -f "$PROJECT_DIR/.env" ]; then
    cp "$PROJECT_DIR/.env.example" "$PROJECT_DIR/.env"
fi

ENV_FILE="$PROJECT_DIR/.env"

# Actualizar (o agregar) las variables de entorno en el archivo .env:
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" "$ENV_FILE"
sed -i "s/^DB_HOST=.*/DB_HOST=127.0.0.1/" "$ENV_FILE"
sed -i "s/^DB_PORT=.*/DB_PORT=3306/" "$ENV_FILE"
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=laravel/" "$ENV_FILE"
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=root/" "$ENV_FILE"
if grep -q "^DB_PASSWORD=" "$ENV_FILE"; then
    sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${MYSQL_PASSWORD}/" "$ENV_FILE"
else
    echo "DB_PASSWORD=${MYSQL_PASSWORD}" >> "$ENV_FILE"
fi

echo "✅ Archivo .env actualizado y contraseña de MariaDB root configurada."



# ───────────────────────────────────────────────────────────────
# 1️⃣5️⃣ Configurar entorno Node y compilar assets del frontend
echo "📦 Configurando entorno Node y compilando assets del frontend..."
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"
nvm install 18
nvm use 18
nvm alias default 18
node -v
npm -v

# Aseguramos la propiedad para que npm pueda escribir
chown -R arandanos:arandanos "$PROJECT_DIR"
cd "$PROJECT_DIR"
npm install
npm audit fix
npm run build
echo "✅ Entorno Node configurado y assets compilados."

# ───────────────────────────────────────────────────────────────
# 1️⃣6️⃣ Configurar la aplicación para producción
echo "📂 Configurando la aplicación para producción..."
cd "$PROJECT_DIR"
php artisan key:generate
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --seed

# ───────────────────────────────────────────────────────────────
# 1️⃣7️⃣ Configurar el sistema para operar periféricos
echo "📂 Configurando la aplicación para manejar los pines de propósito general..."
apt install -y git build-essential
cd /tmp
git clone https://github.com/joan2937/pigpio.git
cd pigpio
make
make install

# Asegurarse de que el binario pigpiod tenga permisos de ejecución
sudo chmod +x /usr/local/bin/pigpiod
sudo pigpiod

# ───────────────────────────────────────────────────────────────
# 1️⃣8️⃣ Restaurar la propiedad del proyecto para Apache (usuario www-data)
echo "🔧 Restaurando propiedad del proyecto a www-data..."
chown -R www-data:www-data "$PROJECT_DIR"
chmod 755 /home/arandanos
echo "✅ Propiedad restaurada a www-data."

# ───────────────────────────────────────────────────────────────
# 1️⃣9️⃣ Instalar y configurar el servicio main_controller
echo "📂 Configurando el servicio main_controller..."
SERVICE_SRC="$PROJECT_DIR/config/services/main_controller.service"
SERVICE_DEST="/etc/systemd/system/main_controller.service"

if [ -f "$SERVICE_SRC" ]; then
    cp "$SERVICE_SRC" "$SERVICE_DEST"
    echo "✅ Archivo main_controller.service copiado a $SERVICE_DEST."
else
    echo "❌ ERROR: No se encontró el archivo $SERVICE_SRC."
    exit 1
fi

# Recargar systemd, habilitar y arrancar el servicio
systemctl daemon-reload
systemctl enable main_controller.service
systemctl start main_controller.service
echo "✅ Servicio main_controller habilitado y arrancado."

# ───────────────────────────────────────────────────────────────
# 2️⃣0️⃣ Instalar y configurar el servicio pigpiod
echo "📂 Configurando el servicio pigpiod..."
PIGPIOD_SERVICE_SRC="$PROJECT_DIR/config/services/pigpiod.service"
PIGPIOD_SERVICE_DEST="/etc/systemd/system/pigpiod.service"

if [ -f "$PIGPIOD_SERVICE_SRC" ]; then
    cp "$PIGPIOD_SERVICE_SRC" "$PIGPIOD_SERVICE_DEST"
    echo "✅ Archivo pigpiod.service copiado a $PIGPIOD_SERVICE_DEST."
else
    echo "❌ ERROR: No se encontró el archivo $PIGPIOD_SERVICE_SRC."
    exit 1
fi

# Recargar systemd para reconocer el nuevo servicio,
# habilitar el servicio para que arranque al iniciar el sistema,
# y arrancarlo de inmediato.
sudo systemctl daemon-reload
sudo systemctl enable pigpiod.service
sudo systemctl restart pigpiod.service
echo "✅ Servicio pigpiod habilitado y arrancado."

# ───────────────────────────────────────────────────────────────
# 2️⃣1️⃣ Mensaje final
echo "============================================"
echo "🎉 Instalación completada con éxito."
echo "Accede a http://arandanos.local en tu navegador."
