<?php

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    // Agregar enlace a la página de administración en el menú de bloques
    $ADMIN->add('blocksettings', new admin_externalpage(
        'manageblockstudentpath', 
        get_string('admin_manage_title', 'block_student_path'),
        new moodle_url('/blocks/student_path/admin_manage.php'),
        'moodle/site:config'
    ));
}
