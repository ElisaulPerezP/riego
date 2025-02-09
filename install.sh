#!/bin/bash
# install.sh - Instalador del proyecto Riego
# Este script configura el entorno, clona el repositorio (si es necesario),
# instala PHP 8.2, Composer, Apache y sus dependencias, y configura Apache para servir la aplicación.
# Además, configura Git globalmente, verifica/genera una clave SSH para conectar con GitHub,
# instala MySQL y configura la base de datos, y prepara el entorno Node para compilar los assets del frontend.
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

echo "🚀 Iniciando instalación del proyecto Riego..."
echo "============================================"

# ───────────────────────────────────────────────────────────────
# 3️⃣ Actualización de paquetes
echo "🔄 Actualizando lista de paquetes..."
apt update -y

# ───────────────────────────────────────────────────────────────
# 4️⃣ Instalación de Git y configuración global (almacenada en el home del usuario real)
if ! command -v git &> /dev/null; then
    echo "📥 Instalando Git..."
    apt install -y git
else
    echo "✅ Git ya está instalado. Omitiendo..."
fi

# Configuración global de Git (se configura para el usuario "arandanos")
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
        # Crear el directorio .ssh si no existe
        if [ ! -d "$USER_HOME/.ssh" ]; then
            mkdir "$USER_HOME/.ssh"
            chmod 700 "$USER_HOME/.ssh"
        fi
        read -p "Ingrese su email para la clave SSH: " user_email
        ssh-keygen -t ed25519 -C "$user_email" -f "$SSH_KEY" -N ""
        # Asegurar que la clave sea propiedad del usuario real
        chown arandanos:arandanos "$SSH_KEY" "$SSH_KEY.pub"
        # Iniciar el agente SSH y añadir la clave
        eval "$(ssh-agent -s)"
        ssh-add "$SSH_KEY"
        echo "✅ Clave SSH generada y agregada al agente."
        echo "Su clave pública es:"
        cat "$SSH_KEY.pub"
        echo "Por favor, agréguela a su cuenta de GitHub antes de continuar."
        read -p "Presione Enter para continuar una vez haya agregado la clave..."
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
# 6️⃣ Verificar si el script se ejecuta dentro del repositorio; si no, clonarlo
SCRIPT_DIR=$(dirname "$(realpath "$0")")
if [[ "$SCRIPT_DIR" == "$PROJECT_DIR" ]]; then
    echo "✅ El script se está ejecutando dentro del repositorio clonado. Omitiendo descarga."
else
    if [ -d "$PROJECT_DIR" ]; then
        echo "⚠️ El directorio $PROJECT_DIR ya existe. Omitiendo clonación."
    else
        echo "📂 Clonando el repositorio en $PROJECT_DIR..."
        git clone "$REPO_URL" "$PROJECT_DIR"
    fi
fi

# ───────────────────────────────────────────────────────────────
# 7️⃣ Configurar permisos TEMPORALES para la instalación
# Se necesita que el usuario "arandanos" (quien ejecuta los comandos de instalación) tenga propiedad
echo "🔧 Configurando permisos temporales para la instalación..."
sudo chown -R arandanos:arandanos "$PROJECT_DIR"
chmod -R 755 "$PROJECT_DIR"
if [ -d "$PROJECT_DIR/storage" ] && [ -d "$PROJECT_DIR/bootstrap/cache" ]; then
    chmod -R 775 "$PROJECT_DIR/storage" "$PROJECT_DIR/bootstrap/cache"
fi
echo "✅ Permisos temporales configurados (propiedad: arandanos)."

# ───────────────────────────────────────────────────────────────
# 8️⃣ Instalar PHP 8.2 y extensiones necesarias
echo "📥 Instalando PHP 8.2 y extensiones necesarias..."
add-apt-repository -y ppa:ondrej/php
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
# 1️⃣1️⃣ Instalar dependencias del proyecto con Composer
echo "📦 Instalando dependencias del proyecto con Composer..."
cd "$PROJECT_DIR"
composer install || composer update

# ───────────────────────────────────────────────────────────────
# 1️⃣2️⃣ Configurar Apache para servir la aplicación
echo "📂 Configurando Apache para servir la aplicación..."
# Deshabilitar el sitio por defecto
a2dissite 000-default.conf
systemctl reload apache2

if [ -f "$PROJECT_DIR/config/apache/riego.conf" ]; then
    cp "$PROJECT_DIR/config/apache/riego.conf" /etc/apache2/sites-available/riego.conf
    echo "✅ Archivo de configuración copiado a /etc/apache2/sites-available."
else
    echo "❌ ERROR: No se encontró el archivo $PROJECT_DIR/config/apache/riego.conf."
    exit 1
fi

# Habilitar el sitio y recargar Apache
a2ensite riego.conf
systemctl reload apache2
echo "✅ Sitio 'riego' habilitado y Apache recargado."

# ───────────────────────────────────────────────────────────────
# 1️⃣3️⃣ Configurar Git para considerar el directorio seguro
echo "🔧 Configurando Git para considerar el directorio seguro..."
sudo -u arandanos git config --global --add safe.directory "$PROJECT_DIR"

# ───────────────────────────────────────────────────────────────
# 1️⃣4️⃣ Configurar la base de datos (MySQL)
echo "📥 Instalando y configurando MySQL..."
apt install -y mysql-server

echo "🔧 Creando la base de datos 'laravel'..."
mysql -e "CREATE DATABASE IF NOT EXISTS laravel;"

MYSQL_PASSWORD=$(openssl rand -hex 12)
echo "🔑 Contraseña generada para MySQL root: $MYSQL_PASSWORD"

# Si no existe el archivo .env, se copia de .env.example
if [ ! -f "$PROJECT_DIR/.env" ]; then
    cp "$PROJECT_DIR/.env.example" "$PROJECT_DIR/.env"
fi

# Actualizar parámetros en el .env
sed -i "s/^DB_CONNECTION=.*/DB_CONNECTION=mysql/" "$PROJECT_DIR/.env"
sed -i "s/^DB_HOST=.*/DB_HOST=127.0.0.1/" "$PROJECT_DIR/.env"
sed -i "s/^DB_PORT=.*/DB_PORT=3306/" "$PROJECT_DIR/.env"
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=laravel/" "$PROJECT_DIR/.env"
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=root/" "$PROJECT_DIR/.env"
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=${MYSQL_PASSWORD}/" "$PROJECT_DIR/.env"

# ───────────────────────────────────────────────────────────────
# 1️⃣5️⃣ Configurar entorno Node y compilar assets del frontend
echo "📦 Configurando entorno Node y compilando assets del frontend..."

# Instalar nvm (si no está instalado)
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.3/install.sh | bash

# Cargar nvm en la sesión actual (se suele añadir al .bashrc o .zshrc)
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

# Instalar Node.js versión 18 y establecerla como predeterminada
nvm install 18
nvm use 18
nvm alias default 18

# Verificar versiones de Node.js y npm
node -v
npm -v

# Para que npm pueda escribir, aseguramos la propiedad temporalmente al usuario "arandanos"
sudo chown -R arandanos:arandanos "$PROJECT_DIR"

# Navegar al directorio del proyecto y ejecutar dependencias y build
cd "$PROJECT_DIR"
npm install
npm audit fix
npm run build

echo "✅ Entorno Node configurado y assets compilados."

# ───────────────────────────────────────────────────────────────
# 1️⃣6️⃣ Configurar la aplicación (generar APP_KEY, migrar, sembrar, etc.)
echo "📂 Configurando la aplicación..."
cd "$PROJECT_DIR"
php artisan key:generate
# Aquí podrías agregar comandos adicionales, por ejemplo:
# php artisan migrate --seed

# ───────────────────────────────────────────────────────────────
# 1️⃣7️⃣ Restaurar la propiedad del proyecto para Apache (usuario www-data)
echo "🔧 Restaurando propiedad del proyecto a www-data..."
sudo chown -R www-data:www-data "$PROJECT_DIR"
echo "✅ Propiedad restaurada a www-data."

# ───────────────────────────────────────────────────────────────
# 1️⃣8️⃣ Mensaje final
echo "============================================"
echo "🎉 Instalación completada con éxito."
echo "Accede a http://arandanos.local en tu navegador (asegúrate de tener la entrada en tu archivo hosts si es necesario)."
