<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

require_login();

$courseid = optional_param('cid', 0, PARAM_INT);

if ($courseid == SITEID && !$courseid) {
    redirect($CFG->wwwroot);
}

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$PAGE->set_course($course);
$context = $PAGE->context;


// Permitir acceso a administradores y profesores
$isadmin = is_siteadmin($USER);
$COURSE_ROLED_AS_TEACHER = $DB->get_record_sql("
    SELECT m.id
    FROM {user} m 
    LEFT JOIN {role_assignments} m2 ON m.id = m2.userid 
    LEFT JOIN {context} m3 ON m2.contextid = m3.id 
    LEFT JOIN {course} m4 ON m3.instanceid = m4.id 
    WHERE (m3.contextlevel = 50 AND m2.roleid IN (3, 4) AND m.id IN ( {$USER->id} )) 
    AND m4.id = {$courseid} 
");

if (!$isadmin && (!isset($COURSE_ROLED_AS_TEACHER->id) || !$COURSE_ROLED_AS_TEACHER->id)) {
    redirect(new moodle_url('/course/view.php', array('id' => $courseid)), 
             get_string('no_teacher_access', 'block_student_path'));
}

$PAGE->set_url('/blocks/student_path/teacher_view.php', array('cid' => $courseid));

$title = get_string('teacher_dashboard', 'block_student_path');

$PAGE->set_pagelayout('standard');
$PAGE->set_title($title . " : " . $course->fullname);
$PAGE->set_heading($title . " : " . $course->fullname);

echo $OUTPUT->header();
echo "<link rel='stylesheet' href='" . $CFG->wwwroot . "/blocks/student_path/styles.css'>";
echo "<div class='block_student_path_container'>";

echo "<h1 class='title_student_path'>" . get_string('students_path_list', 'block_student_path') . "</h1>";

// Obtener estadísticas
$stats = get_student_path_stats($courseid);

// Mostrar estadísticas resumidas
echo "<div class='stats-dashboard'>";
echo "<div class='row'>";
echo "<div class='col-md-3'>";
echo "<div class='stat-card'>";
echo "<div class='stat-number'>" . $stats['total_students'] . "</div>";
echo "<div class='stat-label'>" . get_string('total_students', 'block_student_path') . "</div>";
echo "</div>";
echo "</div>";

echo "<div class='col-md-3'>";
echo "<div class='stat-card'>";
echo "<div class='stat-number'>" . $stats['completed_profiles'] . "</div>";
echo "<div class='stat-label'>" . get_string('completed_profiles', 'block_student_path') . "</div>";
echo "</div>";
echo "</div>";

echo "<div class='col-md-3'>";
echo "<div class='stat-card'>";
echo "<div class='stat-number'>" . $stats['completion_rate'] . "%</div>";
echo "<div class='stat-label'>" . get_string('completion_rate', 'block_student_path') . "</div>";
echo "</div>";
echo "</div>";

echo "<div class='col-md-3'>";
echo "<div class='stat-card'>";
echo "<div class='stat-number'>" . ($stats['total_students'] - $stats['completed_profiles']) . "</div>";
echo "<div class='stat-label'>" . get_string('pending_profiles', 'block_student_path') . "</div>";
echo "</div>";
echo "</div>";

echo "</div>";
echo "</div>";

// Obtener lista de estudiantes con sus perfiles
$students_data = get_students_with_profiles($courseid);

echo "<div class='students-table-container'>";
echo "<h3>" . get_string('students_list', 'block_student_path') . "</h3>";

if (empty($students_data)) {
    echo "<div class='alert alert-info'>" . get_string('no_students_found', 'block_student_path') . "</div>";
} else {
    // Filtros y búsqueda
    echo "<div class='table-filters mb-3'>";
    echo "<div class='row'>";
    echo "<div class='col-md-6'>";
    echo "<input type='text' id='searchStudent' class='form-control' placeholder='" . get_string('search_student', 'block_student_path') . "'>";
    echo "</div>";
    echo "<div class='col-md-3'>";
    echo "<select id='filterStatus' class='form-select'>";
    echo "<option value='all'>" . get_string('all_students', 'block_student_path') . "</option>";
    echo "<option value='completed'>" . get_string('completed_only', 'block_student_path') . "</option>";
    echo "<option value='pending'>" . get_string('pending_only', 'block_student_path') . "</option>";
    echo "</select>";
    echo "</div>";
    echo "<div class='col-md-3'>";
    echo "<div class='btn-group' role='group'>";
    echo "<button type='button' class='btn btn-success btn-sm' onclick='exportData(\"csv\")'>📊 CSV</button>";
    echo "<button type='button' class='btn btn-primary btn-sm' onclick='exportData(\"excel\")'>📋 Excel</button>";
    echo "</div>";
    echo "<br><small class='text-muted mt-1 d-block'>O usa enlaces directos:</small>";
    echo "<div class='mt-1'>";
    $csv_url = new moodle_url('/blocks/student_path/export.php', array('cid' => $courseid, 'format' => 'csv'));
    $excel_url = new moodle_url('/blocks/student_path/export.php', array('cid' => $courseid, 'format' => 'excel'));
    echo "<a href='" . $csv_url . "' class='btn btn-outline-success btn-sm me-1'>CSV</a>";
    echo "<a href='" . $excel_url . "' class='btn btn-outline-primary btn-sm'>Excel</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    echo "</div>";

    echo "<div class='table-responsive'>";
    echo "<table class='table table-striped table-hover' id='studentsTable'>";
    echo "<thead class='table-dark'>";
    echo "<tr>";
    echo "<th>" . get_string('student_name', 'block_student_path') . "</th>";
    echo "<th>" . get_string('email', 'block_student_path') . "</th>";
    echo "<th>" . get_string('program', 'block_student_path') . "</th>";
    echo "<th>" . get_string('admission_year', 'block_student_path') . "</th>";
    echo "<th>" . get_string('code', 'block_student_path') . "</th>";
    echo "<th>" . get_string('status', 'block_student_path') . "</th>";
    echo "<th>" . get_string('last_update', 'block_student_path') . "</th>";
    echo "<th>" . get_string('actions', 'block_student_path') . "</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";

    foreach ($students_data as $student) {
        echo "<tr class='student-row' data-status='" . ($student->has_profile ? 'completed' : 'pending') . "'>";
        echo "<td><strong>" . $student->firstname . ' ' . $student->lastname . "</strong></td>";
        echo "<td>" . $student->email . "</td>";
        
        if ($student->has_profile) {
            echo "<td>" . $student->program . "</td>";
            echo "<td>" . $student->admission_year . "</td>";
            echo "<td>" . $student->code . "</td>";
            echo "<td><span class='badge bg-success'>" . get_string('completed', 'block_student_path') . "</span></td>";
            echo "<td>" . date('d/m/Y H:i', $student->updated_at) . "</td>";
            echo "<td>";
            echo "<a href='" . $CFG->wwwroot . "/blocks/student_path/view_profile.php?userid=" . $student->id . "&cid=" . $courseid . "' class='btn btn-sm btn-primary'>" . get_string('view_profile', 'block_student_path') . "</a>";
            echo "</td>";
        } else {
            echo "<td class='text-muted'>-</td>";
            echo "<td class='text-muted'>-</td>";
            echo "<td class='text-muted'>-</td>";
            echo "<td><span class='badge bg-warning'>" . get_string('pending', 'block_student_path') . "</span></td>";
            echo "<td class='text-muted'>-</td>";
            echo "<td>";
            echo "<span class='text-muted'>" . get_string('no_profile', 'block_student_path') . "</span>";
            echo "</td>";
        }
        
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}

echo "</div>";

echo "<div class='action-buttons mt-4'>";
echo "<a href='" . new moodle_url('/course/view.php', array('id' => $courseid)) . "' class='btn btn-secondary'>" . get_string('back_to_course', 'block_student_path') . "</a>";
echo "</div>";

echo "</div>";

// JavaScript para funcionalidad de la tabla
echo "<script>
document.addEventListener('DOMContentLoaded', function() {
    // Búsqueda en tiempo real
    const searchInput = document.getElementById('searchStudent');
    const filterStatus = document.getElementById('filterStatus');
    const table = document.getElementById('studentsTable');
    const rows = table.querySelectorAll('tbody tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusFilter = filterStatus.value;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const status = row.dataset.status;
            
            const matchesSearch = text.includes(searchTerm);
            const matchesStatus = statusFilter === 'all' || status === statusFilter;
            
            row.style.display = matchesSearch && matchesStatus ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    filterStatus.addEventListener('change', filterTable);

    /* Función para exportar datos (definida globalmente) */
    window.exportData = function(format) {
        console.log('Exportando en formato:', format);
        var url = '" . new moodle_url('/blocks/student_path/export.php') . "';
        var fullUrl = url + '?cid=" . $courseid . "&format=' + format;
        console.log('URL de exportación:', fullUrl);
        window.location.href = fullUrl;
    };
});
</script>";

echo $OUTPUT->footer();
?>
