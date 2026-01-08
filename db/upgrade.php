<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_block_student_path_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    // Log para debug
    error_log("Student Path Upgrade: Old version = $oldversion, Target version = 2025090307");

    if ($oldversion < 2025090307) {
        
        // Define table student_path to modify
        $table = new xmldb_table('student_path');

        // Verificar que la tabla existe antes de modificarla
        if (!$dbman->table_exists($table)) {
            error_log("Student Path Upgrade: Table does not exist, creating it first");
            // Si la tabla no existe, crearla
            require_once(dirname(__DIR__) . '/db/install.php');
            xmldb_block_student_path_install();
        }

        // Add new fields for the updated questions structure
        
        // 2.1 Fortalezas (Strengths)
        $field = new xmldb_field('personality_strengths', XMLDB_TYPE_TEXT, null, null, null, null, null, 'code');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field personality_strengths");
        }

        // 2.2 Debilidades (Weaknesses)
        $field = new xmldb_field('personality_weaknesses', XMLDB_TYPE_TEXT, null, null, null, null, null, 'personality_strengths');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field personality_weaknesses");
        }

        // 2.3 Áreas Vocacionales (Vocational Areas)
        $field = new xmldb_field('vocational_areas', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'personality_weaknesses');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field vocational_areas");
        }

        // 2.3.1 Descripción de aptitudes e intereses
        $field = new xmldb_field('vocational_description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'vocational_areas');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field vocational_description");
        }

        // 2.3.2 Áreas Vocacionales Secundarias (Secondary Vocational Areas) - Optional
        $field = new xmldb_field('vocational_areas_secondary', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'vocational_description');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field vocational_areas_secondary");
        }

        // 2.4 Habilidades emocionales - crear el nuevo campo
        $field_new = new xmldb_field('emotional_skills_level', XMLDB_TYPE_TEXT, null, null, null, null, null, 'vocational_areas_secondary');
        
        if (!$dbman->field_exists($table, $field_new)) {
            $dbman->add_field($table, $field_new);
            error_log("Student Path Upgrade: Added field emotional_skills_level");
        }
        
        // Si existe el campo antiguo emotional_skills, copiar datos y eliminar
        $columns = $DB->get_columns('student_path');
        if (array_key_exists('emotional_skills', $columns)) {
            // Copiar datos del campo antiguo al nuevo
            $DB->execute("UPDATE {student_path} SET emotional_skills_level = emotional_skills WHERE emotional_skills IS NOT NULL");
            error_log("Student Path Upgrade: Copied data from emotional_skills to emotional_skills_level");
            
            // Eliminar el campo antiguo
            $field_old = new xmldb_field('emotional_skills', XMLDB_TYPE_TEXT, null, null, null, null, null);
            if ($dbman->field_exists($table, $field_old)) {
                $dbman->drop_field($table, $field_old);
                error_log("Student Path Upgrade: Dropped old field emotional_skills");
            }
        }

        // 3.1 Meta a corto plazo
        $field = new xmldb_field('goal_short_term', XMLDB_TYPE_TEXT, null, null, null, null, null, 'emotional_skills_level');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field goal_short_term");
        }

        // 3.2 Meta a mediano plazo
        $field = new xmldb_field('goal_medium_term', XMLDB_TYPE_TEXT, null, null, null, null, null, 'goal_short_term');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field goal_medium_term");
        }

        // 3.3 Meta a largo plazo
        $field = new xmldb_field('goal_long_term', XMLDB_TYPE_TEXT, null, null, null, null, null, 'goal_medium_term');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field goal_long_term");
        }

        // 4.1 Acciones para meta a corto plazo
        $field = new xmldb_field('action_short_term', XMLDB_TYPE_TEXT, null, null, null, null, null, 'goal_long_term');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field action_short_term");
        }

        // 4.2 Acciones para meta a mediano plazo
        $field = new xmldb_field('action_medium_term', XMLDB_TYPE_TEXT, null, null, null, null, null, 'action_short_term');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field action_medium_term");
        }

        // 4.3 Acciones para meta a largo plazo
        $field = new xmldb_field('action_long_term', XMLDB_TYPE_TEXT, null, null, null, null, null, 'action_medium_term');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            error_log("Student Path Upgrade: Added field action_long_term");
        }

        error_log("Student Path Upgrade: Completed successfully");
        
        // student_path savepoint reached
        upgrade_block_savepoint(true, 2025090307, 'student_path');
    }

    if ($oldversion < 2025093007) {
        
        // 1. Rename table student_path to block_student_path if needed
        $table = new xmldb_table('student_path');
        if ($dbman->table_exists($table) && !$dbman->table_exists(new xmldb_table('block_student_path'))) {
            $dbman->rename_table($table, 'block_student_path');
        }
        
        $table = new xmldb_table('block_student_path');

        if ($dbman->table_exists($table)) {
            // 2. Handle duplicates before adding unique index on user
            // Keep latest updated record for each user
            $sql = "SELECT user, MAX(updated_at) as max_updated
                    FROM {block_student_path}
                    GROUP BY user
                    HAVING COUNT(*) > 1";
            $duplicates = $DB->get_records_sql($sql);

            foreach ($duplicates as $dup) {
                // Delete older records
                $select = "user = :userid AND updated_at < :maxupdated";
                $params = ['userid' => $dup->user, 'maxupdated' => $dup->max_updated];
                $DB->delete_records_select('block_student_path', $select, $params);
                
                // Handle same timestamp duplicates (keep highest ID)
                $sql_ids = "SELECT id FROM {block_student_path} WHERE user = :userid ORDER BY id DESC";
                $ids = $DB->get_fieldset_sql($sql_ids, ['userid' => $dup->user]);
                if (count($ids) > 1) {
                    array_shift($ids); // Keep newest ID
                    $DB->delete_records_list('block_student_path', 'id', $ids);
                }
            }

            // 3. Drop course field
            // First drop any index that uses the course field
            
            // Try to drop the unique index on user, course if it exists
            $index = new xmldb_index('user_course_uix', XMLDB_INDEX_UNIQUE, ['user', 'course']);
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }
            
            // Also try non-unique just in case
            $index = new xmldb_index('user_course_ix', XMLDB_INDEX_NOTUNIQUE, ['user', 'course']);
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }

            // Drop simple index on course (mdl_studpath_cou_ix)
            $index = new xmldb_index('course', XMLDB_INDEX_NOTUNIQUE, ['course']);
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }

            $field = new xmldb_field('course');
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }

            // 4. Make fields nullable
            $fields_to_modify = [
                'name' => ['type' => XMLDB_TYPE_CHAR, 'length' => '255'],
                'program' => ['type' => XMLDB_TYPE_CHAR, 'length' => '255'],
                'admission_year' => ['type' => XMLDB_TYPE_INTEGER, 'length' => '4'],
                'email' => ['type' => XMLDB_TYPE_CHAR, 'length' => '255'],
                'code' => ['type' => XMLDB_TYPE_CHAR, 'length' => '50']
            ];

            foreach ($fields_to_modify as $name => $props) {
                $field = new xmldb_field($name);
                $field->set_attributes($props['type'], $props['length'], null, false, null, null, null);
                if ($dbman->field_exists($table, $field)) {
                    $dbman->change_field_notnull($table, $field);
                }
            }

            // 5. Add unique index
            $index = new xmldb_index('user_idx', XMLDB_INDEX_UNIQUE, ['user']);
            if (!$dbman->index_exists($table, $index)) {
                $dbman->add_index($table, $index);
            }
        }

        // 6. Create history table
        $table_history = new xmldb_table('block_student_path_history');
        $table_history->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table_history->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table_history->add_field('period', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table_history->add_field('content', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table_history->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        
        $table_history->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table_history->add_key('userid', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        
        $table_history->add_index('userid_period_idx', XMLDB_INDEX_UNIQUE, ['userid', 'period']);

        if (!$dbman->table_exists($table_history)) {
            $dbman->create_table($table_history);
        }

        upgrade_block_savepoint(true, 2025093007, 'student_path');
    }

    if ($oldversion < 2025093008) {
        $table = new xmldb_table('block_student_path');
        
        // Add admission_semester field
        $field = new xmldb_field('admission_semester', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'admission_year');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_block_savepoint(true, 2025093008, 'student_path');
    }

    if ($oldversion < 2025093009) {
        $table = new xmldb_table('block_student_path');

        // Fields to drop
        $fields_to_drop = [
            'personality_aspects',
            'professional_interests',
            'emotional_skills',
            'goals_aspirations',
            'action_plan'
        ];

        foreach ($fields_to_drop as $fieldname) {
            $field = new xmldb_field($fieldname);
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }
        }

        upgrade_block_savepoint(true, 2025093009, 'student_path');
    }

    if ($oldversion < 2025122401) {
        $table = new xmldb_table('block_student_path');
        
        // Add is_completed field
        $field = new xmldb_field('is_completed', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'action_long_term');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        
        // Update existing records
        $records = $DB->get_records('block_student_path');
        foreach ($records as $record) {
            $fields_to_check = [
                'name', 'program', 'admission_year', 'admission_semester', 'email', 'code',
                'personality_strengths', 'personality_weaknesses', 
                'vocational_areas', 'vocational_areas_secondary', 'vocational_description',
                'emotional_skills_level',
                'goal_short_term', 'goal_medium_term', 'goal_long_term',
                'action_short_term', 'action_medium_term', 'action_long_term'
            ];
            
            $total_fields = count($fields_to_check);
            $filled_count = 0;
            
            foreach ($fields_to_check as $f) {
                if (!empty($record->$f)) {
                    $filled_count++;
                }
            }
            
            if ($filled_count >= $total_fields) {
                $record->is_completed = 1;
                $DB->update_record('block_student_path', $record);
            }
        }

        upgrade_block_savepoint(true, 2025122401, 'student_path');
    }

    return true;
}
