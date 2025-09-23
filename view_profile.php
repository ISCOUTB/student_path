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

// CHASIDE (Vocational Test)
echo '<div class="status-item ' . ($profile->chaside_completed ? 'completed' : 'pending') . '">';
echo '<div class="status-icon">CH</div>';
echo '<div class="status-label">' . get_string('chaside_test', 'block_student_path') . '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

// Contenido principal en cuatro columnas
echo '<div class="row mt-4">';

// Primera columna: Identidad Vocacional (Student Path)
echo '<div class="col-xl-3 col-lg-6 col-md-6 mb-4">';
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
echo '<div class="col-xl-3 col-lg-6 col-md-6 mb-4">';
echo '<div class="profile-section">';
echo '<h4 class="section-title">';
echo '<i class="icon fa fa-graduation-cap"></i>';
echo get_string('learning_style', 'block_student_path');
echo '</h4>';

if ($profile->learning_style) {
    echo '<div class="learning-style-result">';
    echo '<div class="alert alert-primary mb-3">';
    echo '<h6 class="alert-heading mb-2"><i class="fa fa-brain"></i> Estilo Identificado</h6>';
    
    if ($profile->learning_style_data) {
        $style_details = get_learning_style_summary($profile->learning_style_data);
        echo '<div class="learning-style-summary">' . $style_details . '</div>';
    } else {
        echo '<span class="badge badge-primary">Test completado</span>';
    }
    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="no-data">';
    echo '<div class="alert alert-warning">';
    echo '<i class="fa fa-exclamation-triangle"></i> ';
    echo get_string('learning_style_test_not_completed', 'block_student_path');
    echo '</div>';
    echo '</div>';
}

echo '</div>';
echo '</div>';

// Tercera columna: Personalidad
echo '<div class="col-xl-3 col-lg-6 col-md-6 mb-4">';
echo '<div class="profile-section">';
echo '<h4 class="section-title">';
echo '<i class="icon fa fa-user"></i>';
echo get_string('personality_profile', 'block_student_path');
echo '</h4>';

if ($profile->personality_traits) {
    echo '<div class="personality-result">';
    echo '<div class="alert alert-purple mb-3" style="background-color: rgba(102, 16, 242, 0.1); border: 1px solid rgba(102, 16, 242, 0.3); color: #4c0c87;">';
    echo '<h6 class="alert-heading mb-2"><i class="fa fa-user-circle"></i> Perfil Identificado</h6>';
    
    if ($profile->personality_data) {
        $personality_details = get_personality_summary($profile->personality_data);
        echo '<div class="personality-summary">' . $personality_details . '</div>';
    } else {
        echo '<span class="badge badge-purple" style="background-color: #6610f2;">Test completado</span>';
    }
    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="no-data">';
    echo '<div class="alert alert-warning">';
    echo '<i class="fa fa-exclamation-triangle"></i> ';
    echo get_string('personality_test_not_completed', 'block_student_path');
    echo '</div>';
    echo '</div>';
}

echo '</div>';
echo '</div>';

// Cuarta columna: Inteligencia Emocional (TMMS-24) y Test Vocacional (CHASIDE)
echo '<div class="col-xl-3 col-lg-6 col-md-6 mb-4">';
echo '<div class="profile-section">';

// Sección de Inteligencia Emocional
echo '<h4 class="section-title">';
echo '<i class="icon fa fa-heart"></i>';
echo get_string('emotional_intelligence', 'block_student_path');
echo '</h4>';

if ($profile->emotional_intelligence && $profile->tmms_24_data) {
    $data = json_decode($profile->tmms_24_data, true);
    $responses = [];
    for ($i = 1; $i <= 24; $i++) {
        $responses[] = $data['item' . $i] ?? 0;
    }
    $scores = calculate_tmms24_scores($responses);
    $gender = $data['gender'] ?? 'F';
    echo '<div class="tmms24-visual-card">';
    $icons = [
        'percepcion' => 'fa-eye',
        'comprension' => 'fa-brain',
        'regulacion' => 'fa-adjust'
    ];
    $labels = [
        'percepcion' => get_string('perception', 'block_student_path'),
        'comprension' => get_string('comprehension', 'block_student_path'),
        'regulacion' => get_string('regulation', 'block_student_path')
    ];
    foreach (['percepcion','comprension','regulacion'] as $dim) {
        echo '<div class="card shadow-sm mb-3">';
        echo '<div class="card-body">';
        echo '<div class="d-flex align-items-center mb-2">';
        echo '<i class="fa ' . $icons[$dim] . ' fa-2x text-primary me-2"></i>';
        echo '<h6 class="mb-0">' . $labels[$dim] . '</h6>';
        echo '</div>';
        echo '<div class="score-value display-5 fw-bold text-primary">' . $scores[$dim] . '</div>';
        echo '<div class="score-interpretation mb-2"><span class="badge badge-info">' . interpret_tmms24_score($dim, $scores[$dim], $gender) . '</span></div>';
        echo '</div>';
        echo '</div>';
    }
    if (isset($data['interpretation']) && !empty($data['interpretation'])) {
        echo '<div class="alert alert-secondary mt-3">';
        echo '<i class="fa fa-info-circle"></i> <strong>' . get_string('interpretation', 'block_student_path') . ':</strong> ';
        echo '<span class="text-muted">' . htmlspecialchars(substr($data['interpretation'], 0, 200)) . '...</span>';
        echo '</div>';
    }
    echo '</div>';
} else if ($profile->emotional_intelligence) {
    echo '<div class="alert alert-success">';
    echo '<i class="fa fa-check-circle"></i> Test TMMS-24 completado';
    echo '</div>';
} else {
    echo '<div class="no-data">';
    echo '<div class="alert alert-warning">';
    echo '<i class="fa fa-exclamation-triangle"></i> ';
    echo get_string('tmms_24_test_not_completed', 'block_student_path');
    echo '</div>';
    echo '</div>';
}

// Separador visual
echo '<hr class="my-4">';

// Sección de CHASIDE en la misma columna
echo '<h4 class="section-title">';
echo '<i class="icon fa fa-briefcase"></i>';
echo get_string('chaside_test', 'block_student_path');
echo '</h4>';

if ($profile->chaside_completed) {
    echo '<div class="chaside-result">';
    
    // Si tenemos datos de CHASIDE, podemos mostrar más información
    if ($profile->chaside_data) {
        $chaside_info = json_decode($profile->chaside_data, true);
        
        echo '<div class="alert alert-success mb-3">';
        echo '<h6 class="alert-heading mb-2"><i class="fa fa-graduation-cap"></i> Test Vocacional Completado</h6>';
        
        // Mostrar fecha de completación
        if (isset($chaside_info['timemodified'])) {
            echo '<div class="completion-date mb-2">';
            echo '<strong>Completado:</strong><br>';
            echo '<small class="text-muted">' . userdate($chaside_info['timemodified'], '%d de %B, %Y') . '</small>';
            echo '</div>';
        }
        
        // Mostrar áreas vocacionales si están disponibles
        $vocational_areas = [];
        $area_labels = [
            'score_c' => 'Ciencias',
            'score_i' => 'Ingeniería',
            'score_a' => 'Artes',
            'score_s' => 'Servicios',
            'score_e' => 'Empresarial',
            'score_o' => 'Oficina'
        ];
        
        foreach ($area_labels as $score_key => $label) {
            if (isset($chaside_info[$score_key]) && $chaside_info[$score_key] > 0) {
                $vocational_areas[$label] = $chaside_info[$score_key];
            }
        }
        
        if (!empty($vocational_areas)) {
            // Ordenar por puntuación descendente
            arsort($vocational_areas);
            
            echo '<div class="vocational-areas mt-2">';
            echo '<small class="text-muted"><strong>Áreas de interés (Top 3):</strong></small><br>';
            echo '<div class="mt-1">';
            $count = 0;
            foreach ($vocational_areas as $area => $score) {
                if ($count >= 3) break;
                $badge_class = $count === 0 ? 'badge-success' : ($count === 1 ? 'badge-info' : 'badge-secondary');
                echo '<span class="badge ' . $badge_class . ' mr-1 mb-1">' . $area . ' (' . $score . ')</span>';
                $count++;
            }
            echo '</div>';
            echo '</div>';
        }
        
        echo '</div>';
    } else {
        echo '<div class="alert alert-success">';
        echo '<i class="fa fa-check-circle"></i> ';
        echo get_string('chaside_completed', 'block_student_path');
        echo '</div>';
    }
    
    echo '</div>';
} else {
    echo '<div class="no-data">';
    echo '<div class="alert alert-warning">';
    echo '<i class="fa fa-exclamation-triangle"></i> ';
    echo get_string('chaside_test_not_completed', 'block_student_path');
    echo '</div>';
    echo '</div>';
}

echo '</div>';
echo '</div>';

echo '</div>'; // Cerrar el row de las 4 columnas principales

// Sección de objetivos y plan de acción - Siempre mostrar con datos o mensaje informativo
echo '<div class="goals-action-plan mt-5">'; // Contenedor de ancho completo
echo '<div class="row">';
echo '<div class="col-12">';
echo '<div class="card border-0 shadow-sm">';
echo '<div class="card-header bg-light">';
echo '<h4 class="mb-0"><i class="fa fa-roadmap text-primary mr-2"></i>Objetivos y Plan de Acción</h4>';
echo '</div>';
echo '<div class="card-body p-4">';

// Verificar si hay datos de objetivos y acciones
$has_goals = !empty($profile->goals_short) || !empty($profile->goals_medium) || !empty($profile->goals_long);
$has_actions = !empty($profile->actions_short) || !empty($profile->actions_medium) || !empty($profile->actions_long);

// Agregar debug temporal (remover después)
echo '<!-- DEBUG: has_goals=' . ($has_goals ? 'true' : 'false') . ', has_actions=' . ($has_actions ? 'true' : 'false') . ' -->';

if ($has_goals || $has_actions) {
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
    
    echo '</div>'; // Cerrar row interno
} else {
    // Mostrar mensaje cuando no hay datos
    echo '<div class="alert alert-info text-center">';
    echo '<i class="fa fa-info-circle fa-2x mb-3 text-muted"></i>';
    echo '<h6>No hay objetivos y plan de acción registrados</h6>';
    echo '<p class="text-muted mb-0">El estudiante aún no ha completado esta sección de su mapa de identidad.</p>';
    echo '</div>';
}

echo '</div>'; // Cerrar card-body
echo '</div>'; // Cerrar card
echo '</div>'; // Cerrar col-12
echo '</div>'; // Cerrar row externo
echo '</div>'; // Cerrar goals-action-plan

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