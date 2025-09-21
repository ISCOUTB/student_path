<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

require_login();

$courseid = required_param('cid', PARAM_INT);
$format = optional_param('format', 'csv', PARAM_ALPHA);

// Debug logging
error_log('Export.php called with courseid: ' . $courseid . ', format: ' . $format);

// Verificar acceso del profesor
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$context = context_course::instance($courseid);
require_capability('moodle/course:manageactivities', $context);

// Obtener datos de estudiantes
$students_data = get_students_path_data($courseid);
error_log('Found ' . count($students_data) . ' students for export');

// Configurar headers para descarga
$filename = 'student_paths_course_' . $courseid . '_' . date('Y-m-d');

if ($format === 'csv') {
    $filename .= '.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Crear output buffer
    $output = fopen('php://output', 'w');
    
    // Headers del CSV
    $headers = array(
        get_string('student_name', 'block_student_path'),
        get_string('email', 'block_student_path'),
        get_string('program', 'block_student_path'),
        get_string('code', 'block_student_path'),
        get_string('admission_year', 'block_student_path'),
        get_string('personality_aspects', 'block_student_path'),
        get_string('professional_interests', 'block_student_path'),
        get_string('emotional_skills_level', 'block_student_path'),
        get_string('goals_aspirations', 'block_student_path'),
        get_string('action_plan', 'block_student_path'),
        get_string('last_update', 'block_student_path'),
        get_string('status', 'block_student_path')
    );
    
    fputcsv($output, $headers);
    
    // Datos de estudiantes
    foreach ($students_data as $student) {
        $row = array(
            $student->firstname . ' ' . $student->lastname,
            $student->email,
            $student->program ?? '',
            $student->code ?? '',
            $student->admission_year ?? '',
            $student->personality_aspects ?? '',
            $student->professional_interests ?? '',
            $student->emotional_skills_level ?? '',
            $student->goals_aspirations ?? '',
            $student->action_plan ?? '',
            $student->timemodified ? date('Y-m-d H:i:s', $student->timemodified) : '',
            $student->has_profile ? get_string('completed', 'block_student_path') : get_string('pending', 'block_student_path')
        );
        fputcsv($output, $row);
    }
    
    fclose($output);
    
} elseif ($format === 'excel') {
    // Para Excel podríamos usar una librería como PhpSpreadsheet
    // Por ahora, usaremos CSV con headers de Excel
    $filename .= '.xls';
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    echo "\xEF\xBB\xBF"; // BOM for UTF-8
    
    // Headers
    $headers = array(
        get_string('student_name', 'block_student_path'),
        get_string('email', 'block_student_path'),
        get_string('program', 'block_student_path'),
        get_string('code', 'block_student_path'),
        get_string('admission_year', 'block_student_path'),
        get_string('personality_aspects', 'block_student_path'),
        get_string('professional_interests', 'block_student_path'),
        get_string('emotional_skills_level', 'block_student_path'),
        get_string('goals_aspirations', 'block_student_path'),
        get_string('action_plan', 'block_student_path'),
        get_string('last_update', 'block_student_path'),
        get_string('status', 'block_student_path')
    );
    
    echo implode("\t", $headers) . "\n";
    
    // Datos
    foreach ($students_data as $student) {
        $row = array(
            $student->firstname . ' ' . $student->lastname,
            $student->email,
            $student->program ?? '',
            $student->code ?? '',
            $student->admission_year ?? '',
            str_replace(array("\n", "\r", "\t"), " ", $student->personality_aspects ?? ''),
            str_replace(array("\n", "\r", "\t"), " ", $student->professional_interests ?? ''),
            str_replace(array("\n", "\r", "\t"), " ", $student->emotional_skills_level ?? ''),
            str_replace(array("\n", "\r", "\t"), " ", $student->goals_aspirations ?? ''),
            str_replace(array("\n", "\r", "\t"), " ", $student->action_plan ?? ''),
            $student->timemodified ? date('Y-m-d H:i:s', $student->timemodified) : '',
            $student->has_profile ? get_string('completed', 'block_student_path') : get_string('pending', 'block_student_path')
        );
        echo implode("\t", $row) . "\n";
    }
}

exit;
?>
