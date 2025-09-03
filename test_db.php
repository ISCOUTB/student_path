<?php

define('CLI_SCRIPT', true);
require_once('/var/www/html/config.php');

echo "Testing Student Path Database Insert\n";

// Test data
$test_entry = new stdClass();
$test_entry->user = 2; // Asumiendo que existe un usuario con ID 2
$test_entry->course = 1; // Asumiendo que existe un curso con ID 1
$test_entry->name = 'Test Student';
$test_entry->program = 'Test Program';
$test_entry->admission_year = 2024;
$test_entry->email = 'test@example.com';
$test_entry->code = 'TEST123';
$test_entry->personality_aspects = 'Test personality';
$test_entry->professional_interests = 'Test interests';
$test_entry->emotional_skills = 'Test skills';
$test_entry->goals_aspirations = 'Test goals';
$test_entry->action_plan = 'Test plan';
$test_entry->created_at = time();
$test_entry->updated_at = time();

try {
    $entry_id = $DB->insert_record('student_path', $test_entry);
    echo "SUCCESS: Test record inserted with ID: " . $entry_id . "\n";
    
    // Clean up test record
    $DB->delete_records('student_path', array('id' => $entry_id));
    echo "Test record cleaned up\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test table structure
echo "\nChecking table structure:\n";
$columns = $DB->get_columns('student_path');
foreach($columns as $column) {
    echo $column->name . " - " . $column->type . "\n";
}

echo "\nTest completed.\n";
?>
