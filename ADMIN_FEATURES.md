# Student Path Block - Funcionalidad de Administración

## 🔧 Nueva Funcionalidad: Gestión Administrativa

Se ha agregado una funcionalidad completa para que los administradores del sitio puedan gestionar las participaciones de estudiantes en el bloque Student Path.

### ✨ Características Principales

#### 1. **Página de Administración (`admin_manage.php`)**
- **Ubicación**: `/blocks/student_path/admin_manage.php`
- **Acceso**: Solo administradores con capacidad `moodle/site:config`
- **Funcionalidades**:
  - Lista todas las participaciones de estudiantes
  - Muestra información del estudiante (nombre, email, fechas)
  - Permite eliminación individual y en masa
  - Confirmación antes de eliminar
  - Selección múltiple con checkboxes
  - Contador total de participaciones

#### 2. **Vista Detallada de Usuario (`admin_view_user.php`)**
- **Ubicación**: `/blocks/student_path/admin_view_user.php`
- **Acceso**: Solo administradores
- **Funcionalidades**:
  - Muestra todas las participaciones de un usuario específico
  - Vista completa del mapa de identidad
  - Información organizada por secciones
  - Navegación de regreso a la página principal

#### 3. **Integración en el Sistema**
- **Enlace en Menu**: Agregado en Administración → Plugins → Bloques
- **Panel de Administrador**: Visible en `view.php` para administradores
- **Permisos**: Verificación de capacidades apropiadas

### 📍 Acceso a la Funcionalidad

#### Para Administradores del Sitio:

**Método 1: Desde el Menu de Administración**
```
Administración → Plugins → Bloques → Gestión de Mapas de Identidad
```

**Método 2: URL Directa**
```
[TU_MOODLE]/blocks/student_path/admin_manage.php
```

**Método 3: Desde el Bloque**
- Cuando un administrador ve el bloque, aparece un panel azul con enlace directo

### 🎯 Funcionalidades Detalladas

#### **Eliminación Individual**
1. Click en "Eliminar" junto al estudiante
2. Confirmación con mensaje personalizado
3. Eliminación de todos los datos del estudiante

### 🔒 **Seguridad Mejorada**

**Eliminación Solo Individual**: Por razones de seguridad, se ha eliminado la funcionalidad de eliminación múltiple. Los administradores solo pueden eliminar participaciones de una en una, lo que previene eliminaciones accidentales masivas.

#### **Vista de Detalles**
- Click en "Ver" para ver el mapa completo del estudiante
- Información organizada por secciones
- Datos legibles con formato apropiado

### 🔒 Seguridad

- **Verificación de Permisos**: Solo usuarios con `moodle/site:config`
- **Token de Sesión**: Todas las acciones usan `sesskey()`
- **Confirmación**: Doble verificación antes de eliminar
- **Escapado de HTML**: Protección contra XSS

### 🌐 Multiidioma

Todas las cadenas están disponibles en:
- **Español** (`lang/es/block_student_path.php`)
- **Inglés** (`lang/en/block_student_path.php`)

### 📊 Información Mostrada

#### **Lista Principal**
- Nombre del estudiante (enlace al perfil)
- Email
- Fecha de creación
- Fecha de última modificación
- Acciones (Ver/Eliminar)

#### **Vista Detallada**
- Información personal completa
- Fortalezas y debilidades
- Áreas vocacionales
- Habilidades emocionales
- Metas (corto, mediano y largo plazo)
- Plan de acción detallado

### 🔄 Actualización

**Versión actual**: `2025090308`

Para activar la funcionalidad:
1. Actualizar Moodle desde Admin
2. La página aparecerá automáticamente en el menú
3. Los administradores verán el panel en el bloque

### 📝 Archivos Modificados/Creados

#### **Nuevos Archivos**
- `admin_manage.php` - Página principal de administración
- `admin_view_user.php` - Vista detallada de usuario
- `settings.php` - Configuración del menú

#### **Archivos Modificados**
- `view.php` - Agregado panel de administrador
- `lang/es/block_student_path.php` - Cadenas en español
- `lang/en/block_student_path.php` - Cadenas en inglés
- `version.php` - Actualizada versión del plugin

### ⚠️ Consideraciones

- **Eliminación Permanente**: Los datos eliminados no se pueden recuperar
- **Eliminación Individual Únicamente**: Por seguridad, solo se permite eliminar una participación a la vez
- **Respaldo Recomendado**: Hacer backup antes de eliminaciones
- **Performance**: La página es eficiente incluso con muchos registros
- **Compatibilidad**: Funciona con Moodle 3.0+

### 🚀 Uso Recomendado

1. **Auditoría Regular**: Revisar participaciones periódicamente
2. **Eliminación Cuidadosa**: Eliminar registros de estudiantes de uno en uno para mayor control
3. **Monitoreo**: Usar la vista detallada para seguimiento académico
4. **Respaldos**: Exportar datos antes de cualquier eliminación

Esta funcionalidad proporciona a los administradores control seguro sobre las participaciones en el bloque Student Path, priorizando la seguridad sobre la conveniencia para evitar eliminaciones accidentales masivas.
