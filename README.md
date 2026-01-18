# Bloque Mapa de Identidad (Moodle)

El bloque **Mapa de Identidad** actúa como un portafolio personal y bitácora de crecimiento para el estudiante. Centraliza información académica, resultados de autodescubrimiento (vocacional, emocional, personalidad) y permite la construcción de un plan de vida mediante metas SMART y planes de acción concretos.

Este repositorio incluye:
- **Experiencia de estudiante** con formulario de 4 secciones, guardado automático, historial de versiones por semestre.
- **Herramientas para docentes** con dashboard de seguimiento, visualización de perfiles individuales y overlays de resultados de otros instrumentos psicométricos.

## Contenido

- [Funcionalidades](#funcionalidades)
- [Recorrido Visual](#recorrido-visual)
- [Sección técnica](#sección-técnica)
- [Instalación](#instalación)
- [Operación y soporte](#operación-y-soporte)
- [Contribuciones](#contribuciones)
- [Equipo de desarrollo](#equipo-de-desarrollo)

---

## Funcionalidades

### Para estudiantes
- **Mapa de Identidad Interactivo**: Formulario dividido en 4 pilares:
  1. **Información Personal**: Datos básicos y de programa.
  2. **Autodescubrimiento**: Reflexión sobre fortalezas, debilidades, áreas vocacionales y habilidades socioemocionales.
  3. **Metas SMART**: Definición guiada de objetivos a corto, mediano y largo plazo.
  4. **Plan de Acción**: Estrategias concretas para alcanzar las metas propuestas.
- **Guardado Automático**: Persistencia de datos en tiempo real para evitar pérdida de información.
- **Historial de Versiones**: Sistema de snapshots que congela el mapa al finalizar un semestre, permitiendo al estudiante ver su evolución en el tiempo (comparativa histórica).
- **Alertas de Nuevo Semestre**: Notificaciones que invitan a actualizar el mapa al detectar un cambio de periodo académico.

### Para docentes / administradores
- **Dashboard de Curso**: Métricas de participación (iniciados, en progreso, completados).
- **Gestión de Estudiantes**: Tabla con estado de avance y acceso rápido al perfil de cada alumno.
- **Overlays de Información**: Dentro del Panel de Administración, el docente puede consultar en ventanas emergentes los resultados por cada uno de los tests integrados (Estilos de Aprendizaje, Personalidad, Orientación Vocacional y Habilidades Socioemocionales). Además en la lista de estudiantes en la columna "Progreso del Test", al hacer click en cada uno de los cuadritos, puede ver los resultados o progreso del respectivo estudiante.
- **Vista Detallada**: Acceso de lectura al mapa completo del estudiante.

---
## Recorrido Visual

### 1. Experiencia del Estudiante

**Invitación y Acceso desde el Bloque**

El estudiante visualiza el estado actual de su mapa desde el bloque lateral. Si nunca lo ha iniciado, se presenta una invitación clara con un botón "Iniciar mapa de Identidad".

<p align="center">
  <img src="https://github.com/user-attachments/assets/ac96e81a-086b-40fc-9b80-bb568ffb5730" alt="Invitación al Mapa" width="528">
</p>

Si el estudiante ya ha avanzado previamente, el bloque muestra el progreso actual e invita a completar.

<p align="center">
  <img src="https://github.com/user-attachments/assets/1ee65a47-4e93-4b1b-87be-fd939feeab43" alt="Progreso en el Bloque" width="528">
</p>

Una vez completado, el estado cambia a "Completado", permitiendo ver o editar la información existente.

<p align="center">
  <img src="https://github.com/user-attachments/assets/8ae57e66-eaa5-4ee6-a8f1-4c9714f56c7c" alt="Bloque Completado" width="528">
</p>

**Formulario del Mapa de Identidad**

Un formulario organizado donde el estudiante reflexiona sobre su identidad. Incluye secciones para datos personales, fortalezas/debilidades (Autodescubrimiento), y planificación de futuro.

<p align="center">
  <img src="https://github.com/user-attachments/assets/ea81be42-3859-40b6-8d63-436b38b7d8fa" alt="Formulario Mapa de Identidad" width="800">
</p>

**Gestión de Periodos y Versiones**

El sistema detecta el cambio de semestre. Si el estudiante ingresa en un nuevo periodo académico, recibe notificaciones diferenciadas según su historial en la plataforma:

1. **Primera renovación:** Cuando el estudiante va a generar su segunda versión histórica, se le invita a actualizar sus metas para el nuevo ciclo.

<p align="center">
  <img src="https://github.com/user-attachments/assets/6341705d-92e7-4806-8aec-b5200f724617" alt="Alerta Nuevo Semestre 1" width="600">
</p>

2. **Trayectoria continua:** Si el estudiante ya cuenta con 2 o más versiones en su historial, la alerta reconoce su recorrido acumulado y le invita a seguir construyendo su bitácora de crecimiento.

<p align="center">
  <img src="https://github.com/user-attachments/assets/4bab3895-df03-431b-ab58-3eee1ec198ce" alt="Alerta Nuevo Semestre 2" width="600">
</p>

**Historial de Versiones**

Tanto en escritorio como en móvil, el estudiante puede consultar sus mapas de semestres anteriores, permitiéndole ver cómo han evolucionado sus metas y autopercepción a lo largo de la carrera.

<p align="center">
  <img src="https://github.com/user-attachments/assets/f1f63e91-c01f-4cdc-b808-7820afad1408" alt="Historial PC" height="350">
  <img src="https://github.com/user-attachments/assets/1d49c0c1-f00f-48b1-83d3-087d555a93f6" alt="Historial Móvil" height="350">
</p>

### 2. Experiencia del Profesor / Administrador

**Vista de Bloque Docente**

El profesor tiene acceso rápido a estadísticas y panel de administración desde el mismo bloque.

<p align="center">
  <img src="https://github.com/user-attachments/assets/46d51594-a6df-4fce-bacc-e992555cba04" alt="Bloque Profesor" width="528">
</p>

**Panel de Administración**

Ofrece un resumen general del avance del grupo y progreso por test.

<p align="center">
  <img src="https://github.com/user-attachments/assets/e2528f0b-b2a2-4483-83af-c853936e5e8c" alt="Panel Admin 1" width="800">
</p>

Muestra la lista de estudiantes matriculados, su estado con respecto al Mapa de Identidad y opciones para ver detalles.

<p align="center">
  <img src="https://github.com/user-attachments/assets/957a2bff-3cc4-4852-b5cf-7f1421e77712" alt="Panel Admin 2" width="800">
</p>

**Tarjetas de Estudiante Expandidas**

Al seleccionar una tarjeta de la sección "Progreso por Test" en el Panel de Administración, se despliega una vista con sus tres estados (Completado, En Progreso y No Iniciado) y se pueden ver los estudiantes que están en cada uno.

<p align="center">
  <img src="https://github.com/user-attachments/assets/5b94660d-60c3-4543-862f-39b698e9dcde" alt="Tarjeta Expandida" width="600">
</p>

Una característica potente es la capacidad de consultar resultados sin salir de esta vista. El docente puede ver overlays con la información al hacer click en el respectivo nombre del estudiante (Solo disponible en los estados "Completado" y "En Progreso").

*Overlay de Estilos de Aprendizaje:*
<p align="center">
  <img src="https://github.com/user-attachments/assets/860cbdc9-d5d0-4213-a64f-bc7a2da5b73c" alt="Overlay Learning Style" width="600">
</p>

*Overlay de Test de Personalidad:*
<p align="center">
  <img src="https://github.com/user-attachments/assets/974ffeec-fce1-4f3d-975d-72f4bbb86cb1" alt="Overlay Personalidad" width="600">
</p>

*Overlay de Orientación Vocacional (CHASIDE):*
<p align="center">
  <img src="https://github.com/user-attachments/assets/30baf205-1afd-42a3-b684-007ef2186054" alt="Overlay CHASIDE" width="600">
</p>

*Overlay de Inteligencia Emocional (TMMS-24):*
<p align="center">
  <img src="https://github.com/user-attachments/assets/18fa3543-8ce0-4310-902d-bcc39090a110" alt="Overlay TMMS24" width="600">
</p>

*Overlay del Mapa de Identidad del Estudiante:*
<p align="center">
  <img src="https://github.com/user-attachments/assets/6ae96d7c-ded9-49a8-8bbf-c6aabca1d64c" alt="Overlay Identity Map" width="600">
</p>

Si el estudiante está en progreso, el sistema lo indica claramente.

<p align="center">
  <img src="https://github.com/user-attachments/assets/d53dd079-6643-4943-8120-cdfbce68ade2" alt="Overlay En Progreso" width="600">
</p>

**Vista Detallada del Perfil**

El docente puede acceder a la lectura completa de todos los resultados en cada uno de los test de un estudiante y lo que este ha escrito en su mapa, facilitando la consejería y el acompañamiento tutorial.

<p align="center">
  <img src="https://github.com/user-attachments/assets/a347e837-eb28-48c7-b28a-b9b2702002fd" alt="Ver Perfil 1" width="800">
  <br>
  <img src="https://github.com/user-attachments/assets/b9ef6a24-aae7-4487-b185-b87008a02d71" alt="Ver Perfil 2" width="800">
</p>

---

## Sección técnica

### 1) Arquitectura y Flujo de Datos

El bloque opera bajo un modelo híbrido donde la interfaz principal se renderiza en PHP (`view.php`, `admin_view.php`), pero la interactividad (validaciones, overlays, guardado) depende fuertemente de AJAX.

- **Backend de Autoservicio (`ajax_get_test_details.php`)**:
  Este endpoint actúa como un controlador centralizado para obtener los detalles de cualquier test integrado. Recibe un `test_type` y un `user_id`, realiza rigurosas verificaciones de seguridad "Anti-Gossip" (valida que el solicitante sea profesor y que el estudiante esté matriculado en el mismo curso) y devuelve el HTML pre-renderizado del overlay. Esto permite que el frontend sea ligero, cargando la información pesada solo bajo demanda.

- **Sistema de Persistencia (`save_auto.php`)**:
  El guardado no requiere un botón de "Enviar". Un script detecta inactividad en los campos de texto y envía los datos asíncronamente.
  - **Detección de Completitud**: En cada guardado, el servidor verifica si todos los campos obligatorios (Información Personal, Autodescubrimiento, Metas SMART y Planes de Acción) tienen contenido. Si es así, marca el registro como `is_completed = 1`.
  - **Lógica de Snapshot**: Al guardar, el sistema verifica si el "semestre actual" del servidor difiere del último registrado por el usuario. Si hay discrepancia, congela el estado actual en la tabla `history` antes de permitir nuevas ediciones, garantizando la inmutabilidad de los registros pasados.

### 2) Integraciones (Loose Coupling)

El bloque está diseñado para coexistir en un ecosistema modular sin dependencias duras que rompan el sistema.

- **Detección de Tablas**: En lugar de depender de las APIs de los otros plugins, el bloque consulta directamente la base de datos (`$DB->get_record`) para verificar la existencia de tablas hermanas (`block_learning_style`, `block_chaside_responses`, etc.).
- **Cálculo de lugares en CHASIDE y TMMS 24**: El bloque replica las fórmulas oficiales de puntuación dentro de su propio código, evitando la necesidad de llamar a funciones externas. 

### 3) Modelo de Datos

El almacenamiento se divide para optimizar el rendimiento y la trazabilidad:

- **`block_student_path`** (Estado "Vivo"):
  - Índice único por `user`.
  - Almacena el borrador actual editable.
  - Campos de texto (`text`) para reflexiones largas y metas.
  - Campos dimensionales normalizados para reportes.

- **`block_student_path_history`** (Almacenamiento en Frío):
  - Almacena snapshots JSON completos del estado del mapa en un momento del tiempo.
  - Clave compuesta `userid` + `period` (ej. '2025-1').
  - Permite reconstruir la vista del estudiante tal cual estaba en semestres pasados sin alterar la estructura actual de la base de datos.

### 4) Seguridad y Permisos

El sistema implementa capas de defensa en profundidad:

- **Contexto de Curso**: Todas las vistas administrativas (`admin_view.php`) requieren un `courseid` válido y verifican la capacidad `block/student_path:viewreports` en ese contexto específico.
- **Sanitización de Salida**: Al exportar datos o mostrar nombres, se utiliza `fullname()` y funciones de escape de Moodle para prevenir XSS.
- **Validación de Matrícula**: Incluso si un usuario malintencionado adivina una URL de AJAX, el backend verifica `is_enrolled` para asegurar que solo los profesores legítimos del alumno puedan acceder a sus datos sensibles.
- **CSRF Protection**: Todas las llamadas AJAX (`save_auto.php`, `ajax_get_test_details.php`) están protegidas estrictamente mediante `sesskey`.

---

## Experiencia de Usuario Avanzada

### 1) Traducción Automática en el Cliente (Chrome Built-in AI)
El bloque es pionero en el uso de **Translator API in Chrome** experimental. Esta funcionalidad se aplica específicamente a los **campos de texto libre (text-areas)** del Mapa de Identidad del estudiante cuando son visualizados por el profesor en:
- Los **overlays** (ventanas emergentes) dentro del panel de administración.
- La vista de **perfil detallado** (`view_profile.php`).

**Cómo funciona:**
- El sistema detecta automáticamente el idioma de Moodle que se encuentra del profesor.
- Analiza el texto escrito por el estudiante usando heurísticas inteligentes o la API de detección de IA del navegador.
- Si hay discordancia lingüística (ej. Profesor en inglés revisando a un estudiante que escribió en español), el texto se **traduce instantáneamente** en el cliente, añadiendo una etiqueta discreta: *"Traducido automáticamente"*.
- **Privacidad**: Al ejecutarse en el navegador (`self.Translator`), los datos sensibles del estudiante nunca abandonan el entorno local.
- **Degradación Elegante**: Si el navegador del usuario no soporta esta tecnología experimental, la interfaz **se mantiene intacta** mostrando el texto original, asegurando que la experiencia visual y funcional nunca se vea comprometida.

### 2) Interfaz Animada y Reactiva
Se ha cuidado cada interacción para garantizar una experiencia moderna:
- **Micro-interacciones**: Efectos de **elevación y escalado** al pasar el cursor sobre las tarjetas de estadísticas estadisticas y tests (`.stat-card:hover`, `.test-card:hover`, etc...).
- **Feedback Visual**: Transiciones suaves en barras de progreso y botones.
- **Gradientes Semánticos**: Uso de gradientes lineales específicos para cada módulo (Amarillo para Orientación Vocacional, Azul para Estilos de Aprendizaje, Verde para Personalidad, Naranja para Habilidades Socioemocionales, Violeta para Mapa de Identidad), ayudando a la rápida identificación visual.
- **Diseño Responsive Integral**: Tanto la vista del estudiante (formulario del mapa) como el panel de administración del docente se adaptan fluidamente a pantallas móviles, tablets y escritorio, permitiendo el uso del bloque desde cualquier dispositivo.

### 3) Soporte Multilenguaje (i18n)
El bloque es nativamente bilingüe (**Español** e **Inglés**). Todos los textos, desde las etiquetas del formulario hasta los mensajes de error AJAX, utilizan el sistema de cadenas de Moodle (`get_string`), permitiendo la fácil adición de nuevos idiomas en el futuro.

---
## Instalación
1. Tener instalados los bloques dependientes (Estilos de Aprendizaje, Personalidad, Orientación Vocacional y Habilidades Socioemocionales)
2. Descargar el plugin desde las *releases* del repositorio oficial: https://github.com/ISCOUTB/student_path
3. En Moodle (como administrador):
   - Ir a **Administración del sitio → Extensiones → Instalar plugins**.
   - Subir el archivo ZIP.
   - Completar el asistente de instalación.
4. En un curso, agregar el bloque **Mapa de Identidad** desde el selector de bloques.
---

## Operación y soporte

### Consideraciones de despliegue

- **Versión de Moodle**: Compatible con Moodle 4.0+.
- **Versión de PHP**: PHP 7.4+ recomendado.
- **JavaScript**: Requerido para el funcionamiento de los modales y guardado AJAX.
- **Integridad de Datos**: Si un bloque dependiente es desinstalado, el bloque mostrará errores.

### Resolución de problemas (rápido)

- **El estudiante no ve el mapa**: validar que tenga la capacidad `block/student_path:makemap` en el contexto del curso.
- **El docente no ve reportes**: validar `block/student_path:viewreports`.
- **El dashboard no carga**: revisar que el navegador permita `fetch` con credenciales y que el `sesskey` sea válido.

---

## Contribuciones
¡Las contribuciones son bienvenidas! Si deseas mejorar este bloque, por favor sigue estos pasos:
1. Haz un fork del repositorio.
2. Crea una nueva rama para tu característica o corrección de errores.
3. Realiza tus cambios y asegúrate de que todo funcione correctamente.
4. Envía un pull request describiendo tus cambios.

---
## Equipo de desarrollo
- Jairo Enrique Serrano Castañeda
- Yuranis Henriquez Núñez
- Isaac David Sánchez Sánchez
- Santiago Andrés Orejuela Cueter
- María Valentina Serna González

<div align="center">
<strong>Con ❤️ para la Universidad Tecnológica de Bolívar</strong>
</div>
