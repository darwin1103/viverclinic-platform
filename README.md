# ViverClinic Platform

Plataforma para la gestión integral de centros de estética.

## Tabla de Contenidos

- [Características](#características)
- [Requerimientos](#requerimientos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)
- [Notas adicionales](#notas-adicionales)

## Características

- Gestión de usuarios, Roles y permisos
- Envío de notificaciones por email

## Requerimientos

1. PHP versión: 8.2 en adelante.
2. NPM
3. Composer

## Instalación

1. Clona el repositorio:
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    ```
2. Accede al directorio del proyecto:
    ```bash
    cd [nombre del proyecto]
    ```
3. Instala las dependencias necesarias según el archivo `package.json` y `composer.json`:
    ```bash
    composer install
    npm install
    ```
4. Configura las variables de entorno y la base de datos según la documentación del framework Laravel versión 12.

5. Ejecuta las migraciones una vez hayas agregado las credenciales de la base de datos:
    ```bash
    php artisan migrate
    ```

## Estructura del Proyecto

```
/directorio_raíz
├── README.md
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   ├── Models/
│   └── Providers/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── css/
│   ├── js/
│   └── index.php
├── resources/
│   ├── js/
│   ├── lang/
│   ├── sass/
│   └── views/
├── routes/
│   ├── api.php
│   ├── channels.php
│   ├── console.php
│   └── web.php
├── storage/
├── tests/
├── vendor/
├── artisan
├── composer.json
└── package.json
```

- **app/**: Código principal de la aplicación (controladores, modelos, servicios, etc.).
- **bootstrap/**: Archivos de arranque y carga inicial de Laravel.
- **config/**: Archivos de configuración de la aplicación.
- **database/**: Migraciones, seeders y factories para la base de datos.
- **public/**: Punto de entrada web y archivos estáticos (CSS, JS, imágenes).
- **resources/**: Vistas Blade, archivos de frontend y recursos de localización.
- **routes/**: Definición de rutas de la aplicación.
- **storage/**: Archivos generados, logs y cachés.
- **tests/**: Pruebas automatizadas.
- **vendor/**: Dependencias instaladas por Composer.
- **artisan**: Interfaz de línea de comandos de Laravel.
- **composer.json**: Dependencias PHP y configuración de Composer.
- **package.json**: Dependencias y scripts de Node.js.

## Notas adicionales

1. Dependencias adicionales:
    - Bootstrap 5
    - JQuery
