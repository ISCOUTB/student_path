<?php

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once(dirname(__FILE__) . '/lib.php');

// Verificar permisos de administrador
admin_externalpage_setup('manageblocks');
require_capability('moodle/site:config', context_system::instance());

$userid = required_param('user_id', PARAM_INT);

$PAGE->set_url('/blocks/student_path/admin_view_user.php', array('user_id' => $userid));
$PAGE->set_title(get_string('admin_manage_title', 'block_student_path'));
$PAGE->set_heading(get_string('admin_manage_heading', 'block_student_path'));

// Obtener información del usuario
$user = $DB->get_record('user', array('id' => $userid), '*', MUST_EXIST);

// Obtener todas las participaciones del usuario
$sql = "SELECT sp.*, c.fullname as coursename, c.shortname as courseshortname
        FROM {student_path} sp
        LEFT JOIN {course} c ON c.id = sp.course
        WHERE sp.user = ?
        ORDER BY sp.updated_at DESC";

$participations = $DB->get_records_sql($sql, array($userid));

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('student_profile', 'block_student_path') . ': ' . fullname($user));

// Navegación
echo '<div style="margin-bottom: 20px;">';
echo '<a href="admin_manage.php" class="btn btn-secondary">← ' . get_string('admin_manage_title', 'block_student_path') . '</a>';
echo '</div>';

if (empty($participations)) {
    echo $OUTPUT->notification(get_string('no_profile', 'block_student_path'), 'info');
} else {
    foreach ($participations as $participation) {
        echo '<div class="card" style="margin-bottom: 20px;">';
        echo '<div class="card-header">';
        echo '<h4>📚 ' . ($participation->coursename ? $participation->coursename : 'Curso desconocido') . '</h4>';
        echo '<small class="text-muted">Creado: ' . userdate($participation->created_at) . ' | Modificado: ' . userdate($participation->updated_at) . '</small>';
        echo '</div>';
        echo '<div class="card-body">';
        
        // Información personal
        echo '<div class="row mb-3">';
        echo '<div class="col-md-12">';
        echo '<h5 class="text-primary">' . get_string('personal_info', 'block_student_path') . '</h5>';
        echo '<div class="row">';
        echo '<div class="col-md-4"><strong>' . get_string('name', 'block_student_path') . ':</strong> ' . fullname($user) . '</div>';
        echo '<div class="col-md-4"><strong>' . get_string('program', 'block_student_path') . ':</strong> ' . ($participation->program ?? 'No especificado') . '</div>';
        echo '<div class="col-md-4"><strong>' . get_string('code', 'block_student_path') . ':</strong> ' . ($participation->code ?? 'No especificado') . '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        // Autodescubrimiento
        echo '<div class="row mb-3">';
        echo '<div class="col-md-12">';
        echo '<h5 class="text-primary">' . get_string('self_discovery', 'block_student_path') . '</h5>';
        
        // Fortalezas
        if (!empty($participation->personality_strengths)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('personality_strengths', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . nl2br(htmlspecialchars($participation->personality_strengths)) . '</div>';
            echo '</div>';
        }
        
        // Debilidades
        if (!empty($participation->personality_weaknesses)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('personality_weaknesses', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . nl2br(htmlspecialchars($participation->personality_weaknesses)) . '</div>';
            echo '</div>';
        }
        
        // Áreas vocacionales
        if (!empty($participation->vocational_areas)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('vocational_areas', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . get_string('vocational_area_' . strtolower($participation->vocational_areas), 'block_student_path') . '</div>';
            echo '</div>';
        }
        
        // Descripción vocacional
        if (!empty($participation->vocational_description)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('vocational_description', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . nl2br(htmlspecialchars($participation->vocational_description)) . '</div>';
            echo '</div>';
        }
        
        // Habilidades emocionales
        if (!empty($participation->emotional_skills_level)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('emotional_skills_level', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . nl2br(htmlspecialchars($participation->emotional_skills_level)) . '</div>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
        
        // Metas
        echo '<div class="row mb-3">';
        echo '<div class="col-md-12">';
        echo '<h5 class="text-primary">' . get_string('goals_aspirations', 'block_student_path') . '</h5>';
        
        if (!empty($participation->goal_short_term)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('goal_short_term', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . nl2br(htmlspecialchars($participation->goal_short_term)) . '</div>';
            echo '</div>';
        }
        
        if (!empty($participation->goal_medium_term)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('goal_medium_term', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . nl2br(htmlspecialchars($participation->goal_medium_term)) . '</div>';
            echo '</div>';
        }
        
        if (!empty($participation->goal_long_term)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('goal_long_term', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . nl2br(htmlspecialchars($participation->goal_long_term)) . '</div>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
        
        // Plan de acción
        echo '<div class="row mb-3">';
        echo '<div class="col-md-12">';
        echo '<h5 class="text-primary">' . get_string('action_plan', 'block_student_path') . '</h5>';
        
        if (!empty($participation->action_short_term)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('action_short_term', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . nl2br(htmlspecialchars($participation->action_short_term)) . '</div>';
            echo '</div>';
        }
        
        if (!empty($participation->action_medium_term)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('action_medium_term', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . nl2br(htmlspecialchars($participation->action_medium_term)) . '</div>';
            echo '</div>';
        }
        
        if (!empty($participation->action_long_term)) {
            echo '<div class="mb-2">';
            echo '<strong>' . get_string('action_long_term', 'block_student_path') . ':</strong>';
            echo '<div class="text-muted">' . nl2br(htmlspecialchars($participation->action_long_term)) . '</div>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
        
        // Acciones
        echo '<div class="card-footer">';
        echo '<a href="admin_manage.php?action=delete&userid=' . $userid . '" class="btn btn-danger btn-sm">';
        echo get_string('admin_action_delete', 'block_student_path') . '</a>';
        echo '</div>';
        
        echo '</div>';
        echo '</div>';
    }
}

echo $OUTPUT->footer();
?>
