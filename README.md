# Proyecto Riego

Riego es una aplicación para gestionar el riego automatizado, combinando módulos de Python para el control del hardware (GPIO) y una interfaz web desarrollada en Laravel.

---

## Tabla de Contenidos

- [Introducción](#introducción)
- [Características](#características)
- [Requisitos del Sistema](#requisitos-del-sistema)
- [Instalación](#instalación)
- [Configuración](#configuración)
- [Uso](#uso)
- [Lanzamiento del Proyecto](#lanzamiento-del-proyecto)
- [Desarrollo y Contribución](#desarrollo-y-contribución)
- [Licencia](#licencia)
- [Contacto](#contacto)

---

## Introducción

Este proyecto nació para automatizar el riego de cultivos de arandanos, integrando control hardware y una interfaz amigable para el usuario.

---

## Características

- Control de hardware mediante Python y GPIO.
- Interfaz web desarrollada en Laravel.
- Despliegue en Raspberry Pi Zero con Ubuntu 22.04 Server.

---

## Requisitos del Sistema

- **Hardware:** Raspberry Pi Zero (u otro compatible)
- **Sistema Operativo:** Ubuntu 22.04 Server (u otra distribución Linux)
- **Software:**  
  - Apache (para servir la aplicación Laravel)  
  - Python 3.x  
  - PHP 7.x/8.x  

---

## Instalación

Pasos detallados para clonar el repositorio e instalar las dependencias necesarias.  
_Ejemplo:_

```bash
# Clonar el repositorio
sudo git clone git@github.com:ElisaulPerezP/riego.git /var/www/riego

# Ajustar permisos
sudo chown -R www-data:www-data /var/www/riego
