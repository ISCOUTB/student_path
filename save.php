<?php

require_once('../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

// Debug básico
error_log('=== SAVE.PHP START ===');
error_log('POST data: ' . json_encode($_POST));

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

// Nuevos campos según las especificaciones actualizadas
$personality_strengths = optional_param('personality_strengths', '', PARAM_TEXT);
$personality_weaknesses = optional_param('personality_weaknesses', '', PARAM_TEXT);
$vocational_areas = optional_param('vocational_areas', '', PARAM_TEXT);
$vocational_areas_secondary = optional_param('vocational_areas_secondary', '', PARAM_TEXT);
$vocational_description = optional_param('vocational_description', '', PARAM_TEXT);
$emotional_skills_level = optional_param('emotional_skills_level', '', PARAM_TEXT);

$goal_short_term = optional_param('goal_short_term', '', PARAM_TEXT);
$goal_medium_term = optional_param('goal_medium_term', '', PARAM_TEXT);
$goal_long_term = optional_param('goal_long_term', '', PARAM_TEXT);

$action_short_term = optional_param('action_short_term', '', PARAM_TEXT);
$action_medium_term = optional_param('action_medium_term', '', PARAM_TEXT);
$action_long_term = optional_param('action_long_term', '', PARAM_TEXT);

$edit = optional_param('edit', 0, PARAM_INT);

error_log('Data received - Program: ' . $program . ', Year: ' . $admission_year . ', Code: ' . $code);
error_log('Vocational areas: ' . $vocational_areas . ', Secondary: ' . $vocational_areas_secondary);
error_log('Goals - Short: ' . substr($goal_short_term, 0, 50) . '...');

// Los datos de nombre y email se toman automáticamente de Moodle
$name = $USER->firstname . ' ' . $USER->lastname;
$email = $USER->email;

error_log('User data - Name: ' . $name . ', Email: ' . $email . ', User ID: ' . $USER->id);

// Verificar que el curso existe
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
error_log('Course found: ' . $course->fullname);

// Verificar que la tabla student_path existe
if ($DB->get_manager()->table_exists('student_path')) {
    error_log('Table student_path exists');
} else {
    error_log('ERROR: Table student_path does NOT exist');
}

// Llamar a la función de guardado con los nuevos campos
$result = save_student_path_updated($courseid, $name, $program, $admission_year, $email, $code, 
                           $personality_strengths, $personality_weaknesses, $vocational_areas, 
                           $vocational_areas_secondary, $vocational_description, $emotional_skills_level,
                           $goal_short_term, $goal_medium_term, $goal_long_term,
                           $action_short_term, $action_medium_term, $action_long_term, $edit);

error_log('Save result: ' . ($result ? 'SUCCESS' : 'FAILED'));

// Redireccionar
$redirect_url = new moodle_url('/course/view.php', array('id' => $courseid));

if ($result) {
    redirect($redirect_url, 'Perfil guardado exitosamente');
} else {
    redirect($redirect_url, 'Error al guardar el perfil');
}

?>
