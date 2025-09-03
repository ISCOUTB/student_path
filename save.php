<?php

require_once('../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

// Debug básico
error_log('=== SAVE.PHP START ===');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log('Not POST request, redirecting');
    redirect($CFG->wwwroot);
    exit;
}

require_login();

$courseid = required_param('cid', PARAM_INT);
error_log('Course ID: ' . $courseid);

// Obtener datos del formulario
$program = required_param('program', PARAM_TEXT);
$admission_year = required_param('admission_year', PARAM_INT);
$code = required_param('code', PARAM_TEXT);
$personality_aspects = optional_param('personality_aspects', '', PARAM_TEXT);
$professional_interests = optional_param('professional_interests', '', PARAM_TEXT);
$emotional_skills = optional_param('emotional_skills', '', PARAM_TEXT);
$goals_aspirations = optional_param('goals_aspirations', '', PARAM_TEXT);
$action_plan = optional_param('action_plan', '', PARAM_TEXT);
$edit = optional_param('edit', 0, PARAM_INT);

error_log('Data received - Program: ' . $program . ', Year: ' . $admission_year . ', Code: ' . $code);

// Los datos de nombre y email se toman automáticamente de Moodle
$name = $USER->firstname . ' ' . $USER->lastname;
$email = $USER->email;

// Verificar que el curso existe
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

// Llamar a la función de guardado
$result = save_student_path($courseid, $name, $program, $admission_year, $email, $code, 
                           $personality_aspects, $professional_interests, $emotional_skills, 
                           $goals_aspirations, $action_plan, $edit);

error_log('Save result: ' . ($result ? 'SUCCESS' : 'FAILED'));

// Redireccionar
$redirect_url = new moodle_url('/course/view.php', array('id' => $courseid));

if ($result) {
    redirect($redirect_url, 'Perfil guardado exitosamente');
} else {
    redirect($redirect_url, 'Error al guardar el perfil');
}

?>
