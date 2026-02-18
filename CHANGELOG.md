# CHANGELOG

Todas las modificaciones importantes del proyecto se documentarán en este archivo.

## [2.0.3] - 2026-02-18
- Se corrigió un bug de validación cruzada que bloqueaba permanentemente el guardado automático cuando estudiantes omitían el campo "Año de Ingreso".

## [2.0.2] - 2026-01-18
- Se eliminaron las comprobaciones redundantes de administrador (`is_siteadmin()`) en `lib.php`, mejorando la detección correcta de roles locales (profesores vs estudiantes) y el sistema de permisos basado en capacidades.
- Se corrigió el estilo del botón principal del bloque en `student_view.mustache` y `styles.css` para asegurar una apariencia consistente con los otros bloques.

## [2.0.1] — 2026-01-18
- Opción para mostrar/ocultar las descripciones en el bloque principal.
- Se mantiene el titulo "Mapa de Identidad" en todas las vistas del bloque.
- Se ha eliminado las referencias a la palabra "Test".

## [2.0.0] — 2026-01-08
- A partir de la versión 2.0.0 se comienza a documentar este CHANGELOG.
- Diseñado desde cero mediante una renovación completa y moderna de la UI/UX del bloque, mapa, panel de administración y vistas individuales (Todas con diseño responsivo).
- Mejora en la experiencia/flujo de usuario (Profesores y Estudiantes).
- Guardado automático de respuestas y progreso.
- Uso de logos institucionales y paleta de colores oficial.
- Soporte para múltiples idiomas (Español e Inglés).
- Consistencia con los otros bloques (chaside, learning_style, personality_test y tmms_24).
- Seguridad mejorada.
- Optimización del rendimiento.
- Corrección de errores menores.
