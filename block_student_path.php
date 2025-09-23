<?php

require_once(__DIR__ . '/lib.php');

class block_student_path extends block_base
{

    public function init()
    {
        $this->title = get_string('pluginname', 'block_student_path');
    }

    public function instance_allow_multiple()
    {
        return false;
    }

    public function get_content()
    {
        global $OUTPUT, $CFG, $DB, $USER, $COURSE;

        if ($COURSE->id == SITEID) {
            return;
        }

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = "";
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if (!isloggedin()) {
            return;
        }

        // Verificar si el usuario es estudiante en el curso
        $COURSE_ROLED_AS_STUDENT = $DB->get_record_sql("
            SELECT m.id
            FROM {user} m 
            LEFT JOIN {role_assignments} m2 ON m.id = m2.userid 
            LEFT JOIN {context} m3 ON m2.contextid = m3.id 
            LEFT JOIN {course} m4 ON m3.instanceid = m4.id 
            WHERE (m3.contextlevel = 50 AND m2.roleid IN (5) AND m.id IN ( {$USER->id} )) 
            AND m4.id = {$COURSE->id} 
        ");

        // Verificar si el usuario es profesor en el curso
        $COURSE_ROLED_AS_TEACHER = $DB->get_record_sql("
            SELECT m.id
            FROM {user} m 
            LEFT JOIN {role_assignments} m2 ON m.id = m2.userid 
            LEFT JOIN {context} m3 ON m2.contextid = m3.id 
            LEFT JOIN {course} m4 ON m3.instanceid = m4.id 
            WHERE (m3.contextlevel = 50 AND m2.roleid IN (3, 4) AND m.id IN ( {$USER->id} )) 
            AND m4.id = {$COURSE->id} 
        ");

        // Verificar si es estudiante
        if (isset($COURSE_ROLED_AS_STUDENT->id) && $COURSE_ROLED_AS_STUDENT->id) {
            // Verificar si ya tiene información guardada
            $entry = $DB->get_record('student_path', array('user' => $USER->id, 'course' => $COURSE->id));

            $this->content->text .= '<div class="block_student_path_container">';
            if (!$entry) {
                // Si no tiene información, redirigir a la vista para llenar el formulario
                $this->content->text .= '<div class="student-path-block">';
                $this->content->text .= '<p>' . get_string('student_path_intro', 'block_student_path') . '</p>';
                $redirect_url = new moodle_url('/blocks/student_path/view.php', array('cid' => $COURSE->id));
                $this->content->text .= '<a href="' . $redirect_url . '" class="btn btn-primary">' . 
                                       get_string('complete_profile', 'block_student_path') . '</a>';
                $this->content->text .= '</div>';
            } else {
                // Si ya tiene información, mostrar resumen y opción de editar
                $this->content->text .= '<div class="student-path-block">';
                $this->content->text .= '<h4>' . get_string('student_path_summary', 'block_student_path') . '</h4>';
                
                // Mostrar información básica
                $this->content->text .= '<div class="student-info">';
                $this->content->text .= '<strong>' . get_string('name', 'block_student_path') . ':</strong> ' . $USER->firstname . ' ' . $USER->lastname . '<br>';
                $this->content->text .= '<strong>' . get_string('program', 'block_student_path') . ':</strong> ' . $entry->program . '<br>';
                $this->content->text .= '<strong>' . get_string('admission_year', 'block_student_path') . ':</strong> ' . $entry->admission_year . '<br>';
                $this->content->text .= '</div>';
                
                // Botón para ver/editar información completa
                $edit_url = new moodle_url('/blocks/student_path/view.php', array('cid' => $COURSE->id, 'edit' => 1));
                $this->content->text .= '<div class="student-path-actions">';
                $this->content->text .= '<a href="' . $edit_url . '" class="btn btn-secondary">' . 
                                       get_string('edit_profile', 'block_student_path') . '</a>';
                $this->content->text .= '</div>';
                $this->content->text .= '</div>';
            }
            $this->content->text .= '</div>';
        } else if (isset($COURSE_ROLED_AS_TEACHER->id) && $COURSE_ROLED_AS_TEACHER->id) {
            // Vista para profesores - mostrar estadísticas integradas y enlace a lista de estudiantes
            $stats = get_integrated_course_stats($COURSE->id);
            $this->content->text .= '<div class="block_student_path_container">';
            $this->content->text .= '<div class="student-path-block teacher-view">';
            $this->content->text .= '<h4>' . get_string('integrated_dashboard', 'block_student_path') . '</h4>';
            
            $this->content->text .= '<div class="stats-grid">';
            
            // Total de estudiantes
            $this->content->text .= '<div class="stat-item">';
            $this->content->text .= '<span class="stat-number">' . $stats->total_students . '</span>';
            $this->content->text .= '<span class="stat-label">' . get_string('total_students', 'block_student_path') . '</span>';
            $this->content->text .= '</div>';
            
            // Perfiles completos
            $this->content->text .= '<div class="stat-item">';
            $this->content->text .= '<span class="stat-number">' . $stats->complete_profiles . '</span>';
            $this->content->text .= '<span class="stat-label">' . get_string('complete_profiles', 'block_student_path') . '</span>';
            $this->content->text .= '</div>';
            
            // Porcentaje de finalización
            $this->content->text .= '<div class="stat-item">';
            $this->content->text .= '<span class="stat-number">' . $stats->complete_profiles_percentage . '%</span>';
            $this->content->text .= '<span class="stat-label">' . get_string('completion_rate', 'block_student_path') . '</span>';
            $this->content->text .= '</div>';
            
            $this->content->text .= '</div>';
            
            // Desglose por tipo de evaluación
            $this->content->text .= '<div class="evaluation-breakdown">';
            $this->content->text .= '<h5>' . get_string('evaluation_breakdown', 'block_student_path') . '</h5>';
            $this->content->text .= '<div class="breakdown-items">';
            
            $this->content->text .= '<div class="breakdown-item">';
            $this->content->text .= '<span class="breakdown-label">' . get_string('student_path_test', 'block_student_path') . ':</span>';
            $this->content->text .= '<span class="breakdown-value">' . $stats->student_path_completed . '/' . $stats->total_students . ' (' . $stats->student_path_percentage . '%)</span>';
            $this->content->text .= '</div>';
            
            $this->content->text .= '<div class="breakdown-item">';
            $this->content->text .= '<span class="breakdown-label">' . get_string('learning_style_test', 'block_student_path') . ':</span>';
            $this->content->text .= '<span class="breakdown-value">' . $stats->learning_style_completed . '/' . $stats->total_students . ' (' . $stats->learning_style_percentage . '%)</span>';
            $this->content->text .= '</div>';
            
            $this->content->text .= '<div class="breakdown-item">';
            $this->content->text .= '<span class="breakdown-label">' . get_string('personality_test', 'block_student_path') . ':</span>';
            $this->content->text .= '<span class="breakdown-value">' . $stats->personality_test_completed . '/' . $stats->total_students . ' (' . $stats->personality_test_percentage . '%)</span>';
            $this->content->text .= '</div>';
            
            $this->content->text .= '<div class="breakdown-item">';
            $this->content->text .= '<span class="breakdown-label">' . get_string('tmms_24_test', 'block_student_path') . ':</span>';
            $this->content->text .= '<span class="breakdown-value">' . $stats->tmms_24_completed . '/' . $stats->total_students . ' (' . $stats->tmms_24_percentage . '%)</span>';
            $this->content->text .= '</div>';
            
            $this->content->text .= '<div class="breakdown-item">';
            $this->content->text .= '<span class="breakdown-label">' . get_string('chaside_test', 'block_student_path') . ':</span>';
            $this->content->text .= '<span class="breakdown-value">' . $stats->chaside_completed . '/' . $stats->total_students . ' (' . $stats->chaside_percentage . '%)</span>';
            $this->content->text .= '</div>';
            
            $this->content->text .= '</div>';
            $this->content->text .= '</div>';
            
            // Botón para ver mapa de identidades integrado
            $teacher_url = new moodle_url('/blocks/student_path/teacher_view.php', array('cid' => $COURSE->id));
            $this->content->text .= '<div class="teacher-actions">';
            $this->content->text .= '<a href="' . $teacher_url . '" class="btn btn-primary">' . 
                                   get_string('view_identity_map', 'block_student_path') . '</a>';
            $this->content->text .= '</div>';
            $this->content->text .= '</div>';
            $this->content->text .= '</div>';
        } else {
            // Si no es estudiante ni profesor, mostrar mensaje genérico
            $this->content->text .= '<div class="block_student_path_container">';
            $this->content->text .= '<div class="student-path-block">';
            $this->content->text .= '<p>' . get_string('no_access', 'block_student_path') . '</p>';
            $this->content->text .= '</div>';
            $this->content->text .= '</div>';
        }

        // Agregar estilos CSS
        $this->content->text .= '<link rel="stylesheet" href="' . $CFG->wwwroot . '/blocks/student_path/styles.css">';

        return $this->content;
    }
}
