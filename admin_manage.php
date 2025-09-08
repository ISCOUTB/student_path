<?php

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

// Verificar permisos de administrador
admin_externalpage_setup('manageblocks');
require_capability('moodle/site:config', context_system::instance());

$action = optional_param('action', '', PARAM_ALPHA);
$userid = optional_param('userid', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$PAGE->set_url('/blocks/student_path/admin_manage.php');
$PAGE->set_title(get_string('admin_manage_title', 'block_student_path'));
$PAGE->set_heading(get_string('admin_manage_heading', 'block_student_path'));

// Procesar acciones
if ($action === 'delete' && $userid && $confirm) {
    if (confirm_sesskey()) {
        $DB->delete_records('student_path', array('user' => $userid));
        redirect($PAGE->url, get_string('admin_delete_success', 'block_student_path'), null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();

// Mostrar confirmación de eliminación individual
if ($action === 'delete' && $userid && !$confirm) {
    $user = $DB->get_record('user', array('id' => $userid), 'id,firstname,lastname,email');
    if ($user) {
        $message = get_string('admin_delete_confirm', 'block_student_path', fullname($user));
        $continueurl = new moodle_url($PAGE->url, array('action' => 'delete', 'userid' => $userid, 'confirm' => 1, 'sesskey' => sesskey()));
        $cancelurl = $PAGE->url;
        
        echo $OUTPUT->confirm($message, $continueurl, $cancelurl);
        echo $OUTPUT->footer();
        exit;
    }
}

// Obtener datos de participaciones
$sql = "SELECT sp.id, sp.user, sp.created_at, sp.updated_at,
               u.firstname, u.lastname, u.email, u.username
        FROM {student_path} sp
        JOIN {user} u ON u.id = sp.user
        ORDER BY sp.updated_at DESC, u.lastname ASC";

$participations = $DB->get_records_sql($sql);

echo $OUTPUT->heading(get_string('admin_manage_heading', 'block_student_path'));

if (empty($participations)) {
    echo $OUTPUT->notification(get_string('admin_no_participations', 'block_student_path'), 'info');
} else {
    $table = new html_table();
    $table->head = array(
        get_string('admin_table_student', 'block_student_path'),
        get_string('admin_table_email', 'block_student_path'),
        get_string('admin_table_created', 'block_student_path'),
        get_string('admin_table_modified', 'block_student_path'),
        get_string('admin_table_actions', 'block_student_path')
    );
    $table->attributes['class'] = 'admintable generaltable';
    
    foreach ($participations as $participation) {
        $userlink = new moodle_url('/user/view.php', array('id' => $participation->user));
        $username = html_writer::link($userlink, fullname($participation));
        
        $viewlink = new moodle_url('/blocks/student_path/admin_view_user.php', array('user_id' => $participation->user));
        $deletelink = new moodle_url($PAGE->url, array('action' => 'delete', 'userid' => $participation->user));
        
        $actions = '';
        $actions .= html_writer::link($viewlink, get_string('admin_action_view', 'block_student_path'), 
                                     array('class' => 'btn btn-sm btn-primary', 'style' => 'margin-right: 5px;'));
        $actions .= html_writer::link($deletelink, get_string('admin_action_delete', 'block_student_path'), 
                                     array('class' => 'btn btn-sm btn-danger'));
        
        $table->data[] = array(
            $username,
            $participation->email,
            userdate($participation->created_at),
            userdate($participation->updated_at),
            $actions
        );
    }
    
    echo html_writer::table($table);
    
    echo '<div style="margin-top: 20px;">';
    echo '<p><strong>' . get_string('admin_total_participations', 'block_student_path', count($participations)) . '</strong></p>';
    echo '</div>';
}


echo $OUTPUT->footer();
?>
