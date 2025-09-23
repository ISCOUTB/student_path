<?php

/**
 * Guarda o actualiza la información del student_path
 */
function save_student_path($course, $name, $program, $admission_year, $email, $code, 
                          $personality_aspects, $professional_interests, $emotional_skills_level, 
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
            $existing_entry->emotional_skills_level = $emotional_skills_level;
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
            $entry->emotional_skills_level = $emotional_skills_level;
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
                   sp.personality_aspects, sp.professional_interests,
                   sp.goals_aspirations, sp.action_plan,
                   sp.personality_strengths, sp.personality_weaknesses, 
                   sp.vocational_areas, sp.vocational_areas_secondary, sp.vocational_description,
                   sp.emotional_skills_level, sp.goal_short_term, sp.goal_medium_term, sp.goal_long_term,
                   sp.action_short_term, sp.action_medium_term, sp.action_long_term,
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
            $student_data->emotional_skills_level = $profile->emotional_skills_level;
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
            $student_data->emotional_skills_level = '';
            $student_data->goals_aspirations = '';
            $student_data->action_plan = '';
            $student_data->timecreated = null;
            $student_data->timemodified = null;
        }
        
        $students_data[] = $student_data;
    }
    
    return $students_data;
}

/**
 * Guarda o actualiza la información del student_path con la nueva estructura
 */
function save_student_path_updated($course, $name, $program, $admission_year, $email, $code, 
                          $personality_strengths, $personality_weaknesses, $vocational_areas, 
                          $vocational_areas_secondary, $vocational_description, $emotional_skills_level,
                          $goal_short_term, $goal_medium_term, $goal_long_term,
                          $action_short_term, $action_medium_term, $action_long_term, $edit = 0) {
    global $DB, $USER, $CFG;
    
    // Debug: Log de entrada
    error_log("save_student_path_updated called with: user={$USER->id}, course={$course}, program={$program}");
    
    try {
        // Verificar que la tabla existe antes de hacer cualquier operación
        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('student_path')) {
            error_log("ERROR: Table student_path does not exist!");
            return false;
        }
        
        // Buscar si ya existe un registro
        error_log("Searching for existing record: user={$USER->id}, course={$course}");
        $existing_entry = $DB->get_record('student_path', array('user' => $USER->id, 'course' => $course));
        error_log("Existing entry found: " . ($existing_entry ? 'YES (ID: ' . $existing_entry->id . ')' : 'NO'));
        
        if ($existing_entry) {
            // Si existe, actualizar
            error_log("Updating existing record ID: " . $existing_entry->id);
            $existing_entry->name = $name;
            $existing_entry->program = $program;
            $existing_entry->admission_year = $admission_year;
            $existing_entry->email = $email;
            $existing_entry->code = $code;
            
            // Nuevos campos estructurados
            $existing_entry->personality_strengths = $personality_strengths;
            $existing_entry->personality_weaknesses = $personality_weaknesses;
            $existing_entry->vocational_areas = $vocational_areas;
            $existing_entry->vocational_areas_secondary = $vocational_areas_secondary;
            $existing_entry->vocational_description = $vocational_description;
            $existing_entry->emotional_skills_level = $emotional_skills_level;
            
            $existing_entry->goal_short_term = $goal_short_term;
            $existing_entry->goal_medium_term = $goal_medium_term;
            $existing_entry->goal_long_term = $goal_long_term;
            
            $existing_entry->action_short_term = $action_short_term;
            $existing_entry->action_medium_term = $action_medium_term;
            $existing_entry->action_long_term = $action_long_term;
            
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
            
            // Log de los datos principales
            error_log("Main data - Name: {$name}, Program: {$program}, Year: {$admission_year}, Code: {$code}");
            
            // Nuevos campos estructurados
            $entry->personality_strengths = $personality_strengths;
            $entry->personality_weaknesses = $personality_weaknesses;
            $entry->vocational_areas = $vocational_areas;
            $entry->vocational_areas_secondary = $vocational_areas_secondary;
            $entry->vocational_description = $vocational_description;
            $entry->emotional_skills_level = $emotional_skills_level;
            
            error_log("Vocational data - Areas: {$vocational_areas}, Secondary: {$vocational_areas_secondary}");
            
            $entry->goal_short_term = $goal_short_term;
            $entry->goal_medium_term = $goal_medium_term;
            $entry->goal_long_term = $goal_long_term;
            
            $entry->action_short_term = $action_short_term;
            $entry->action_medium_term = $action_medium_term;
            $entry->action_long_term = $action_long_term;
            
            $entry->created_at = time();
            $entry->updated_at = time();
            
            error_log("Attempting to insert record...");
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
 * Obtiene datos integrados de student_path, learning_style, personality_test y tmms_24 para un estudiante
 */
function get_integrated_student_profile($user_id, $course_id) {
    global $DB;
    
    $profile = new stdClass();
    
    // Datos básicos de student_path
    $student_path = $DB->get_record("student_path", array("user" => $user_id, "course" => $course_id));
    $profile->student_path_data = $student_path ? json_encode($student_path) : null;
    
    // Extraer información específica de student_path para mejor presentación
    if ($student_path) {
        $profile->program = $student_path->program ?? '';
        $profile->admission_year = $student_path->admission_year ?? '';
        $profile->code = $student_path->code ?? '';
        $profile->personality_strengths = $student_path->personality_strengths ?? '';
        $profile->personality_weaknesses = $student_path->personality_weaknesses ?? '';
        $profile->vocational_description = $student_path->vocational_description ?? '';
        $profile->emotional_skills_level = $student_path->emotional_skills_level ?? '';
        $profile->goals_short = $student_path->goal_short_term ?? '';
        $profile->goals_medium = $student_path->goal_medium_term ?? '';
        $profile->goals_long = $student_path->goal_long_term ?? '';
        $profile->actions_short = $student_path->action_short_term ?? '';
        $profile->actions_medium = $student_path->action_medium_term ?? '';
        $profile->actions_long = $student_path->action_long_term ?? '';
    }
    
    // Datos de learning_style
    $learning_style = $DB->get_record("learning_style", array("user" => $user_id, "course" => $course_id));
    $profile->learning_style = $learning_style ? 'completed' : null;
    $profile->learning_style_data = $learning_style ? json_encode($learning_style) : null;
    
    // Datos de personality_test
    $personality_test = $DB->get_record("personality_test", array("user" => $user_id, "course" => $course_id));
    $profile->personality_traits = $personality_test ? 'completed' : null;
    $profile->personality_data = $personality_test ? json_encode($personality_test) : null;
    
    // Datos de tmms_24 (Inteligencia Emocional)
    $tmms_24 = $DB->get_record("tmms_24", array("user" => $user_id, "course" => $course_id));
    $profile->emotional_intelligence = $tmms_24 ? 'completed' : null;
    $profile->tmms_24_data = $tmms_24 ? json_encode($tmms_24) : null;
    
    // Extraer tipo Holland y puntuación de student_path
    if ($student_path && isset($student_path->vocational_areas)) {
        $profile->holland_type = $student_path->vocational_areas;
        $profile->holland_score = 100; // Placeholder, ajustar según datos reales
    } else {
        $profile->holland_type = null;
        $profile->holland_score = null;
    }
    
    // Calcular porcentaje de finalización (ahora incluye 4 tests)
    $completed_tests = 0;
    if ($student_path) $completed_tests++;
    if ($learning_style) $completed_tests++;
    if ($personality_test) $completed_tests++;
    if ($tmms_24) $completed_tests++;
    
    $profile->completion_percentage = round(($completed_tests / 4) * 100);
    
    return $profile;
}

/**
 * Obtiene estadísticas integradas del curso para los cuatro tipos de evaluaciones
 */
function get_integrated_course_stats($course_id) {
    global $DB;
    
    // Total de estudiantes en el curso
    $total_students = $DB->count_records_sql(
        "SELECT COUNT(DISTINCT u.id) 
         FROM {user} u
         INNER JOIN {role_assignments} ra ON u.id = ra.userid
         INNER JOIN {context} ctx ON ra.contextid = ctx.id
         WHERE ctx.contextlevel = 50 AND ra.roleid IN (5) AND ctx.instanceid = :courseid",
        ['courseid' => $course_id]
    );
    
    // Estudiantes que completaron student_path
    $student_path_completed = $DB->count_records("student_path", array("course" => $course_id));
    
    // Estudiantes que completaron learning_style
    $learning_style_completed = $DB->count_records("learning_style", array("course" => $course_id));
    
    // Estudiantes que completaron personality_test
    $personality_test_completed = $DB->count_records("personality_test", array("course" => $course_id));
    
    // Estudiantes que completaron tmms_24
    $tmms_24_completed = $DB->count_records("tmms_24", array("course" => $course_id));
    
    // Estudiantes con perfiles completos (4 evaluaciones)
    $complete_profiles_sql = "
        SELECT COUNT(DISTINCT sp.user) as complete_count
        FROM {student_path} sp
        INNER JOIN {learning_style} ls ON sp.user = ls.user AND sp.course = ls.course
        INNER JOIN {personality_test} pt ON sp.user = pt.user AND sp.course = pt.course
        INNER JOIN {tmms_24} tm ON sp.user = tm.user AND sp.course = tm.course
        WHERE sp.course = :courseid
    ";
    $complete_profiles = $DB->get_record_sql($complete_profiles_sql, ['courseid' => $course_id]);
    $complete_profiles_count = $complete_profiles ? $complete_profiles->complete_count : 0;
    
    // Preparar objeto de respuesta
    $stats = new stdClass();
    $stats->total_students = $total_students;
    $stats->complete_profiles = $complete_profiles_count;
    $stats->complete_profiles_percentage = $total_students > 0 ? round(($complete_profiles_count / $total_students) * 100, 1) : 0;
    
    $stats->student_path_completed = $student_path_completed;
    $stats->student_path_percentage = $total_students > 0 ? round(($student_path_completed / $total_students) * 100, 1) : 0;
    
    $stats->learning_style_completed = $learning_style_completed;
    $stats->learning_style_percentage = $total_students > 0 ? round(($learning_style_completed / $total_students) * 100, 1) : 0;
    
    $stats->personality_test_completed = $personality_test_completed;
    $stats->personality_test_percentage = $total_students > 0 ? round(($personality_test_completed / $total_students) * 100, 1) : 0;
    
    $stats->tmms_24_completed = $tmms_24_completed;
    $stats->tmms_24_percentage = $total_students > 0 ? round(($tmms_24_completed / $total_students) * 100, 1) : 0;
    
    return $stats;
}

/**
 * Genera un resumen legible del estilo de aprendizaje usando la misma visualización del bloque
 */
function get_learning_style_summary($learning_style_data) {
    if (!$learning_style_data) {
        return '<div class="alert alert-warning">' . get_string('no_data_available', 'block_student_path') . '</div>';
    }
    
    $data = json_decode($learning_style_data, true);
    if (!$data) {
        return '<div class="alert alert-warning">' . get_string('no_data_available', 'block_student_path') . '</div>';
    }
    
    $summary = '<div class="learning-style-visualization">';
    
    // Definir las dimensiones del estilo de aprendizaje
    $dimensions = array(
        array(
            'name' => 'Procesamiento',
            'active' => isset($data['ap_active']) ? intval($data['ap_active']) : 0,
            'reflexive' => isset($data['ap_reflexivo']) ? intval($data['ap_reflexivo']) : 0,
            'active_label' => 'Activo',
            'reflexive_label' => 'Reflexivo',
            'color_active' => '#e74c3c',
            'color_reflexive' => '#3498db'
        ),
        array(
            'name' => 'Percepción',
            'active' => isset($data['ap_sensorial']) ? intval($data['ap_sensorial']) : 0,
            'reflexive' => isset($data['ap_intuitivo']) ? intval($data['ap_intuitivo']) : 0,
            'active_label' => 'Sensorial',
            'reflexive_label' => 'Intuitivo',
            'color_active' => '#27ae60',
            'color_reflexive' => '#f39c12'
        ),
        array(
            'name' => 'Entrada',
            'active' => isset($data['ap_visual']) ? intval($data['ap_visual']) : 0,
            'reflexive' => isset($data['ap_verbal']) ? intval($data['ap_verbal']) : 0,
            'active_label' => 'Visual',
            'reflexive_label' => 'Verbal',
            'color_active' => '#9b59b6',
            'color_reflexive' => '#e67e22'
        ),
        array(
            'name' => 'Comprensión',
            'active' => isset($data['ap_secuencial']) ? intval($data['ap_secuencial']) : 0,
            'reflexive' => isset($data['ap_global']) ? intval($data['ap_global']) : 0,
            'active_label' => 'Secuencial',
            'reflexive_label' => 'Global',
            'color_active' => '#1abc9c',
            'color_reflexive' => '#34495e'
        )
    );
    
    foreach ($dimensions as $dimension) {
        $total = $dimension['active'] + $dimension['reflexive'];
        $active_percentage = $total > 0 ? round(($dimension['active'] / $total) * 100, 1) : 0;
        $reflexive_percentage = $total > 0 ? round(($dimension['reflexive'] / $total) * 100, 1) : 0;
        
        $summary .= '<div class="dimension-card mb-3">';
        $summary .= '<h6 class="dimension-title">' . $dimension['name'] . '</h6>';
        
        // Barra activa
        $summary .= '<div class="dimension-bar-group">';
        $summary .= '<div class="bar-label">' . $dimension['active_label'] . ': ' . $dimension['active'] . ' (' . $active_percentage . '%)</div>';
        $summary .= '<div class="progress mb-2" style="height: 20px;">';
        $summary .= '<div class="progress-bar" style="width: ' . $active_percentage . '%; background-color: ' . $dimension['color_active'] . ';"></div>';
        $summary .= '</div>';
        
        // Barra reflexiva
        $summary .= '<div class="bar-label">' . $dimension['reflexive_label'] . ': ' . $dimension['reflexive'] . ' (' . $reflexive_percentage . '%)</div>';
        $summary .= '<div class="progress mb-2" style="height: 20px;">';
        $summary .= '<div class="progress-bar" style="width: ' . $reflexive_percentage . '%; background-color: ' . $dimension['color_reflexive'] . ';"></div>';
        $summary .= '</div>';
        
        // Estilo dominante
        $dominant_style = $dimension['active'] > $dimension['reflexive'] ? 
            $dimension['active_label'] : $dimension['reflexive_label'];
        $dominant_percentage = max($active_percentage, $reflexive_percentage);
        
        $summary .= '<div class="dominant-style">';
        $summary .= '<strong>Dominante: </strong>';
        $summary .= '<span class="text-primary">' . $dominant_style . ' (' . $dominant_percentage . '%)</span>';
        $summary .= '</div>';
        $summary .= '</div>';
        $summary .= '</div>';
    }
    
    $summary .= '</div>';
    
    return $summary;
}

/**
 * Genera un resumen legible del perfil de personalidad usando la misma visualización del bloque
 */
function get_personality_summary($personality_data) {
    if (!$personality_data) {
        return '<div class="alert alert-warning">' . get_string('no_data_available', 'block_student_path') . '</div>';
    }
    
    $data = json_decode($personality_data, true);
    if (!$data) {
        return '<div class="alert alert-warning">' . get_string('no_data_available', 'block_student_path') . '</div>';
    }
    
    $summary = '<div class="personality-visualization">';
    
    // Dimensiones de personalidad
    $extraversion = isset($data['extraversion']) ? intval($data['extraversion']) : 0;
    $introversion = isset($data['introversion']) ? intval($data['introversion']) : 0;
    
    $sensing = isset($data['sensing']) ? intval($data['sensing']) : 0;
    $intuition = isset($data['intuition']) ? intval($data['intuition']) : 0;
    
    $thinking = isset($data['thinking']) ? intval($data['thinking']) : 0;
    $feeling = isset($data['feeling']) ? intval($data['feeling']) : 0;
    
    $judging = isset($data['judging']) ? intval($data['judging']) : 0;
    $perceptive = isset($data['perceptive']) ? intval($data['perceptive']) : 0;
    
    // Función para renderizar barras comparativas
    $render_bar = function($label1, $value1, $label2, $value2) {
        $total = $value1 + $value2;
        $percent1 = $total > 0 ? ($value1 / $total) * 100 : 50;
        $percent2 = $total > 0 ? ($value2 / $total) * 100 : 50;
        
        $output = '<div class="personality-dimension mb-3">';
        $output .= '<div class="d-flex justify-content-between mb-2">';
        $output .= '<span><strong>' . $label1 . '</strong> (' . $value1 . ')</span>';
        $output .= '<span><strong>' . $label2 . '</strong> (' . $value2 . ')</span>';
        $output .= '</div>';
        $output .= '<div class="progress" style="height: 25px;">';
        $output .= '<div class="progress-bar bg-info" style="width: ' . $percent1 . '%" aria-valuenow="' . $percent1 . '">';
        $output .= round($percent1, 1) . '%';
        $output .= '</div>';
        $output .= '<div class="progress-bar bg-warning" style="width: ' . $percent2 . '%" aria-valuenow="' . $percent2 . '">';
        $output .= round($percent2, 1) . '%';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        
        return $output;
    };
    
    $summary .= $render_bar('Extraversión', $extraversion, 'Introversión', $introversion);
    $summary .= $render_bar('Sensación', $sensing, 'Intuición', $intuition);
    $summary .= $render_bar('Pensamiento', $thinking, 'Sentimiento', $feeling);
    $summary .= $render_bar('Juicio', $judging, 'Percepción', $perceptive);
    
    // Calcular tipo MBTI
    $mbti_type = '';
    $mbti_type .= $extraversion >= $introversion ? 'E' : 'I';
    $mbti_type .= $sensing >= $intuition ? 'S' : 'N';
    $mbti_type .= $thinking >= $feeling ? 'T' : 'F';
    $mbti_type .= $judging >= $perceptive ? 'J' : 'P';
    
    // Descripciones MBTI
    $mbti_descriptions = [
        "ISTJ" => "práctica y centrada en los hechos, cuya fiabilidad no puede ser cuestionada.",
        "ISFJ" => "protectora muy dedicada y cálida, siempre lista para defender a sus seres queridos.",
        "INFJ" => "tranquila y mística, pero muy inspiradora e incansable idealista.",
        "INTJ" => "visionaria, pensadora estratégica y resolvente de problemas lógicos.",
        "ISTP" => "experimentadora audaz y práctica, maestra de todo tipo de herramientas.",
        "ISFP" => "artística flexible y encantadora, siempre dispuesta a explorar y experimentar algo nuevo.",
        "INFP" => "poética, amable y altruista, siempre dispuesta por ayudar a una buena causa.",
        "INTP" => "creativa e innovadora con una sed insaciable de conocimiento.",
        "ESTP" => "inteligente, enérgica y muy perceptiva, que realmente disfruta viviendo al límite.",
        "ESFP" => "espontánea, enérgica y entusiasta.",
        "ENFP" => "de espíritu libre, entusiasta, creativa y sociable, que siempre pueden encontrar una razón para sonreír.",
        "ENTP" => "pensadora, inteligente y curiosa, que no puede resistirse a un desafío intelectual.",
        "ESTJ" => "práctica y centrada en los hechos, cuya fiabilidad no puede ser cuestionada.",
        "ESFJ" => "extraordinariamente cariñosa, sociable y popular, siempre dispuesta a ayudar.",
        "ENFJ" => "líder, carismática e inspiradora, capaz de cautivar a su audiencia.",
        "ENTJ" => "líder, audaz, imaginativa y de voluntad fuerte, siempre encontrando una forma, o creándola."
    ];
    
    // Mostrar tipo MBTI
    $summary .= '<div class="mbti-summary text-center mt-3">';
    $summary .= '<h4 class="text-primary">' . $mbti_type . '</h4>';
    $summary .= '<p class="text-muted">' . ($mbti_descriptions[$mbti_type] ?? '') . '</p>';
    $summary .= '</div>';
    
    $summary .= '</div>';
    
    return $summary;
}

/**
 * Genera un resumen corto del estilo de aprendizaje para la tabla de estudiantes
 */
function get_learning_style_summary_short($learning_style_data) {
    if (!$learning_style_data) {
        return '<span class="text-muted">Sin datos</span>';
    }
    
    $data = json_decode($learning_style_data, true);
    if (!$data) {
        return '<span class="text-muted">Sin datos</span>';
    }
    
    $dominant_styles = [];
    
    // Determinar estilos dominantes
    if (isset($data['ap_active']) && isset($data['ap_reflexivo'])) {
        $dominant_styles[] = $data['ap_active'] > $data['ap_reflexivo'] ? 'Activo' : 'Reflexivo';
    }
    
    if (isset($data['ap_sensorial']) && isset($data['ap_intuitivo'])) {
        $dominant_styles[] = $data['ap_sensorial'] > $data['ap_intuitivo'] ? 'Sensorial' : 'Intuitivo';
    }
    
    if (isset($data['ap_visual']) && isset($data['ap_verbal'])) {
        $dominant_styles[] = $data['ap_visual'] > $data['ap_verbal'] ? 'Visual' : 'Verbal';
    }
    
    if (isset($data['ap_secuencial']) && isset($data['ap_global'])) {
        $dominant_styles[] = $data['ap_secuencial'] > $data['ap_global'] ? 'Secuencial' : 'Global';
    }
    
    return !empty($dominant_styles) ? 
        '<span class="learning-style-short">' . implode(', ', $dominant_styles) . '</span>' : 
        '<span class="text-muted">Procesando...</span>';
}

/**
 * Genera un resumen corto del perfil de personalidad para la tabla de estudiantes
 */
function get_personality_summary_short($personality_data) {
    if (!$personality_data) {
        return '<span class="text-muted">Sin datos</span>';
    }
    
    $data = json_decode($personality_data, true);
    if (!$data) {
        return '<span class="text-muted">Sin datos</span>';
    }
    
    // Calcular tipo MBTI simplificado
    $mbti_type = '';
    
    if (isset($data['extraversion']) && isset($data['introversion'])) {
        $mbti_type .= $data['extraversion'] >= $data['introversion'] ? 'E' : 'I';
    }
    
    if (isset($data['sensing']) && isset($data['intuition'])) {
        $mbti_type .= $data['sensing'] >= $data['intuition'] ? 'S' : 'N';
    }
    
    if (isset($data['thinking']) && isset($data['feeling'])) {
        $mbti_type .= $data['thinking'] >= $data['feeling'] ? 'T' : 'F';
    }
    
    if (isset($data['judging']) && isset($data['perceptive'])) {
        $mbti_type .= $data['judging'] >= $data['perceptive'] ? 'J' : 'P';
    }
    
    if (strlen($mbti_type) == 4) {
        return '<span class="mbti-badge-small">' . $mbti_type . '</span>';
    }
    
    return '<span class="text-muted">Procesando...</span>';
}

/**
 * Calcula los puntajes del TMMS-24 desde las respuestas individuales
 */
function calculate_tmms24_scores($responses) {
    $percepcion = array_sum(array_slice($responses, 0, 8));
    $comprension = array_sum(array_slice($responses, 8, 8));
    $regulacion = array_sum(array_slice($responses, 16, 8));
    
    return [
        'percepcion' => $percepcion,
        'comprension' => $comprension,
        'regulacion' => $regulacion
    ];
}

/**
 * Interpreta un puntaje del TMMS-24 según la dimensión y el género
 */
function interpret_tmms24_score($dimension, $score, $gender) {
    switch ($dimension) {
        case 'percepcion':
            if ($gender === 'M') {
                if ($score < 21) return get_string('needs_improvement', 'block_student_path');
                if ($score >= 22 && $score <= 32) return get_string('adequate', 'block_student_path');
                return get_string('needs_improvement', 'block_student_path');
            } else { // Mujer
                if ($score < 24) return get_string('needs_improvement', 'block_student_path');
                if ($score >= 25 && $score <= 35) return get_string('adequate', 'block_student_path');
                return get_string('needs_improvement', 'block_student_path');
            }
            break;
            
        case 'comprension':
            if ($gender === 'M') {
                if ($score < 25) return get_string('needs_improvement', 'block_student_path');
                if ($score >= 26 && $score <= 35) return get_string('adequate', 'block_student_path');
                return get_string('excellent', 'block_student_path');
            } else { // Mujer
                if ($score < 23) return get_string('needs_improvement', 'block_student_path');
                if ($score >= 24 && $score <= 34) return get_string('adequate', 'block_student_path');
                return get_string('excellent', 'block_student_path');
            }
            break;
            
        case 'regulacion':
            if ($gender === 'M') {
                if ($score < 23) return get_string('needs_improvement', 'block_student_path');
                if ($score >= 24 && $score <= 35) return get_string('adequate', 'block_student_path');
                return get_string('excellent', 'block_student_path');
            } else { // Mujer
                if ($score < 23) return get_string('needs_improvement', 'block_student_path');
                if ($score >= 24 && $score <= 34) return get_string('adequate', 'block_student_path');
                return get_string('excellent', 'block_student_path');
            }
            break;
    }
    return get_string('not_determined', 'block_student_path');
}

/**
 * Genera un resumen legible completo del TMMS-24
 */
function get_tmms24_summary($tmms_24_data) {
    if (!$tmms_24_data) {
        return '<div class="alert alert-warning">' . get_string('no_data_available', 'block_student_path') . '</div>';
    }
    
    $data = json_decode($tmms_24_data, true);
    if (!$data) {
        return '<div class="alert alert-warning">' . get_string('no_data_available', 'block_student_path') . '</div>';
    }
    
    // Calcular puntajes desde las respuestas individuales
    $responses = [];
    for ($i = 1; $i <= 24; $i++) {
        $responses[] = $data['item' . $i] ?? 0;
    }
    $scores = calculate_tmms24_scores($responses);
    
    $gender = $data['gender'] ?? 'F';
    
    // Generar visualización completa
    $html = '<div class="tmms24-summary">';
    $html .= '<div class="emotional-intelligence-dimensions">';
    
    // Percepción
    $html .= '<div class="ei-dimension">';
    $html .= '<h6>' . get_string('perception', 'block_student_path') . '</h6>';
    $html .= '<div class="score-container">';
    $html .= '<span class="score-value">' . $scores['percepcion'] . '</span>';
    $html .= '<span class="score-interpretation">' . interpret_tmms24_score('percepcion', $scores['percepcion'], $gender) . '</span>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Comprensión
    $html .= '<div class="ei-dimension">';
    $html .= '<h6>' . get_string('comprehension', 'block_student_path') . '</h6>';
    $html .= '<div class="score-container">';
    $html .= '<span class="score-value">' . $scores['comprension'] . '</span>';
    $html .= '<span class="score-interpretation">' . interpret_tmms24_score('comprension', $scores['comprension'], $gender) . '</span>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Regulación
    $html .= '<div class="ei-dimension">';
    $html .= '<h6>' . get_string('regulation', 'block_student_path') . '</h6>';
    $html .= '<div class="score-container">';
    $html .= '<span class="score-value">' . $scores['regulacion'] . '</span>';
    $html .= '<span class="score-interpretation">' . interpret_tmms24_score('regulacion', $scores['regulacion'], $gender) . '</span>';
    $html .= '</div>';
    $html .= '</div>';
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

/**
 * Genera un resumen corto del TMMS-24 para la tabla de estudiantes
 */
function get_tmms24_summary_short($tmms_24_data) {
    if (!$tmms_24_data) {
        return '<span class="text-muted">Sin datos</span>';
    }
    
    $data = json_decode($tmms_24_data, true);
    if (!$data) {
        return '<span class="text-muted">Sin datos</span>';
    }
    
    // Calcular puntajes desde las respuestas individuales
    $responses = [];
    for ($i = 1; $i <= 24; $i++) {
        $responses[] = $data['item' . $i] ?? 0;
    }
    $scores = calculate_tmms24_scores($responses);
    
    $gender = $data['gender'] ?? 'F';
    
    // Determinar las mejores dimensiones
    $dimensions = [];
    
    $perception_level = interpret_tmms24_score('percepcion', $scores['percepcion'], $gender);
    $comprehension_level = interpret_tmms24_score('comprension', $scores['comprension'], $gender);
    $regulation_level = interpret_tmms24_score('regulacion', $scores['regulacion'], $gender);
    
    // Mostrar de forma compacta
    $summary_parts = [];
    $summary_parts[] = 'P:' . $scores['percepcion'];
    $summary_parts[] = 'C:' . $scores['comprension'];
    $summary_parts[] = 'R:' . $scores['regulacion'];
    
    return '<span class="tmms24-short">' . implode(' | ', $summary_parts) . '</span>';
}
