# Student Path Block - v2.0

Este repositorio contiene el bloque "Student Path" para Moodle, diseñado para ayudar a los estudiantes a completar su **Mapa de Identidad** y a los profesores a gestionar y visualizar el progreso académico y personal de sus estudiantes.

## Características principales
- **Mapa de Identidad Estudiantil** completo con estructura renovada
- Sistema de preguntas estructuradas siguiendo metodología SMART
- Visualización del perfil y progreso del estudiante
- Panel administrativo para profesores con estadísticas
- Exportación de datos en formato CSV/Excel
- Interfaz responsiva para profesores y estudiantes
- Soporte multilenguaje (español e inglés)
- Instalación y actualización automática de base de datos
- Estilos modernos integrados con Moodle
- Operaciones AJAX para mejor experiencia de usuario

## Estructura de Preguntas - v2.0

### 1. Información Personal
- Nombre (automático desde Moodle)
- Programa académico
- Año de ingreso a la UTB
- Email (automático desde Moodle) 
- Código estudiantil

### 2. ¿Quién soy y qué he descubierto sobre mí?
- **2.1** Aspectos de tu personalidad (Fortalezas)
- **2.2** Aspectos de tu personalidad (Debilidades)
- **2.3** Áreas Vocacionales (selección múltiple):
  - C (Ciencias Exactas, Administrativas y Contables)
  - H (Humanística y Ciencias Sociales)
  - A (Artísticas)
  - S (Ciencias de la Salud y Medicina)
  - I (Ingenierías y Computación)
  - D (Defensa y Seguridad)
  - E (Ciencias Agrarias y Naturales)
- **2.3.1** Descripción de aptitudes e intereses del área escogida
- **2.4** Habilidades emocionales identificadas (percepción, comprensión y regulación)

### 3. Metas (Metodología SMART)
- **3.1** Logro a corto plazo (hasta 1 año)
- **3.2** Logro a mediano plazo (entre 1 y 2 años)
- **3.3** Logro a largo plazo (más de 2 años)

*Incluye guía SMART: Específica, Medible, Alcanzable, Relevante, Temporal*

### 4. Plan de Acción
- **4.1** Acciones para alcanzar la meta a corto plazo
- **4.2** Acciones para alcanzar la meta a mediano plazo
- **4.3** Acciones para alcanzar la meta a largo plazo

*Plantilla: [Verbo infinitivo] + [qué] + [con quién/dónde/cómo] + [para qué]*

## Estructura del proyecto
- `block_student_path.php`: Archivo principal del bloque.
- `export.php`: Exporta datos del bloque.
- `lib.php`: Funciones auxiliares del bloque.
- `save.php`: Guarda información relevante.
- `styles.css`: Estilos del bloque.
- `teacher_view.php`: Vista para profesores.
- `test_db.php`: Pruebas de la base de datos.
- `version.php`: Versión del bloque.
- `view.php`: Vista principal del bloque.
- `ajax/`: Operaciones AJAX (ej. `get_student_profile.php`).
- `db/`: Instalación y acceso a la base de datos (`install.php`, `access.php`).
- `lang/`: Archivos de idioma (`en`, `es`).

## Instalación
1. Copia la carpeta del bloque en el directorio de bloques de tu instalación de Moodle.
2. Accede como administrador y sigue el proceso de instalación de plugins.
3. Configura el bloque según tus necesidades.

## Uso
- Añade el bloque "Student Path" en el curso deseado.
- Accede a las vistas de estudiante o profesor para gestionar y visualizar el progreso.

## Contribución
Las contribuciones son bienvenidas. Por favor, abre un issue o pull request para sugerencias o mejoras.

## Licencia
Este proyecto está bajo la licencia ISCOUTB.
