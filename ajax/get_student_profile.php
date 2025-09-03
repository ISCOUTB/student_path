<?php
require_once(dirname(__FILE__) . '/../../../config.php');
require_once(dirname(__FILE__) . '/../lib.php');

require_login();

$userid = required_param('userid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

// Verificar que el usuario actual sea profesor en el curso
$context = context_course::instance($courseid);
require_capability('moodle/course:manageactivities', $context);

// Obtener el perfil completo del estudiante
$profile = get_student_complete_profile($userid, $courseid);

if (!$profile) {
    echo '<div class="alert alert-warning">' . get_string('no_profile', 'block_student_path') . '</div>';
    exit;
}

// Mostrar el perfil del estudiante
echo '<div class="student-profile-content">';

// Información personal
echo '<div class="section-card mb-3">';
echo '<h5>' . get_string('personal_info', 'block_student_path') . '</h5>';
echo '<div class="row">';
echo '<div class="col-md-6"><strong>' . get_string('name', 'block_student_path') . ':</strong> ' . $profile->firstname . ' ' . $profile->lastname . '</div>';
echo '<div class="col-md-6"><strong>' . get_string('email', 'block_student_path') . ':</strong> ' . $profile->email . '</div>';
echo '<div class="col-md-6"><strong>' . get_string('program', 'block_student_path') . ':</strong> ' . $profile->program . '</div>';
echo '<div class="col-md-6"><strong>' . get_string('admission_year', 'block_student_path') . ':</strong> ' . $profile->admission_year . '</div>';
echo '<div class="col-md-6"><strong>' . get_string('code', 'block_student_path') . ':</strong> ' . $profile->code . '</div>';
echo '</div>';
echo '</div>';

// Autodescubrimiento
if (!empty($profile->personality_aspects) || !empty($profile->professional_interests) || !empty($profile->emotional_skills)) {
    echo '<div class="section-card mb-3">';
    echo '<h5>' . get_string('self_discovery', 'block_student_path') . '</h5>';
    
    if (!empty($profile->personality_aspects)) {
        echo '<div class="subsection">';
        echo '<h6>' . get_string('personality_aspects', 'block_student_path') . '</h6>';
        echo '<p>' . nl2br(htmlspecialchars($profile->personality_aspects)) . '</p>';
        echo '</div>';
    }
    
    if (!empty($profile->professional_interests)) {
        echo '<div class="subsection">';
        echo '<h6>' . get_string('professional_interests', 'block_student_path') . '</h6>';
        echo '<p>' . nl2br(htmlspecialchars($profile->professional_interests)) . '</p>';
        echo '</div>';
    }
    
    if (!empty($profile->emotional_skills)) {
        echo '<div class="subsection">';
        echo '<h6>' . get_string('emotional_skills', 'block_student_path') . '</h6>';
        echo '<p>' . nl2br(htmlspecialchars($profile->emotional_skills)) . '</p>';
        echo '</div>';
    }
    
    echo '</div>';
}

// Metas y aspiraciones
if (!empty($profile->goals_aspirations)) {
    echo '<div class="section-card mb-3">';
    echo '<h5>' . get_string('goals_aspirations', 'block_student_path') . '</h5>';
    echo '<p>' . nl2br(htmlspecialchars($profile->goals_aspirations)) . '</p>';
    echo '</div>';
}

// Plan de acción
if (!empty($profile->action_plan)) {
    echo '<div class="section-card mb-3">';
    echo '<h5>' . get_string('action_plan', 'block_student_path') . '</h5>';
    echo '<p>' . nl2br(htmlspecialchars($profile->action_plan)) . '</p>';
    echo '</div>';
}

// Información de fechas
echo '<div class="section-card">';
echo '<h6>Información del perfil</h6>';
echo '<small class="text-muted">';
echo '<strong>Creado:</strong> ' . date('d/m/Y H:i', $profile->created_at) . '<br>';
echo '<strong>Última actualización:</strong> ' . date('d/m/Y H:i', $profile->updated_at);
echo '</small>';
echo '</div>';

echo '</div>';
?>
