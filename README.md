# ViverClinic Platform

Plataforma integral para la gestión, operación y administración de clínicas y centros de estética. ViverClinic está diseñada para optimizar los flujos de trabajo, desde el agendamiento de citas y control de inventario/disparos láser, hasta la fidelización de clientes y reportes financieros, con soporte multi-sucursal y diferentes niveles de acceso.

## Tabla de Contenidos

- [Características Principales](#características-principales)
- [Módulos y Funcionalidades](#módulos-y-funcionalidades)
- [Roles de Usuario](#roles-de-usuario)
- [Requerimientos](#requerimientos)
- [Instalación](#instalación)
- [Estructura del Proyecto](#estructura-del-proyecto)

## Características Principales

- **Arquitectura Multi-Sucursal:** Gestión centralizada de múltiples sedes, permitiendo asignar personal, pacientes y reportes a sucursales específicas.
- **Sistema de Roles y Permisos Avanzado:** Control de acceso granular basado en Spatie, asegurando que cada usuario vea únicamente lo que necesita.
- **Portal de Pacientes Integrado:** Los clientes tienen su propio panel para gestionar sus citas, ver su historial clínico, comprar paquetes y revisar tips de cuidado.
- **Control de Inventario y Disparos:** Seguimiento detallado del uso de equipamiento médico (como disparos de depilación láser) con cálculo dinámico de límites según las zonas tratadas.
- **Programa de Referidos:** Sistema de fidelización automatizado que recompensa a los pacientes por invitar a nuevos clientes, otorgando beneficios (ej. sesiones extra) al concretarse ventas.

## Módulos y Funcionalidades

### 1. Gestión de Citas (Agenda)
- Calendario intuitivo para agendamiento, confirmación, atención y cancelación de citas.
- Integración automática con los paquetes de tratamiento comprados por el cliente.
- Filtros dependientes y predictivos para la asignación rápida de tratamientos en base al paciente seleccionado.

### 2. Control de Tratamientos y Paquetes
- Creación y venta de tratamientos (individuales o en paquete).
- Clasificación por zonas anatómicas (Grandes, Pequeñas, Minis) que alimentan la lógica de consumo.
- Control del número de sesiones consumidas y seguimiento del progreso del paciente.

### 3. Reportes y Analíticas (Dashboard)
- Visualización de ingresos diarios (tratamientos + productos), egresos y pagos pendientes.
- Listado y métricas de citas del día, mes actual y estadísticas de los últimos 7 días.
- Reporte especializado de **Control de Disparos**, con semáforo de estados (Normal, Exceso Leve, Exceso Crítico) para supervisar la eficiencia del personal operativo.

### 4. Módulo de CRM y Fidelización (Referidos)
- Generación de enlaces y códigos únicos por paciente.
- Panel administrativo para revisar los referidos exitosos.
- Lógica automatizada que abona automáticamente recompensas a los planes activos del referente.

### 5. Configuración y Módulos de Contenido
- **Tips de Cuidado / Recomendaciones:** Gestión de contenido informativo para los pacientes post-tratamiento.
- **Módulo de Capacitaciones:** Sección interna para la formación estandarizada del personal.
- **Ajustes Globales:** Configuración de parámetros operativos (como el límite global de disparos láser por zona).

## Roles de Usuario

El sistema cuenta con niveles de acceso especializados para organizar la operación:

1. **SUPER_ADMIN:** Acceso absoluto al sistema, código fuente y configuraciones críticas.
2. **OWNER (Dueño):** Visualización global de todas las sucursales, reportes financieros y gestión gerencial.
3. **ADMIN (Administrador):** Gestión operativa y de reportes limitada a las sucursales que se le asignen.
4. **EMPLOYEE (Empleado/Especialista):** Personal médico o estético que atiende las citas. Su panel se centra en su agenda diaria y el registro de la atención (ej. llenar el reporte de disparos).
5. **SALES (Ventas):** Personal encargado del seguimiento de clientes potenciales, gestión de compras y métricas de ventas.
6. **PATIENT (Paciente):** Usuario final que consume los servicios. Accede al portal de clientes para gestionar su experiencia.

## Requerimientos

1. PHP versión: 8.2 en adelante.
2. NPM & Node.js
3. Composer
4. Base de datos MySQL / MariaDB o PostgreSQL.

## Instalación

1. Clona el repositorio:
    ```bash
    git clone <URL_DEL_REPOSITORIO>
    ```
2. Accede al directorio del proyecto:
    ```bash
    cd viverclinic
    ```
3. Instala las dependencias de PHP y Node:
    ```bash
    composer install
    npm install
    npm run build
    ```
4. Configura el entorno:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
5. Configura las variables de tu base de datos en el archivo `.env`.
6. Ejecuta las migraciones y seeders para establecer la estructura y datos base (roles, permisos, admin inicial):
    ```bash
    php artisan migrate --seed
    ```

## Estructura del Proyecto

La arquitectura sigue el patrón MVC de Laravel 12, con adiciones específicas para la lógica de negocio:

- **app/Http/Controllers:** Controladores organizados en subcarpetas (`Admin/`, `Staff/`, `Auth/`) según el rol y área de negocio.
- **app/Models:** Modelos Eloquent con relaciones complejas y Scopes (ej. `ScopesByBranch` para el filtrado multi-sucursal).
- **app/Traits:** Lógica reutilizable y modular para los modelos.
- **resources/views:** Vistas de Blade estructuradas semánticamente (`admin/`, `staff/`, `client/`, `components/`).
- **public/images:** Almacén de assets visuales, iconos de navegación y logos corporativos.
- **routes/web.php:** Punto de entrada que bifurca a archivos de rutas dedicados (`admin.php`, `client.php`, `staff.php`).
