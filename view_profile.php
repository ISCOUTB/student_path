<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

require_login();

$userid = required_param('uid', PARAM_INT);
$courseid = required_param('cid', PARAM_INT);

// Verificar acceso del profesor
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($courseid);
require_capability('moodle/course:manageactivities', $context);

// Obtener datos del estudiante
$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

// Obtener perfil integrado del estudiante
$profile = get_integrated_student_profile($userid, $courseid);

// Configurar página
$PAGE->set_url(new moodle_url('/blocks/student_path/view_profile.php', array('uid' => $userid, 'cid' => $courseid)));
$PAGE->set_context($context);
$PAGE->set_title(get_string('integrated_student_profile', 'block_student_path') . ': ' . fullname($user));
$PAGE->set_heading(get_string('integrated_student_profile', 'block_student_path') . ': ' . fullname($user));

// Agregar CSS personalizado
$PAGE->requires->css('/blocks/student_path/styles.css');

// Mostrar header
echo $OUTPUT->header();

// Breadcrumb navigation
echo '<nav aria-label="breadcrumb" class="mb-4">';
echo '<ol class="breadcrumb">';
echo '<li class="breadcrumb-item"><a href="' . new moodle_url('/course/view.php', array('id' => $courseid)) . '">' . $course->fullname . '</a></li>';
echo '<li class="breadcrumb-item"><a href="' . new moodle_url('/blocks/student_path/teacher_view.php', array('cid' => $courseid)) . '">' . get_string('identity_map_title', 'block_student_path') . '</a></li>';
echo '<li class="breadcrumb-item active" aria-current="page">' . fullname($user) . '</li>';
echo '</ol>';
echo '</nav>';

echo '<div class="integrated-profile-container">';

// Encabezado del estudiante
echo '<div class="student-header">';
echo '<div class="row align-items-center">';
echo '<div class="col-md-8">';
echo '<h2 class="mb-2">' . fullname($user) . '</h2>';
echo '<p class="text-muted mb-0">' . $user->email . '</p>';
echo '</div>';
echo '<div class="col-md-4 text-end">';
echo '<div class="completion-overview">';
echo '<div class="completion-circle" data-percentage="' . $profile->completion_percentage . '">';
echo '<span class="completion-text">' . $profile->completion_percentage . '%</span>';
echo '</div>';
echo '<p class="small text-muted mt-2">' . get_string('profile_completion', 'block_student_path') . '</p>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

// Indicadores de estado
echo '<div class="evaluation-status-bar">';
echo '<div class="status-indicators">';

// Student Path
echo '<div class="status-item ' . ($profile->holland_type ? 'completed' : 'pending') . '">';
echo '<div class="status-icon">SP</div>';
echo '<div class="status-label">' . get_string('student_path_test', 'block_student_path') . '</div>';
echo '</div>';

// Learning Style
echo '<div class="status-item ' . ($profile->learning_style ? 'completed' : 'pending') . '">';
echo '<div class="status-icon">LS</div>';
echo '<div class="status-label">' . get_string('learning_style_test', 'block_student_path') . '</div>';
echo '</div>';

// Personality Test
echo '<div class="status-item ' . ($profile->personality_traits ? 'completed' : 'pending') . '">';
echo '<div class="status-icon">PT</div>';
echo '<div class="status-label">' . get_string('personality_test', 'block_student_path') . '</div>';
echo '</div>';

// TMMS-24 (Emotional Intelligence)
echo '<div class="status-item ' . ($profile->emotional_intelligence ? 'completed' : 'pending') . '">';
echo '<div class="status-icon">EI</div>';
echo '<div class="status-label">' . get_string('tmms_24_test', 'block_student_path') . '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

// Contenido principal en cuatro columnas
echo '<div class="row mt-4">';

// Primera columna: Identidad Vocacional (Student Path)
echo '<div class="col-lg-3">';
echo '<div class="profile-section">';
echo '<h4 class="section-title">';
echo '<i class="icon fa fa-compass"></i>';
echo get_string('vocational_identity', 'block_student_path');
echo '</h4>';

if ($profile->student_path_data) {
    echo '<div class="student-path-result">';
    
    // Mostrar Holland Type en una línea
    if (!empty($profile->holland_type)) {
        echo '<div class="holland-result-inline mb-3">';
        
        $holland_types = explode(',', $profile->holland_type);
        $primary_type = trim($holland_types[0]);
        
        // Mapear códigos a nombres y colores
        $holland_map = array(
            'R' => array('name' => 'Realista', 'class' => 'holland-realistic'),
            'I' => array('name' => 'Investigativo', 'class' => 'holland-investigative'),
            'A' => array('name' => 'Artístico', 'class' => 'holland-artistic'),
            'S' => array('name' => 'Social', 'class' => 'holland-social'),
            'E' => array('name' => 'Emprendedor', 'class' => 'holland-enterprising'),
            'C' => array('name' => 'Convencional', 'class' => 'holland-conventional')
        );
        
        if (isset($holland_map[$primary_type])) {
            echo '<p><strong>Tipo Holland:</strong> ';
            echo '<span class="holland-badge-inline ' . $holland_map[$primary_type]['class'] . '">' . $primary_type . '</span> ';
            echo $holland_map[$primary_type]['name'];
            echo '</p>';
        } else {
            echo '<p><strong>Tipo Holland:</strong> ' . htmlspecialchars($primary_type) . '</p>';
        }
        
        echo '</div>';
    }
    
    // Mostrar información académica
    if (!empty($profile->program) || !empty($profile->admission_year) || !empty($profile->code)) {
        echo '<div class="academic-info">';
        echo '<h6>Información Académica:</h6>';
        if (!empty($profile->program)) {
            echo '<p><strong>Programa:</strong> ' . htmlspecialchars($profile->program) . '</p>';
        }
        if (!empty($profile->admission_year)) {
            echo '<p><strong>Año de Ingreso:</strong> ' . htmlspecialchars($profile->admission_year) . '</p>';
        }
        if (!empty($profile->code)) {
            echo '<p><strong>Código:</strong> ' . htmlspecialchars($profile->code) . '</p>';
        }
        echo '</div>';
    }
    
    // Mostrar fortalezas y debilidades de personalidad
    if (!empty($profile->personality_strengths) || !empty($profile->personality_weaknesses)) {
        echo '<div class="personality-aspects">';
        echo '<h6>Aspectos de Personalidad:</h6>';
        if (!empty($profile->personality_strengths)) {
            echo '<div class="strengths">';
            echo '<strong>Fortalezas:</strong><br>';
            echo '<p class="text-muted small">' . htmlspecialchars($profile->personality_strengths) . '</p>';
            echo '</div>';
        }
        if (!empty($profile->personality_weaknesses)) {
            echo '<div class="weaknesses">';
            echo '<strong>Áreas de Mejora:</strong><br>';
            echo '<p class="text-muted small">' . htmlspecialchars($profile->personality_weaknesses) . '</p>';
            echo '</div>';
        }
        echo '</div>';
    }
    
    // Mostrar descripción vocacional
    if (!empty($profile->vocational_description)) {
        echo '<div class="vocational-description">';
        echo '<h6>Descripción Vocacional:</h6>';
        echo '<p class="text-muted small">' . htmlspecialchars($profile->vocational_description) . '</p>';
        echo '</div>';
    }
    
    // Mostrar nivel de habilidades emocionales
    if (!empty($profile->emotional_skills_level)) {
        echo '<div class="emotional-skills">';
        echo '<h6>Nivel de Habilidades Emocionales:</h6>';
        echo '<p><strong>' . htmlspecialchars($profile->emotional_skills_level) . '</strong></p>';
        echo '</div>';
    }
    
    echo '</div>';
} else {
    echo '<div class="no-data">';
    echo '<p class="text-muted">' . get_string('vocational_test_not_completed', 'block_student_path') . '</p>';
    echo '</div>';
}

echo '</div>';
echo '</div>';

// Segunda columna: Estilo de Aprendizaje
echo '<div class="col-lg-3">';
echo '<div class="profile-section">';
echo '<h4 class="section-title">';
echo '<i class="icon fa fa-graduation-cap"></i>';
echo get_string('learning_style', 'block_student_path');
echo '</h4>';

if ($profile->learning_style) {
    echo '<div class="learning-style-result">';
    $style_details = get_learning_style_summary($profile->learning_style_data);
    echo '<div class="learning-style-summary">' . $style_details . '</div>';
    
    echo '</div>';
} else {
    echo '<div class="no-data">';
    echo '<p class="text-muted">' . get_string('learning_style_test_not_completed', 'block_student_path') . '</p>';
    echo '</div>';
}

echo '</div>';
echo '</div>';

// Tercera columna: Personalidad
echo '<div class="col-lg-3">';
echo '<div class="profile-section">';
echo '<h4 class="section-title">';
echo '<i class="icon fa fa-user"></i>';
echo get_string('personality_profile', 'block_student_path');
echo '</h4>';

if ($profile->personality_traits) {
    echo '<div class="personality-result">';
    $personality_details = get_personality_summary($profile->personality_data);
    echo '<div class="personality-summary">' . $personality_details . '</div>';
    
    echo '</div>';
} else {
    echo '<div class="no-data">';
    echo '<p class="text-muted">' . get_string('personality_test_not_completed', 'block_student_path') . '</p>';
    echo '</div>';
}

echo '</div>';
echo '</div>';

// Cuarta columna: Inteligencia Emocional (TMMS-24)
echo '<div class="col-lg-3">';
echo '<div class="profile-section">';
echo '<h4 class="section-title">';
echo '<i class="icon fa fa-heart"></i>';
echo get_string('emotional_intelligence', 'block_student_path');
echo '</h4>';

if ($profile->emotional_intelligence) {
    echo '<div class="tmms24-result">';
    $tmms24_details = get_tmms24_summary($profile->tmms_24_data);
    echo '<div class="tmms24-summary">' . $tmms24_details . '</div>';
    
    echo '</div>';
} else {
    echo '<div class="no-data">';
    echo '<p class="text-muted">' . get_string('tmms_24_test_not_completed', 'block_student_path') . '</p>';
    echo '</div>';
}

echo '</div>';
echo '</div>';

echo '</div>';

// Sección de objetivos y plan de acción (si hay datos de student_path)
if (!empty($profile->goals_short) || !empty($profile->goals_medium) || !empty($profile->goals_long) || 
    !empty($profile->actions_short) || !empty($profile->actions_medium) || !empty($profile->actions_long)) {
    
    echo '<div class="goals-action-plan mt-4">';
    echo '<h4>Objetivos y Plan de Acción</h4>';
    echo '<div class="row">';
    
    // Columna de objetivos
    if (!empty($profile->goals_short) || !empty($profile->goals_medium) || !empty($profile->goals_long)) {
        echo '<div class="col-md-6">';
        echo '<div class="goals-section">';
        echo '<h6><i class="fa fa-bullseye"></i> Objetivos</h6>';
        
        if (!empty($profile->goals_short)) {
            echo '<div class="goal-item">';
            echo '<strong>Corto Plazo:</strong>';
            echo '<p class="text-muted small">' . htmlspecialchars($profile->goals_short) . '</p>';
            echo '</div>';
        }
        
        if (!empty($profile->goals_medium)) {
            echo '<div class="goal-item">';
            echo '<strong>Mediano Plazo:</strong>';
            echo '<p class="text-muted small">' . htmlspecialchars($profile->goals_medium) . '</p>';
            echo '</div>';
        }
        
        if (!empty($profile->goals_long)) {
            echo '<div class="goal-item">';
            echo '<strong>Largo Plazo:</strong>';
            echo '<p class="text-muted small">' . htmlspecialchars($profile->goals_long) . '</p>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    // Columna de acciones
    if (!empty($profile->actions_short) || !empty($profile->actions_medium) || !empty($profile->actions_long)) {
        echo '<div class="col-md-6">';
        echo '<div class="actions-section">';
        echo '<h6><i class="fa fa-tasks"></i> Plan de Acción</h6>';
        
        if (!empty($profile->actions_short)) {
            echo '<div class="action-item">';
            echo '<strong>Corto Plazo:</strong>';
            echo '<p class="text-muted small">' . htmlspecialchars($profile->actions_short) . '</p>';
            echo '</div>';
        }
        
        if (!empty($profile->actions_medium)) {
            echo '<div class="action-item">';
            echo '<strong>Mediano Plazo:</strong>';
            echo '<p class="text-muted small">' . htmlspecialchars($profile->actions_medium) . '</p>';
            echo '</div>';
        }
        
        if (!empty($profile->actions_long)) {
            echo '<div class="action-item">';
            echo '<strong>Largo Plazo:</strong>';
            echo '<p class="text-muted small">' . htmlspecialchars($profile->actions_long) . '</p>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
    }
    
    echo '</div>';
    echo '</div>';
}

// Botones de acción
echo '<div class="action-buttons mt-4">';
echo '<a href="' . new moodle_url('/blocks/student_path/teacher_view.php', array('cid' => $courseid)) . '" class="btn btn-secondary">';
echo get_string('back_to_list', 'block_student_path');
echo '</a>';

if ($profile->completion_percentage > 0) {
    echo '<a href="#" class="btn btn-primary ms-2" onclick="window.print()">';
    echo get_string('print_profile', 'block_student_path');
    echo '</a>';
}

echo '</div>';

echo '</div>';

// CSS personalizado para el perfil integrado
echo '<style>
.integrated-profile-container {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 20px 0;
}

.student-header {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.completion-overview {
    text-align: center;
}

.completion-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: conic-gradient(#007bff 0deg, #007bff calc(var(--percentage) * 3.6deg), #e9ecef calc(var(--percentage) * 3.6deg), #e9ecef 360deg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
}

.completion-text {
    background: white;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #007bff;
}

.evaluation-status-bar {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.status-indicators {
    display: flex;
    justify-content: space-around;
    gap: 20px;
}

.status-item {
    text-align: center;
    flex: 1;
}

.status-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-weight: bold;
    color: white;
    font-size: 0.9rem;
}

.status-item.completed .status-icon {
    background-color: #28a745;
}

.status-item.pending .status-icon {
    background-color: #6c757d;
}

.status-label {
    font-size: 0.9rem;
    font-weight: 500;
}

.profile-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    height: 100%;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 20px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 10px;
}

.section-title .icon {
    color: #007bff;
}

.holland-result {
    text-align: center;
}

.holland-main-type {
    margin-bottom: 15px;
}

.holland-badge {
    display: inline-block;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 1.2rem;
    color: white;
    margin-bottom: 8px;
}

.holland-badge-inline {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: bold;
    font-size: 0.9rem;
    color: white;
    margin-right: 5px;
}

.holland-realistic { background-color: #28a745; }
.holland-investigative { background-color: #007bff; }
.holland-artistic { background-color: #e83e8c; }
.holland-social { background-color: #fd7e14; }
.holland-enterprising { background-color: #dc3545; }
.holland-conventional { background-color: #6f42c1; }

.holland-type-name {
    font-weight: 600;
    color: #333;
    margin-top: 5px;
}

.dimension-list {
    list-style: none;
    padding: 0;
}

.dimension-list li {
    padding: 5px 0;
    border-bottom: 1px solid #eee;
}

.traits-list {
    margin-top: 15px;
}

.trait-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.trait-name {
    flex: 0 0 120px;
    font-size: 0.9rem;
    font-weight: 500;
}

.trait-bar {
    flex: 1;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.trait-fill {
    height: 100%;
    background: #007bff;
    transition: width 0.3s ease;
}

.trait-value {
    flex: 0 0 40px;
    text-align: right;
    font-size: 0.9rem;
    font-weight: 500;
    color: #007bff;
}

.integrated-analysis {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.analysis-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 6px;
    height: 100%;
}

.no-data {
    text-align: center;
    padding: 40px 20px;
}

.action-buttons {
    text-align: center;
}

@media (max-width: 768px) {
    .status-indicators {
        flex-direction: column;
        gap: 15px;
    }
    
    .trait-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .trait-name {
        flex: none;
    }
    
    .trait-bar {
        width: 100%;
    }
}

@media print {
    .action-buttons,
    .breadcrumb {
        display: none !important;
    }
    
    .integrated-profile-container {
        background: white !important;
        box-shadow: none !important;
    }
}
</style>';

// JavaScript para el círculo de progreso
echo '<script>
document.addEventListener("DOMContentLoaded", function() {
    const circles = document.querySelectorAll(".completion-circle");
    circles.forEach(circle => {
        const percentage = circle.dataset.percentage;
        circle.style.setProperty("--percentage", percentage);
    });
});
</script>';

echo $OUTPUT->footer();
?>