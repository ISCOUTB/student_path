<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

require_login();

$userid = required_param('userid', PARAM_INT);
$courseid = required_param('cid', PARAM_INT);

// Verificar acceso del profesor
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($courseid);
require_capability('moodle/course:manageactivities', $context);

// Obtener datos del estudiante
$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

// Obtener perfil del estudiante
$profile = get_student_complete_profile($userid, $courseid);

// Función para convertir códigos de áreas vocacionales a texto completo
function get_vocational_area_text($code) {
    $areas = array(
        'C' => get_string('vocational_area_c', 'block_student_path'),
        'H' => get_string('vocational_area_h', 'block_student_path'),
        'A' => get_string('vocational_area_a', 'block_student_path'),
        'S' => get_string('vocational_area_s', 'block_student_path'),
        'I' => get_string('vocational_area_i', 'block_student_path'),
        'D' => get_string('vocational_area_d', 'block_student_path'),
        'E' => get_string('vocational_area_e', 'block_student_path'),
    );
    return isset($areas[$code]) ? $areas[$code] : $code;
}

// Configurar página
$PAGE->set_url(new moodle_url('/blocks/student_path/view_profile.php', array('userid' => $userid, 'cid' => $courseid)));
$PAGE->set_context($context);
$PAGE->set_title(get_string('student_profile', 'block_student_path') . ': ' . fullname($user));
$PAGE->set_heading(get_string('student_profile', 'block_student_path') . ': ' . fullname($user));

// Agregar CSS personalizado
$PAGE->requires->css('/blocks/student_path/styles.css');

// Mostrar header
echo $OUTPUT->header();

// Breadcrumb navigation
echo '<nav aria-label="breadcrumb" class="mb-4">';
echo '<ol class="breadcrumb">';
echo '<li class="breadcrumb-item"><a href="' . new moodle_url('/course/view.php', array('id' => $courseid)) . '">' . $course->fullname . '</a></li>';
echo '<li class="breadcrumb-item"><a href="' . new moodle_url('/blocks/student_path/teacher_view.php', array('cid' => $courseid)) . '">' . get_string('students_path_list', 'block_student_path') . '</a></li>';
echo '<li class="breadcrumb-item active" aria-current="page">' . get_string('student_profile', 'block_student_path') . '</li>';
echo '</ol>';
echo '</nav>';

// Contenedor principal
echo '<div class="container-fluid">';

// Header con información del estudiante
echo '<div class="row mb-4">';
echo '<div class="col-12">';
echo '<div class="card">';
echo '<div class="card-header bg-primary text-white">';
echo '<h3 class="card-title mb-0">';
echo '<i class="fa fa-user"></i> ' . get_string('student_profile', 'block_student_path');
echo '</h3>';
echo '</div>';
echo '<div class="card-body">';
echo '<div class="row">';
echo '<div class="col-md-6">';
echo '<h5>' . fullname($user) . '</h5>';
echo '<p class="text-muted mb-1"><strong>' . get_string('email', 'block_student_path') . ':</strong> ' . $user->email . '</p>';
if ($profile) {
    echo '<p class="text-muted mb-1"><strong>' . get_string('program', 'block_student_path') . ':</strong> ' . $profile->program . '</p>';
    echo '<p class="text-muted mb-1"><strong>' . get_string('admission_year', 'block_student_path') . ':</strong> ' . $profile->admission_year . '</p>';
    echo '<p class="text-muted mb-1"><strong>' . get_string('code', 'block_student_path') . ':</strong> ' . $profile->code . '</p>';
}
echo '</div>';
echo '<div class="col-md-6 text-end">';
echo '<a href="' . new moodle_url('/blocks/student_path/teacher_view.php', array('cid' => $courseid)) . '" class="btn btn-secondary">';
echo '<i class="fa fa-arrow-left"></i> ' . get_string('back_to_list', 'block_student_path');
echo '</a>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

if (!$profile) {
    // Mostrar mensaje si no hay perfil
    echo '<div class="row">';
    echo '<div class="col-12">';
    echo '<div class="alert alert-warning">';
    echo '<h5><i class="fa fa-exclamation-triangle"></i> ' . get_string('no_profile_found', 'block_student_path') . '</h5>';
    echo '<p>' . get_string('student_has_not_completed_profile', 'block_student_path') . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
} else {
    // Mostrar perfil completo del estudiante
    echo '<div class="row">';

    // Información básica
    echo '<div class="col-md-6 mb-4">';
    echo '<div class="card h-100">';
    echo '<div class="card-header">';
    echo '<h5 class="card-title mb-0"><i class="fa fa-info-circle"></i> ' . get_string('basic_information', 'block_student_path') . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    echo '<dl class="row">';
    echo '<dt class="col-sm-5">' . get_string('program', 'block_student_path') . ':</dt>';
    echo '<dd class="col-sm-7">' . $profile->program . '</dd>';

    echo '<dt class="col-sm-5">' . get_string('admission_year', 'block_student_path') . ':</dt>';
    echo '<dd class="col-sm-7">' . $profile->admission_year . '</dd>';

    echo '<dt class="col-sm-5">' . get_string('code', 'block_student_path') . ':</dt>';
    echo '<dd class="col-sm-7">' . $profile->code . '</dd>';

    echo '<dt class="col-sm-5">' . get_string('last_update', 'block_student_path') . ':</dt>';
    echo '<dd class="col-sm-7">' . date('d/m/Y H:i', $profile->updated_at) . '</dd>';
    echo '</dl>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Aspectos de personalidad
    echo '<div class="col-md-6 mb-4">';
    echo '<div class="card h-100">';
    echo '<div class="card-header">';
    echo '<h5 class="card-title mb-0"><i class="fa fa-brain"></i> ' . get_string('personality_aspects', 'block_student_path') . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    if (!empty($profile->personality_aspects)) {
        echo '<p>' . nl2br($profile->personality_aspects) . '</p>';
    } else {
        echo '<p class="text-muted">' . get_string('not_specified', 'block_student_path') . '</p>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '</div>';

    echo '<div class="row">';

    // Intereses profesionales
    echo '<div class="col-md-6 mb-4">';
    echo '<div class="card h-100">';
    echo '<div class="card-header">';
    echo '<h5 class="card-title mb-0"><i class="fa fa-briefcase"></i> ' . get_string('professional_interests', 'block_student_path') . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    if (!empty($profile->professional_interests)) {
        echo '<p>' . nl2br($profile->professional_interests) . '</p>';
    } else {
        echo '<p class="text-muted">' . get_string('not_specified', 'block_student_path') . '</p>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Habilidades emocionales
    echo '<div class="col-md-6 mb-4">';
    echo '<div class="card h-100">';
    echo '<div class="card-header">';
    echo '<h5 class="card-title mb-0"><i class="fa fa-heart"></i> ' . get_string('emotional_skills_level', 'block_student_path') . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    if (!empty($profile->emotional_skills_level)) {
        echo '<p>' . nl2br($profile->emotional_skills_level) . '</p>';
    } else {
        echo '<p class="text-muted">' . get_string('not_specified', 'block_student_path') . '</p>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '</div>';

    echo '<div class="row">';

    // Metas y aspiraciones
    echo '<div class="col-md-6 mb-4">';
    echo '<div class="card h-100">';
    echo '<div class="card-header">';
    echo '<h5 class="card-title mb-0"><i class="fa fa-bullseye"></i> ' . get_string('goals_aspirations', 'block_student_path') . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    if (!empty($profile->goals_aspirations)) {
        echo '<p>' . nl2br($profile->goals_aspirations) . '</p>';
    } else {
        echo '<p class="text-muted">' . get_string('not_specified', 'block_student_path') . '</p>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Plan de acción
    echo '<div class="col-md-6 mb-4">';
    echo '<div class="card h-100">';
    echo '<div class="card-header">';
    echo '<h5 class="card-title mb-0"><i class="fa fa-road"></i> ' . get_string('action_plan', 'block_student_path') . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';
    if (!empty($profile->action_plan)) {
        echo '<p>' . nl2br($profile->action_plan) . '</p>';
    } else {
        echo '<p class="text-muted">' . get_string('not_specified', 'block_student_path') . '</p>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo '</div>';

    // Nueva información estructurada (si existe)
    if (!empty($profile->personality_strengths) || !empty($profile->personality_weaknesses) ||
        !empty($profile->vocational_areas) || !empty($profile->goal_short_term)) {

        echo '<div class="row mb-4">';
        echo '<div class="col-12">';
        echo '<h4 class="text-primary"><i class="fa fa-star"></i> ' . get_string('detailed_profile', 'block_student_path') . '</h4>';
        echo '<hr>';
        echo '</div>';
        echo '</div>';

        echo '<div class="row">';

        // Fortalezas de personalidad
        if (!empty($profile->personality_strengths)) {
            echo '<div class="col-md-6 mb-4">';
            echo '<div class="card h-100 border-success">';
            echo '<div class="card-header bg-success text-white">';
            echo '<h6 class="card-title mb-0"><i class="fa fa-plus-circle"></i> ' . get_string('personality_strengths', 'block_student_path') . '</h6>';
            echo '</div>';
            echo '<div class="card-body">';
            echo '<p>' . nl2br($profile->personality_strengths) . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        // Debilidades de personalidad
        if (!empty($profile->personality_weaknesses)) {
            echo '<div class="col-md-6 mb-4">';
            echo '<div class="card h-100 border-warning">';
            echo '<div class="card-header bg-warning text-dark">';
            echo '<h6 class="card-title mb-0"><i class="fa fa-minus-circle"></i> ' . get_string('personality_weaknesses', 'block_student_path') . '</h6>';
            echo '</div>';
            echo '<div class="card-body">';
            echo '<p>' . nl2br($profile->personality_weaknesses) . '</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

        echo '<div class="row">';

        // Áreas vocacionales
        if (!empty($profile->vocational_areas)) {
            echo '<div class="col-md-6 mb-4">';
            echo '<div class="card h-100">';
            echo '<div class="card-header">';
            echo '<h6 class="card-title mb-0"><i class="fa fa-compass"></i> ' . get_string('vocational_areas', 'block_student_path') . '</h6>';
            echo '</div>';
            echo '<div class="card-body">';
            echo '<p><strong>' . get_string('primary_area', 'block_student_path') . ':</strong> ' . get_vocational_area_text($profile->vocational_areas) . '</p>';
            if (!empty($profile->vocational_areas_secondary)) {
                echo '<p><strong>' . get_string('secondary_area', 'block_student_path') . ':</strong> ' . get_vocational_area_text($profile->vocational_areas_secondary) . '</p>';
            }
            if (!empty($profile->vocational_description)) {
                echo '<p><strong>' . get_string('description', 'block_student_path') . ':</strong></p>';
                echo '<p>' . nl2br($profile->vocational_description) . '</p>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        // Metas por tiempo
        if (!empty($profile->goal_short_term) || !empty($profile->goal_medium_term) || !empty($profile->goal_long_term)) {
            echo '<div class="col-md-6 mb-4">';
            echo '<div class="card h-100">';
            echo '<div class="card-header">';
            echo '<h6 class="card-title mb-0"><i class="fa fa-target"></i> ' . get_string('goals_by_time', 'block_student_path') . '</h6>';
            echo '</div>';
            echo '<div class="card-body">';
            if (!empty($profile->goal_short_term)) {
                echo '<p><strong>' . get_string('short_term', 'block_student_path') . ':</strong> ' . nl2br($profile->goal_short_term) . '</p>';
            }
            if (!empty($profile->goal_medium_term)) {
                echo '<p><strong>' . get_string('medium_term', 'block_student_path') . ':</strong> ' . nl2br($profile->goal_medium_term) . '</p>';
            }
            if (!empty($profile->goal_long_term)) {
                echo '<p><strong>' . get_string('long_term', 'block_student_path') . ':</strong> ' . nl2br($profile->goal_long_term) . '</p>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

        // Acciones por tiempo
        if (!empty($profile->action_short_term) || !empty($profile->action_medium_term) || !empty($profile->action_long_term)) {
            echo '<div class="row">';
            echo '<div class="col-12 mb-4">';
            echo '<div class="card">';
            echo '<div class="card-header">';
            echo '<h6 class="card-title mb-0"><i class="fa fa-tasks"></i> ' . get_string('actions_by_time', 'block_student_path') . '</h6>';
            echo '</div>';
            echo '<div class="card-body">';
            echo '<div class="row">';
            if (!empty($profile->action_short_term)) {
                echo '<div class="col-md-4">';
                echo '<h6 class="text-primary">' . get_string('short_term', 'block_student_path') . '</h6>';
                echo '<p>' . nl2br($profile->action_short_term) . '</p>';
                echo '</div>';
            }
            if (!empty($profile->action_medium_term)) {
                echo '<div class="col-md-4">';
                echo '<h6 class="text-primary">' . get_string('medium_term', 'block_student_path') . '</h6>';
                echo '<p>' . nl2br($profile->action_medium_term) . '</p>';
                echo '</div>';
            }
            if (!empty($profile->action_long_term)) {
                echo '<div class="col-md-4">';
                echo '<h6 class="text-primary">' . get_string('long_term', 'block_student_path') . '</h6>';
                echo '<p>' . nl2br($profile->action_long_term) . '</p>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
    }
}

// Botón para regresar al listado
echo '<div class="row mt-4">';
echo '<div class="col-12 text-center">';
echo '<a href="' . new moodle_url('/blocks/student_path/teacher_view.php', array('cid' => $courseid)) . '" class="btn btn-primary btn-lg">';
echo '<i class="fa fa-arrow-left"></i> ' . get_string('back_to_list', 'block_student_path');
echo '</a>';
echo '</div>';
echo '</div>';

echo '</div>';

// Mostrar footer
echo $OUTPUT->footer();
?>