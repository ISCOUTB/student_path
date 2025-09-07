<?php

defined('MOODLE_INTERNAL') || die();

function xmldb_block_student_path_install() {
    global $DB;
    
    $dbman = $DB->get_manager();
    
    // Define table student_path
    $table = new xmldb_table('student_path');
    
    // Adding fields to table student_path
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('user', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('program', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('admission_year', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
    $table->add_field('email', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('code', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
    
    // Nuevos campos estructurados
    $table->add_field('personality_strengths', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('personality_weaknesses', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('vocational_areas', XMLDB_TYPE_CHAR, '50', null, null, null, null);
    $table->add_field('vocational_areas_secondary', XMLDB_TYPE_CHAR, '50', null, null, null, null);
    $table->add_field('vocational_description', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('emotional_skills_level', XMLDB_TYPE_TEXT, null, null, null, null, null);
    
    $table->add_field('goal_short_term', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('goal_medium_term', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('goal_long_term', XMLDB_TYPE_TEXT, null, null, null, null, null);
    
    $table->add_field('action_short_term', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('action_medium_term', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('action_long_term', XMLDB_TYPE_TEXT, null, null, null, null, null);
    
    // Campos legados (mantener compatibilidad)
    $table->add_field('personality_aspects', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('professional_interests', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('emotional_skills', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('goals_aspirations', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('action_plan', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('created_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('updated_at', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    
    // Adding keys to table student_path
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('user', XMLDB_KEY_FOREIGN, array('user'), 'user', array('id'));
    $table->add_key('course', XMLDB_KEY_FOREIGN, array('course'), 'course', array('id'));
    
    // Adding indexes to table student_path
    $table->add_index('user_course', XMLDB_INDEX_UNIQUE, array('user', 'course'));
    
    // Create table for student_path
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }
}
