<?php

/**
 * Guarda o actualiza la información del student_path
 */
function save_student_path($course, $name, $program, $admission_year, $email, $code, 
                          $personality_aspects, $professional_interests, $emotional_skills, 
                          $goals_aspirations, $action_plan, $edit = 0) {
    global $DB, $USER, $CFG;
    
    // Debug: Log de entrada
    error_log("save_student_path called with: user={$USER->id}, course={$course}, program={$program}");
    
    try {
        // Buscar si ya existe un registro
        $existing_entry = $DB->get_record('student_path', array('user' => $USER->id, 'course' => $course));
        
        if ($existing_entry) {
            // Si existe, actualizar
            error_log("Updating existing record ID: " . $existing_entry->id);
            $existing_entry->name = $name;
            $existing_entry->program = $program;
            $existing_entry->admission_year = $admission_year;
            $existing_entry->email = $email;
            $existing_entry->code = $code;
            $existing_entry->personality_aspects = $personality_aspects;
            $existing_entry->professional_interests = $professional_interests;
            $existing_entry->emotional_skills = $emotional_skills;
            $existing_entry->goals_aspirations = $goals_aspirations;
            $existing_entry->action_plan = $action_plan;
            $existing_entry->updated_at = time();
            
            $result = $DB->update_record('student_path', $existing_entry);
            error_log('Student Path Update: ' . ($result ? 'SUCCESS' : 'FAILED'));
            return $result;
        } else {
            // Crear nuevo registro
            error_log("Creating new record for user {$USER->id} in course {$course}");
            $entry = new stdClass();
            $entry->user = $USER->id;
            $entry->course = $course;
            $entry->name = $name;
            $entry->program = $program;
            $entry->admission_year = $admission_year;
            $entry->email = $email;
            $entry->code = $code;
            $entry->personality_aspects = $personality_aspects;
            $entry->professional_interests = $professional_interests;
            $entry->emotional_skills = $emotional_skills;
            $entry->goals_aspirations = $goals_aspirations;
            $entry->action_plan = $action_plan;
            $entry->created_at = time();
            $entry->updated_at = time();
            
            $entry_id = $DB->insert_record('student_path', $entry);
            error_log('Student Path Insert: ' . ($entry_id ? 'SUCCESS (ID: ' . $entry_id . ')' : 'FAILED'));
            return $entry_id ? true : false;
        }
    } catch (Exception $e) {
        // Log del error para debug
        error_log('Error saving student_path: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        return false;
    }
}

/**
 * Obtiene la información del student_path de un usuario
 */
function get_student_path($user_id, $course_id) {
    global $DB;
    
    return $DB->get_record('student_path', array('user' => $user_id, 'course' => $course_id));
}

/**
 * Obtiene estadísticas del student_path para un curso
 */
function get_student_path_stats($course_id) {
    global $DB;
    
    $response = [
        "total_students" => 0,
        "completed_profiles" => 0,
        "completion_rate" => 0,
        "course" => $course_id
    ];
    
    // Obtener total de estudiantes en el curso
    $total_students = $DB->get_record_sql(
        "SELECT count(m.id) as cantidad
        FROM {user} m
        LEFT JOIN {role_assignments} m2 ON m.id = m2.userid
        LEFT JOIN {context} m3 ON m2.contextid = m3.id
        LEFT JOIN {course} m4 ON m3.instanceid = m4.id
        WHERE m3.contextlevel = 50 
        AND m2.roleid IN (5) 
        AND m4.id = :courseid",
        ['courseid' => $course_id]
    );
    
    // Obtener estudiantes que completaron el perfil
    $completed_profiles = $DB->get_record_sql(
        "SELECT count(id) as total
        FROM {student_path} 
        WHERE course = :courseid",
        ['courseid' => $course_id]
    );
    
    $response["total_students"] = intval($total_students->cantidad);
    $response["completed_profiles"] = intval($completed_profiles->total);
    
    if ($response["total_students"] > 0) {
        $response["completion_rate"] = round(($response["completed_profiles"] / $response["total_students"]) * 100, 2);
    }
    
    return $response;
}

/**
 * Obtiene la lista de estudiantes del curso con sus perfiles (si los tienen)
 */
function get_students_with_profiles($course_id) {
    global $DB;
    
    $sql = "SELECT u.id, u.firstname, u.lastname, u.email,
                   sp.program, sp.admission_year, sp.code, sp.updated_at,
                   CASE WHEN sp.id IS NOT NULL THEN 1 ELSE 0 END as has_profile
            FROM {user} u
            INNER JOIN {role_assignments} ra ON u.id = ra.userid
            INNER JOIN {context} ctx ON ra.contextid = ctx.id
            INNER JOIN {course} c ON ctx.instanceid = c.id
            LEFT JOIN {student_path} sp ON u.id = sp.user AND c.id = sp.course
            WHERE ctx.contextlevel = 50 
              AND ra.roleid IN (5)
              AND c.id = :courseid
            ORDER BY u.lastname, u.firstname";
    
    return $DB->get_records_sql($sql, ['courseid' => $course_id]);
}

/**
 * Obtiene el perfil completo de un estudiante específico
 */
function get_student_complete_profile($user_id, $course_id) {
    global $DB;
    
    $sql = "SELECT u.firstname, u.lastname, u.email,
                   sp.program, sp.admission_year, sp.code,
                   sp.personality_aspects, sp.professional_interests, sp.emotional_skills,
                   sp.goals_aspirations, sp.action_plan,
                   sp.created_at, sp.updated_at
            FROM {user} u
            INNER JOIN {student_path} sp ON u.id = sp.user
            WHERE u.id = :userid AND sp.course = :courseid";
    
    return $DB->get_record_sql($sql, ['userid' => $user_id, 'courseid' => $course_id]);
}

/**
 * Obtiene todos los datos de estudiantes para exportación
 */
function get_students_path_data($course_id) {
    global $DB;
    
    // Obtener todos los estudiantes del curso
    $context = context_course::instance($course_id);
    $students = get_enrolled_users($context, 'mod/assign:submit');
    
    $students_data = array();
    
    foreach ($students as $student) {
        // Obtener datos del perfil si existe
        $profile = $DB->get_record('student_path', 
            array('user' => $student->id, 'course' => $course_id)
        );
        
        $student_data = new stdClass();
        $student_data->userid = $student->id;
        $student_data->firstname = $student->firstname;
        $student_data->lastname = $student->lastname;
        $student_data->email = $student->email;
        $student_data->has_profile = !empty($profile);
        
        if ($profile) {
            $student_data->program = $profile->program;
            $student_data->admission_year = $profile->admission_year;
            $student_data->code = $profile->code;
            $student_data->personality_aspects = $profile->personality_aspects;
            $student_data->professional_interests = $profile->professional_interests;
            $student_data->emotional_skills = $profile->emotional_skills;
            $student_data->goals_aspirations = $profile->goals_aspirations;
            $student_data->action_plan = $profile->action_plan;
            $student_data->timecreated = $profile->created_at;
            $student_data->timemodified = $profile->updated_at;
        } else {
            $student_data->program = '';
            $student_data->admission_year = '';
            $student_data->code = '';
            $student_data->personality_aspects = '';
            $student_data->professional_interests = '';
            $student_data->emotional_skills = '';
            $student_data->goals_aspirations = '';
            $student_data->action_plan = '';
            $student_data->timecreated = null;
            $student_data->timemodified = null;
        }
        
        $students_data[] = $student_data;
    }
    
    return $students_data;
}
