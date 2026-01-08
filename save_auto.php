<?php
/**
 * Auto-save Functionality - Student Path Block
 *
 * @package    block_student_path
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

// Check if user is logged in
if (!isloggedin()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Check permissions
$context = context_system::instance(); // Or course context if we had it, but for auto-save system context is safer fallback or we pass courseid just for context check
$courseid = optional_param('courseid', SITEID, PARAM_INT);
if ($courseid) {
    $context = context_course::instance($courseid);
}

if (!has_capability('block/student_path:makemap', $context)) {
    echo json_encode(['success' => false, 'message' => 'No permission']);
    exit;
}

// Get existing record first (Fix variable scope)
$existing = $DB->get_record('block_student_path', array('user' => $USER->id));

// Validate Sesskey (CSRF Protection)
require_sesskey();

// Get all possible fields
$fields = [
    'program', 'admission_year', 'admission_semester', 'code',
    'personality_strengths', 'personality_weaknesses',
    'vocational_areas', 'vocational_areas_secondary',
    'vocational_description', 'emotional_skills_level',
    'goal_short_term', 'goal_medium_term', 'goal_long_term',
    'action_short_term', 'action_medium_term', 'action_long_term'
];

$data = new stdClass();
$data->user = $USER->id;
$data->updated_at = time();

// Populate data object
foreach ($fields as $field) {
    // Use PARAM_RAW for text areas to preserve formatting, PARAM_TEXT for others
    
    if (in_array($field, ['program', 'code', 'vocational_areas', 'vocational_areas_secondary'])) {
        $val = optional_param($field, null, PARAM_TEXT);
    } elseif ($field == 'admission_year' || $field == 'admission_semester') {
        $val = optional_param($field, null, PARAM_INT);
    } else {
        $val = optional_param($field, null, PARAM_TEXT); // Textareas
    }
    
    if ($val !== null) {
        $data->$field = $val;
    }
}

// Add required fields for new records
$data->name = fullname($USER);
$data->email = $USER->email;

// Calculate is_completed
$check_record = clone $data;
if ($existing) {
    foreach ($existing as $key => $value) {
        if (!isset($check_record->$key)) {
            $check_record->$key = $value;
        }
    }
}

$fields_to_check = [
    'name', 'program', 'admission_year', 'admission_semester', 'email', 'code',
    'personality_strengths', 'personality_weaknesses', 
    'vocational_areas', 'vocational_areas_secondary', 'vocational_description',
    'emotional_skills_level',
    'goal_short_term', 'goal_medium_term', 'goal_long_term',
    'action_short_term', 'action_medium_term', 'action_long_term'
];

$is_completed = 1;
foreach ($fields_to_check as $field) {
    if (empty($check_record->$field)) {
        $is_completed = 0;
        break;
    }
}
$data->is_completed = $is_completed;

try {
    $transaction = $DB->start_delegated_transaction();

    if ($existing) {
        $data->id = $existing->id;
        
        // Snapshot Logic: Check if we need to archive the previous state
        $current_period = block_student_path_get_semester(time());
        $last_updated_period = block_student_path_get_semester($existing->updated_at);
        
        // If we are in a new period compared to the last update, archive the OLD state
        $history_created = false;
        if ($current_period !== $last_updated_period) {
            // Check if we already have a snapshot for that old period
            if (!$DB->record_exists('block_student_path_history', ['userid' => $USER->id, 'period' => $last_updated_period])) {
                $history = new stdClass();
                $history->userid = $USER->id;
                $history->period = $last_updated_period;
                $history->content = json_encode($existing);
                $history->timecreated = time();
                
                $DB->insert_record('block_student_path_history', $history);
                $history_created = true;
            }
        }
        
        $DB->update_record('block_student_path', $data);
    } else {
        $data->created_at = time();
        $DB->insert_record('block_student_path', $data);
        $history_created = false;
    }

    $transaction->allow_commit();

    $response = ['success' => true, 'history_created' => $history_created];

    if ($history_created) {
        // Fetch updated history records to send back
        $history_records = $DB->get_records('block_student_path_history', array('userid' => $USER->id), 'period DESC');
        $history_data = [];
        foreach ($history_records as $h) {
            $content = json_decode($h->content);
            $date = isset($content->updated_at) ? $content->updated_at : $h->timecreated;
            $history_data[] = [
                'id' => $h->id,
                'period' => block_student_path_format_period($h->period),
                'date_formatted' => userdate($date)
            ];
        }
        $response['history_data'] = $history_data;
    }

    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
