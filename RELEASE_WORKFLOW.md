# Release Workflow Documentation

Este documento explica cómo funciona el sistema automatizado de releases para el bloque Student Path.

## Workflows Disponibles

### 1. Build Development Package (`build.yml`)
- **Trigger**: Se ejecuta en cada push a las ramas `main` y `develop`
- **Propósito**: Crear paquetes de desarrollo para testing
- **Artefactos**: ZIP files con información de build incluida
- **Retención**: 30 días

### 2. Create Release Package (`release.yml`)
- **Trigger**: Se ejecuta automáticamente en cada push a `main` O manualmente
- **Propósito**: Crear releases oficiales en GitHub con descarga directa
- **Artefactos**: Release oficial con ZIP adjunto automáticamente

## Cómo Funciona el Release Automático

### Método 1: Release Automático (RECOMENDADO)
**¡Simplemente haz push a main y listo!** 🚀

1. Actualiza la versión en `version.php` si es necesario
2. Commit y push a main:
   ```bash
   git add .
   git commit -m "Nueva funcionalidad"
   git push origin main
   ```
3. **¡El release se crea automáticamente!** El workflow:
   - Detecta la versión en `version.php`
   - Crea automáticamente el tag `v{release}`
   - Genera el ZIP del bloque
   - Crea el release en GitHub con descarga directa

### Método 2: Release Manual
1. Ve a "Actions" > "Create Release Package"
2. Click "Run workflow"
3. Mantén "Create GitHub Release" marcado
4. Click "Run workflow"

## ¿Qué Se Genera Automáticamente?

Cada push a `main` crea:
- **Tag automático**: `v{release}` (ej: `v1.0`)
- **Release en GitHub**: "Student Path Block v{release}"
- **Archivo ZIP**: `block_student_path_v{release}.zip`
- **Descripción completa**: Con instrucciones de instalación y detalles técnicos

## Estructura del Paquete

Los paquetes generados incluyen:
- Todos los archivos del bloque
- Estructura de directorios preservada
- Exclusión de archivos de desarrollo (.git, .github, build/, etc.)
- Para builds de desarrollo: archivo `BUILD_INFO.txt` con información del commit

## Naming Convention

- **Development builds**: `block_student_path_{release}_{commit_short}.zip`
- **Official releases**: `block_student_path_v{release}.zip`

## Ejemplo Práctico

### Escenario: Quiero crear release v1.2

```bash
# 1. Editar version.php
# $plugin->release = '1.2';

# 2. Commit y push (¡ESO ES TODO!)
git add version.php
git commit -m "Bump to version 1.2"
git push origin main

# 3. Ve a GitHub > Releases
# ¡Ya tienes tu release v1.2 con ZIP descargable!
```

## Verificación del Release

1. **Durante el proceso**: Ve a "Actions" para ver el progreso
2. **Release completado**: Ve a "Releases" para la descarga directa
3. **Archivo disponible**: `block_student_path_v{release}.zip`

## Ventajas del Sistema Actualizado

✅ **Un solo paso**: Solo push a main
✅ **Tags automáticos**: No necesitas crearlos manualmente  
✅ **Descarga directa**: ZIP disponible inmediatamente en Releases
✅ **Versionado automático**: Lee la versión de `version.php`
✅ **Documentación rica**: Release notes automáticas con instrucciones

## Instalación de Releases

1. Ve a "Releases" en GitHub
2. Descarga `block_student_path_v{version}.zip`
3. Extrae el contenido en el directorio `blocks/` de tu instalación Moodle
4. Visita la página de administración de Moodle para completar la instalación

## Troubleshooting

- **El workflow falla**: Revisa los logs en "Actions"
- **Formato de version.php**: Asegúrate que tenga `$plugin->release = '1.0';`
- **Permisos**: Verifica que el repositorio permita crear releases
- **Tag duplicado**: El sistema detecta tags existentes y no los duplica

## Notas Importantes

- **🔥 AUTOMÁTICO**: Cada push a `main` = nuevo release
- **📦 DESCARGA DIRECTA**: ZIP disponible inmediatamente en Releases  
- **🏷️ TAGS AUTOMÁTICOS**: Se crean basados en `version.php`
- **🚀 PRODUCTION READY**: Los releases son seguros para producción
- **⏰ DEVELOPMENT BUILDS**: Siguen disponibles en Artifacts para testing
