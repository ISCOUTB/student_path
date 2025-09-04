# Release Workflow Documentation

Este documento explica cómo funciona el sistema automatizado de releases para el bloque Student Path.

## Workflows Disponibles

### 1. Build Development Package (`build.yml`)
- **Trigger**: Se ejecuta en cada push a las ramas `main` y `develop`
- **Propósito**: Crear paquetes de desarrollo para testing
- **Artefactos**: ZIP files con información de build incluida
- **Retención**: 30 días

### 2. Create Release Package (`release.yml`)
- **Trigger**: Se ejecuta cuando se crea un tag que comience con 'v'
- **Propósito**: Crear releases oficiales en GitHub
- **Artefactos**: Release oficial con ZIP adjunto

## Cómo Crear un Release

### Método 1: Release Automático con Tags
1. Actualiza la versión en `version.php`
2. Commit los cambios
3. Crea y pushea un tag:
   ```bash
   git tag v1.1
   git push origin v1.1
   ```
4. El workflow automáticamente creará un release en GitHub

### Método 2: Release Manual
1. Ve a la pestaña "Actions" en GitHub
2. Selecciona "Create Release Package"
3. Click en "Run workflow"
4. Selecciona la rama y ejecuta

## Estructura del Paquete

Los paquetes generados incluyen:
- Todos los archivos del bloque
- Estructura de directorios preservada
- Exclusión de archivos de desarrollo (.git, .github, etc.)
- Para builds de desarrollo: archivo `BUILD_INFO.txt` con información del commit

## Naming Convention

- **Development builds**: `block_student_path_{release}_{commit_short}.zip`
- **Official releases**: `block_student_path_{release}.zip`

## Instalación de Releases

1. Descarga el archivo ZIP desde GitHub Releases o Artifacts
2. Extrae el contenido en el directorio `blocks/` de tu instalación Moodle
3. Visita la página de administración de Moodle para completar la instalación

## Troubleshooting

- Si el workflow falla, revisa los logs en la pestaña "Actions"
- Asegúrate de que `version.php` tenga el formato correcto
- Verifica que los permisos del repositorio permitan crear releases

## Notas Importantes

- Los development builds son para testing únicamente
- Solo usa releases oficiales en producción
- Los artifacts de development se eliminan automáticamente después de 30 días
