# Controlador de Almacenamiento Seguro - Prueba Técnica

Este proyecto implementa un sistema completo de gestión de archivos seguro desarrollado en Laravel, que permite a los usuarios subir documentos mientras aplica un conjunto de reglas de negocio para garantizar la seguridad y el uso justo del almacenamiento.

## Características Principales

### 🎯 Requerimientos Funcionales Implementados

#### 1. Sistema de Roles y Grupos
- **Roles**: Usuario y Administrador
- **Grupos**: Creación y asignación de usuarios a grupos
- **Gestión de usuarios**: Creación, edición y eliminación de usuarios

#### 2. Interfaz de Usuario
- **Panel de Usuario**: Dashboard intuitivo para ver archivos subidos y subir nuevos archivos
- **Panel de Administrador**: Gestión completa de usuarios, grupos y configuraciones
- **Estética**: Interfaz limpia usando TailwindCSS (incluido con Laravel Breeze)

#### 3. Lógica de Subida de Archivos
- **Límite de Cuota Total**: Verificación antes de guardar archivos
- **Configuración Administrativa**: Límites globales, por grupo y por usuario
- **Restricción de Tipos**: Bloqueo de extensiones peligrosas
- **Análisis de ZIP**: Inspección de contenido de archivos .zip
- **Notificaciones JavaScript**: Retroalimentación clara sin recargar la página

### 🛡️ Características de Seguridad

#### Sistema de Cuotas
- **Cuota Global**: Límite predeterminado (10MB)
- **Cuota por Grupo**: Límites específicos por grupo
- **Cuota por Usuario**: Límites individuales (máxima prioridad)
- **Cálculo en Tiempo Real**: Verificación antes de cada subida

#### Restricciones de Archivos
- **Extensiones Prohibidas**: exe, bat, js, php, sh (configurables)
- **Análisis de ZIP**: Inspección de todos los archivos dentro de archivos comprimidos
- **Validación Backend**: Todas las validaciones en PHP

#### Roles y Permisos
- **Autenticación**: Laravel Breeze con verificación de email
- **Autorización**: Middleware personalizado para acceso de administrador
- **Seguridad de Archivos**: Solo usuarios autorizados pueden subir/eliminar archivos

## Instalación y Configuración

### Requisitos del Sistema
- PHP 8.1 o superior
- Composer
- Node.js y npm (para assets)
- MySQL 8.0+ o MariaDB 10.5+
- Servidor web (Apache/Nginx) o Laravel Sail

### Pasos de Instalación

#### 1. Clonar el Repositorio
```bash
git clone https://github.com/HeinerLandero/secure-storage.git
cd secure-storage-app
```

#### 2. Instalar Dependencias
```bash
# Instalar dependencias de PHP
composer install

# Instalar dependencias de Node.js
npm install

# Construir assets
npm run build
```

#### 3. Configuración del Entorno
```bash
# Copiar archivo de configuración
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

#### 4. Configurar Base de Datos
Editar el archivo `.env` con las credenciales de tu base de datos:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=secure_storage_db
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

#### 5. Ejecutar Migraciones y Seeders
```bash
# Ejecutar migraciones
php artisan migrate

# (Obligatorio) Ejecutar seeders para datos de prueba
php artisan db:seed
```

#### 6. Crear Enlaces de Storage
```bash
php artisan storage:link
```

#### 7. Iniciar el Servidor de Desarrollo
```bash
php artisan serve
```

La aplicación estará disponible en `http://localhost:8000`

### Configuración Inicial

#### 1. Crear Primer Administrador
Usar el seeder incluido o crear manualmente un usuario administrador:

```bash
# Usando tinker
php artisan tinker
```

```php
# En tinker
$user = new App\Models\User();
$user->name = 'Administrador';
$user->email = 'admin@example.com';
$user->password = Hash::make('password123');
$user->role = 'admin';
$user->save();
```

#### 2. Configurar Valores Predeterminados
El sistema creará automáticamente:
- Cuota global: 10MB (10,485,760 bytes)
- Extensiones prohibidas: exe,bat,js,php,sh

Puedes modificarlos desde el panel de administración.

## Uso del Sistema

### Para Usuarios Regulares
1. **Registro/Login**: Crear cuenta y verificar email
2. **Dashboard**: Ver archivos subidos y cuota utilizada
3. **Subir Archivos**: Usar formulario de subida (máximo 10MB)
4. **Gestionar Archivos**: Eliminar archivos propios

### Para Administradores
1. **Acceso al Panel**: Botón "Panel de Administración" en dashboard
2. **Gestionar Usuarios**: Crear, editar y eliminar usuarios
3. **Gestionar Grupos**: Crear grupos con cuotas específicas
4. **Configuraciones**: Modificar cuota global y extensiones prohibidas

## Arquitectura Técnica

### Backend (PHP/Laravel)
- **Framework**: Laravel 11
- **Autenticación**: Laravel Breeze
- **Base de Datos**: MySQL con Eloquent ORM
- **Validación**: FormRequest classes
- **Servicios**: Service classes para lógica de negocio

### Frontend (JavaScript/Vue + Blade)
- **Template Engine**: Blade templates
- **JavaScript**: Vanilla ES6+ (sin frameworks)
- **CSS Framework**: TailwindCSS
- **AJAX**: Fetch API para operaciones dinámicas

### Seguridad
- **Validación**: Server-side en PHP
- **Autorización**: Middleware personalizado
- **CSRF Protection**: Tokens CSRF en formularios
- **File Validation**: MIME type y extensión
- **Storage**: Archivos privados en filesystem

## Estructura del Proyecto

```
secure-storage-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php
│   │   │   ├── FileController.php
│   │   │   └── ProfileController.php
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   └── ...
│   ├── Models/
│   │   ├── User.php
│   │   ├── File.php
│   │   ├── Group.php
│   │   ├── Configuration.php
│   │   └── ...
│   ├── Services/
│   │   └── FileService.php
│   └── ...
├── database/
│   ├── migrations/
│   └── seeders/
├── public/
│   ├── js/
│   │   └── file-upload.js
│   └── ...
├── resources/
│   ├── views/
│   │   ├── dashboard.blade.php
│   │   ├── admin/
│   │   │   ├── users/
│   │   │   ├── groups/
│   │   │   └── configurations/
│   │   └── ...
│   └── ...
├── routes/
│   └── web.php
└── ...
```

## API Endpoints

### Usuarios Autenticados
- `GET /dashboard` - Dashboard principal
- `POST /files/upload` - Subir archivo
- `DELETE /files/{id}` - Eliminar archivo
- `GET /files/get-files` - Obtener lista de archivos (JSON)
- `GET /files/storage-info` - Información de almacenamiento (JSON)

### Administradores
- `GET /admin` - Dashboard administrativo
- `GET /admin/users` - Gestión de usuarios
- `POST /admin/users` - Crear usuario
- `PUT /admin/users/{id}` - Actualizar usuario
- `DELETE /admin/users/{id}` - Eliminar usuario
- `GET /admin/groups` - Gestión de grupos
- `POST /admin/groups` - Crear grupo
- `GET /admin/configurations` - Configuraciones del sistema
- `PUT /admin/configurations` - Actualizar configuraciones

## Credenciales de Ejemplo

admin@example.com     - password     (Admin)
user@example.com      - password123  (Usuario)
juan@example.com      - password123  (Usuario)
maria@example.com     - password123  (Usuario)
superadmin@example.com - password123  (Admin)

## Tecnologías Utilizadas

- **Backend**: Laravel 12, PHP 8.2
- **Frontend**: Blade Templates, TailwindCSS, Vanilla JavaScript
- **Base de Datos**: MySQL 8.0
- **Autenticación**: Laravel Breeze
- **Validation**: Laravel Validation

## Decisiones de Diseño

### 1. Arquitectura de Capas
- **Controllers**: Manejo de requests/responses
- **Services**: Lógica de negocio
- **Models**: Representación de datos
- **Middleware**: Cross-cutting concerns

### 2. Principios SOLID
- **Single Responsibility**: Cada clase tiene una responsabilidad específica
- **Open/Closed**: Extensible sin modificar código existente
- **Dependency Inversion**: Uso de dependency injection

### 3. Seguridad
- **Validación Multi-capa**: Client-side + Server-side
- **Principio de Menor Privilegio**: Acceso mínimo necesario
- **Segregación de Datos**: Archivos por usuario en directorios separados

### 4. Experiencia de Usuario
- **AJAX**: Sin recarga de página para operaciones
- **Feedback Inmediato**: Notificaciones en tiempo real
- **Interfaz Intuitiva**: Navegación clara y consistente


## Contribución

Este proyecto fue desarrollado como prueba técnica siguiendo las mejores prácticas de desarrollo en Laravel y principios SOLID.

## Licencia

Este proyecto es para propósitos educativos y de demostración.
