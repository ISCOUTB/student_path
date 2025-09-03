# Student Path Block

Este repositorio contiene el bloque "Student Path" para Moodle, diseñado para ayudar a los estudiantes y profesores a visualizar y gestionar el progreso académico dentro de un curso.

## Características principales
- Visualización del perfil y progreso del estudiante.
- Exportación de datos relevantes.
- Interfaz para profesores y estudiantes.
- Soporte multilenguaje (español e inglés).
- Instalación y acceso a la base de datos propia del bloque.
- Estilos personalizados para una mejor experiencia de usuario.
- Integración con AJAX para operaciones dinámicas.

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
