# ReserBot - Sistema de Reservaciones y Citas Profesionales

<p align="center">
  <img src="https://img.shields.io/badge/PHP-7.4+-777BB4?style=flat-square&logo=php" alt="PHP Version">
  <img src="https://img.shields.io/badge/MySQL-5.7+-4479A1?style=flat-square&logo=mysql" alt="MySQL Version">
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=flat-square&logo=tailwind-css" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="License">
</p>

ReserBot es un sistema completo de gestión de reservaciones y citas profesionales, desarrollado en PHP puro con arquitectura MVC. Ideal para clínicas, salones de belleza, barberías, consultorios legales, psicológicos y cualquier negocio que requiera gestión de citas.

## 📋 Características

### Niveles de Acceso
- **Administrador General (Superadmin)**: Gestión completa del sistema
- **Administrador de Sucursal**: Gestión de su sucursal asignada
- **Especialista/Profesional**: Gestión de sus citas y horarios
- **Cliente/Usuario Final**: Solicitud y seguimiento de citas
- **Recepcionista**: Gestión manual de citas

### Módulos Principales
- ✅ Autenticación y Registro con validación de correo
- ✅ Gestión de Sucursales con horarios y días feriados
- ✅ Gestión de Especialistas con perfiles públicos
- ✅ Catálogo de Servicios y Categorías
- ✅ Sistema de Reservaciones en tiempo real
- ✅ Calendario interactivo con FullCalendar.js
- ✅ Dashboard con métricas y gráficas (Chart.js)
- ✅ Sistema de Notificaciones
- ✅ Reportes con exportación a Excel/PDF
- ✅ Configuración personalizable (colores, logos, PayPal)
- ✅ Logs de seguridad y bitácora de acciones

## 🔧 Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Apache con mod_rewrite habilitado
- Extensiones PHP: PDO, PDO_MySQL, JSON, mbstring, session

## 🚀 Instalación

### 1. Clonar o descargar el repositorio

```bash
git clone https://github.com/usuario/Sistema-ReserBot-.git
cd Sistema-ReserBot-
```

### 2. Configurar la base de datos

Edite el archivo `config/config.php` con sus credenciales de base de datos:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'reserbot_db');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_password');
```

### 3. Importar el esquema de base de datos

```bash
mysql -u tu_usuario -p < sql/schema.sql
```

O desde phpMyAdmin:
1. Acceda a phpMyAdmin
2. Cree una base de datos llamada `reserbot_db`
3. Importe el archivo `sql/schema.sql`

### 4. Configurar Apache

Asegúrese de que mod_rewrite esté habilitado:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Configure su VirtualHost para permitir .htaccess:

```apache
<Directory /var/www/html/reserbot>
    AllowOverride All
    Require all granted
</Directory>
```

### 5. Verificar la instalación

Acceda al archivo de prueba de conexión:
```
http://tu-dominio.com/ruta/test_connection.php
```

### 6. Acceder al sistema

Una vez verificada la instalación:
```
http://tu-dominio.com/ruta/
```

**Acceso alternativo (si mod_rewrite no está disponible):**
```
http://tu-dominio.com/ruta/login.php
http://tu-dominio.com/ruta/registro.php
```

## 🔐 Credenciales por Defecto

| Usuario | Correo | Contraseña | Rol |
|---------|--------|------------|-----|
| Administrador | admin@reserbot.com | admin123 | Superadmin |
| Carlos Hernández | carlos.hernandez@reserbot.com | password123 | Admin Sucursal |
| María López | maria.lopez@reserbot.com | password123 | Admin Sucursal |
| Dr. Roberto | roberto.martinez@reserbot.com | password123 | Especialista |
| Pedro González | pedro.gonzalez@email.com | password123 | Cliente |

> ⚠️ **Importante**: Cambie las contraseñas después de la primera instalación.

## 📁 Estructura del Proyecto

```
Sistema-ReserBot-/
├── app/
│   ├── controllers/       # Controladores MVC
│   ├── models/            # Modelos (en desarrollo)
│   └── views/             # Vistas organizadas por módulo
│       ├── auth/          # Login, registro, recuperación
│       ├── branches/      # Gestión de sucursales
│       ├── calendar/      # Calendario interactivo
│       ├── clients/       # Gestión de clientes
│       ├── dashboard/     # Panel principal
│       ├── layouts/       # Plantillas principales
│       ├── logs/          # Bitácora de seguridad
│       ├── notifications/ # Sistema de notificaciones
│       ├── profile/       # Perfil de usuario
│       ├── reports/       # Reportes y estadísticas
│       ├── reservations/  # Gestión de citas
│       ├── services/      # Servicios y categorías
│       ├── settings/      # Configuraciones del sistema
│       └── specialists/   # Gestión de especialistas
├── config/
│   ├── config.php         # Configuración principal
│   └── database.php       # Clase de conexión BD
├── helpers/
│   └── functions.php      # Funciones auxiliares
├── public/
│   ├── css/               # Estilos adicionales
│   ├── images/            # Imágenes del sistema
│   ├── js/                # Scripts JavaScript
│   ├── .htaccess          # Reescritura de URLs
│   └── index.php          # Punto de entrada
├── sql/
│   └── schema.sql         # Esquema de base de datos
├── .htaccess              # Redirección a public
├── test_connection.php    # Verificación de instalación
└── README.md
```

## 🎨 Personalización

### Colores del Sistema

Desde **Configuraciones > Estilos** puede personalizar:
- Color primario
- Color secundario
- Color de acento

### Logotipo

Suba su logotipo desde **Configuraciones > General**.

### Configuración de Correo

Configure el servidor SMTP desde **Configuraciones > Correo**.

### PayPal

Configure sus credenciales de PayPal desde **Configuraciones > PayPal**.

## 🗺️ URLs Amigables

El sistema utiliza URLs amigables con el siguiente patrón:

| Ruta | Descripción |
|------|-------------|
| `/dashboard` | Panel principal |
| `/login` | Inicio de sesión |
| `/registro` | Registro de usuarios |
| `/sucursales` | Gestión de sucursales |
| `/especialistas` | Gestión de especialistas |
| `/servicios` | Gestión de servicios |
| `/reservaciones` | Gestión de citas |
| `/calendario` | Vista de calendario |
| `/reportes` | Reportes y estadísticas |
| `/configuraciones` | Configuración del sistema |

## 📊 Datos de Ejemplo

El esquema SQL incluye datos de ejemplo del estado de Querétaro, México:

- 3 sucursales en Querétaro
- 6 categorías de servicios
- 15 servicios predefinidos
- 3 especialistas con horarios
- Días feriados de México

## 🔒 Seguridad

- Contraseñas hasheadas con `password_hash()`
- Protección contra SQL Injection con PDO prepared statements
- Sanitización de entradas con `htmlspecialchars()`
- Tokens CSRF en formularios
- Bitácora de acciones de usuarios
- Control de acceso por roles

## 🤝 Contribuir

1. Fork el repositorio
2. Cree una rama para su feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit sus cambios (`git commit -am 'Agrega nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abra un Pull Request

## 📝 Licencia

Este proyecto está bajo la Licencia MIT. Vea el archivo `LICENSE` para más detalles.

## 📞 Soporte

Para soporte o consultas:
- Abra un issue en GitHub
- Contacte al equipo de desarrollo

---

<p align="center">
  Desarrollado con ❤️ para la gestión eficiente de citas y reservaciones
</p>
