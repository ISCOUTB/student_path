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

    return true;
}
