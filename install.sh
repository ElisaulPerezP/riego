#!/bin/bash
# install.sh - Instalador del proyecto Riego
# Este script configura el entorno, clona el repositorio (si es necesario),
# instala PHP 8.2, Composer, Apache y sus dependencias, y configura Apache para servir la aplicación.
# Además, configura globalmente Git (solicitando nombre y correo) y verifica/genera una clave SSH para conectar con GitHub.

# Verificar si el script se está ejecutando como root o con sudo
if [ "$EUID" -ne 0 ]; then
    echo "❌ ERROR: Este script debe ejecutarse con permisos de superusuario."
    echo "👉 Usa: sudo bash install.sh"
    exit 1
fi

# Variables del script
USER_HOME="/home/arandanos"
PROJECT_DIR="$USER_HOME/riego"
REPO_URL="git@github.com:ElisaulPerezP/riego.git"

echo "🚀 Iniciando instalación del proyecto Riego..."
echo "============================================"

# 1️⃣ ACTUALIZACIÓN DE PAQUETES
echo "🔄 Actualizando lista de paquetes..."
apt update -y

# 2️⃣ INSTALACIÓN DE GIT (si no está instalado)
if ! command -v git &> /dev/null; then
    echo "📥 Instalando Git..."
    apt install -y git
else
    echo "✅ Git ya está instalado. Omitiendo..."
fi

# 2.1 CONFIGURACIÓN GLOBAL DE GIT
read -p "Ingrese su nombre de usuario global para Git: " git_username
read -p "Ingrese su correo electrónico global para Git: " git_email
git config --global user.name "$git_username"
git config --global user.email "$git_email"
echo "✅ Configuración global de Git establecida: $git_username <$git_email>"

# 2.5 CONFIGURACIÓN DE CLAVE SSH
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
        # Iniciar el agente SSH y añadir la clave
        eval "$(ssh-agent -s)"
        ssh-add "$SSH_KEY"
        echo "✅ Clave SSH generada y agregada al agente."
        echo "Su clave pública es:"
        cat "$SSH_KEY.pub"
        echo "Por favor, agréguela a su cuenta de GitHub antes de continuar."
        read -p "Presione Enter para continuar una vez haya agregado la clave..."
    else
        echo "❌ No se generó una clave SSH. La clonación del repositorio podría fallar si no tiene acceso configurado."
    fi
else
    echo "✅ Clave SSH encontrada."
    eval "$(ssh-agent -s)"
    ssh-add "$SSH_KEY"
fi

# 3️⃣ VERIFICAR SI EL SCRIPT ESTÁ DENTRO DEL REPOSITORIO
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

# 4️⃣ INSTALAR PHP 8.2 Y EXTENSIONES
echo "📥 Instalando PHP 8.2 y extensiones necesarias..."
add-apt-repository -y ppa:ondrej/php
apt update -y
apt install -y php8.2 php8.2-cli php8.2-common php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-mysql php8.2-dom

# Verificar la versión de PHP
echo "🔍 Verificando versión de PHP..."
update-alternatives --set php /usr/bin/php8.2
update-alternatives --set phar /usr/bin/phar8.2
php -v

# 5️⃣ INSTALACIÓN DE COMPOSER
if ! command -v composer &> /dev/null; then
    echo "📥 Instalando Composer..."
    apt install -y curl php-cli unzip
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
else
    echo "✅ Composer ya está instalado. Omitiendo..."
fi

# 6️⃣ VERIFICACIÓN DE COMPOSER
echo "🔍 Verificando instalación de Composer..."
composer --version

# 7️⃣ INSTALACIÓN Y CONFIGURACIÓN DE APACHE
if ! systemctl is-active --quiet apache2; then
    echo "📥 Instalando Apache..."
    apt install -y apache2
    echo "✅ Apache instalado correctamente."
else
    echo "✅ Apache ya está instalado. Omitiendo..."
fi

# Habilitar mod_rewrite para Laravel
echo "🔧 Habilitando mod_rewrite en Apache..."
a2enmod rewrite
systemctl enable apache2
systemctl restart apache2
echo "✅ Apache configurado correctamente."

# 8️⃣ INSTALAR DEPENDENCIAS CON COMPOSER
echo "📦 Instalando dependencias del proyecto con Composer..."
cd "$PROJECT_DIR"
composer install || composer update

echo "📂 Configurando Apache para servir la aplicación..."

# Verificar que el archivo de configuración exista en el repositorio
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

# 9️⃣ CONFIGURAR GIT PARA CONSIDERAR EL DIRECTORIO COMO SEGURO
echo "🔧 Configurando Git para considerar el directorio seguro..."
# Se ejecuta como el usuario 'arandanos' para que la configuración global se almacene en su home.
sudo -u arandanos git config --global --add safe.directory "$PROJECT_DIR"

echo "============================================"
echo "🎉 Instalación completada con éxito."
echo "Accede a http://arandanos.local en tu navegador (asegúrate de tener la entrada en tu archivo hosts si es necesario)."
