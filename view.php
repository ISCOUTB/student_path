<?php
require_once(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

if (!isloggedin()) {
    return;
}

$courseid = optional_param('cid', 0, PARAM_INT);
$edit = optional_param('edit', 0, PARAM_INT);
$error = optional_param('error', 0, PARAM_INT);

if ($courseid == SITEID && !$courseid) {
    redirect($CFG->wwwroot);
}

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
$PAGE->set_course($course);
$context = $PAGE->context;

$PAGE->set_url('/blocks/student_path/view.php', array('cid' => $courseid));

$title = get_string('pluginname', 'block_student_path');

$PAGE->set_pagelayout('embedded');
$PAGE->set_title($title . " : " . $course->fullname);
$PAGE->set_heading($title . " : " . $course->fullname);

// Verificar si ya existe información del estudiante
$entry = $DB->get_record('student_path', array('user' => $USER->id, 'course' => $courseid));

echo $OUTPUT->header();

// Usar las clases CSS nativas de Moodle en lugar de Bootstrap externo
echo "<div class='block_student_path_container'>";

if ($entry && !$edit) {
    // Mostrar información existente con opción de editar
    echo "<div class='card'>";
    echo "<div class='card-header'>";
    echo "<h2 class='card-title'>" . get_string('student_path_title', 'block_student_path') . "</h2>";
    echo "</div>";
    echo "<div class='card-body'>";
    
    // Sección 1: Información Personal
    echo "<div class='mb-4'>";
    echo "<h4 class='text-primary'>" . get_string('personal_info', 'block_student_path') . "</h4>";
    echo "<div class='row'>";
    echo "<div class='col-md-6'><strong>" . get_string('name', 'block_student_path') . ":</strong> " . $USER->firstname . ' ' . $USER->lastname . "</div>";
    echo "<div class='col-md-6'><strong>" . get_string('program', 'block_student_path') . ":</strong> " . $entry->program . "</div>";
    echo "<div class='col-md-6'><strong>" . get_string('admission_year', 'block_student_path') . ":</strong> " . $entry->admission_year . "</div>";
    echo "<div class='col-md-6'><strong>" . get_string('email', 'block_student_path') . ":</strong> " . $USER->email . "</div>";
    echo "<div class='col-md-6'><strong>" . get_string('code', 'block_student_path') . ":</strong> " . $entry->code . "</div>";
    echo "</div>";
    echo "</div>";
    
    // Sección 2: Autodescubrimiento
    echo "<div class='mb-4'>";
    echo "<h4 class='text-primary'>" . get_string('self_discovery', 'block_student_path') . "</h4>";
    echo "<div class='row'>";
    echo "<div class='col-md-4'>";
    echo "<h5>" . get_string('personality_aspects', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->personality_aspects)) . "</p>";
    echo "</div>";
    echo "<div class='col-md-4'>";
    echo "<h5>" . get_string('professional_interests', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->professional_interests)) . "</p>";
    echo "</div>";
    echo "<div class='col-md-4'>";
    echo "<h5>" . get_string('emotional_skills', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->emotional_skills)) . "</p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    // Sección 3: Metas y Aspiraciones
    echo "<div class='mb-4'>";
    echo "<h4 class='text-primary'>" . get_string('goals_aspirations', 'block_student_path') . "</h4>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->goals_aspirations)) . "</p>";
    echo "</div>";
    
    // Sección 4: Plan de Acción
    echo "<div class='mb-4'>";
    echo "<h4 class='text-primary'>" . get_string('action_plan', 'block_student_path') . "</h4>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->action_plan)) . "</p>";
    echo "</div>";
    
    echo "</div>"; // card-body
    echo "<div class='card-footer'>";
    echo "<div class='d-flex gap-2'>";
    echo "<a href='" . new moodle_url('/blocks/student_path/view.php', array('cid' => $courseid, 'edit' => 1)) . "' class='btn btn-primary'>" . get_string('edit_profile', 'block_student_path') . "</a>";
    echo "<a href='" . new moodle_url('/course/view.php', array('id' => $courseid)) . "' class='btn btn-secondary'>" . get_string('back_to_course', 'block_student_path') . "</a>";
    echo "</div>";
    echo "</div>"; // card-footer
    echo "</div>"; // card
} else {
    // Mostrar formulario para llenar o editar información
    echo "<div class='card'>";
    echo "<div class='card-header'>";
    echo "<h2 class='card-title'>" . get_string('student_path_form_title', 'block_student_path') . "</h2>";
    echo "<p class='card-text text-muted'>" . get_string('student_path_intro_form', 'block_student_path') . "</p>";
    echo "</div>";
    
    // Sección del Mapa de Identidad
    echo "<div class='card-body border-bottom'>";
    echo "<div class='alert alert-info identity-map-section' role='alert'>";
    echo "<h4 class='alert-heading'><i class='fas fa-map-marked-alt'></i> " . get_string('identity_map_title', 'block_student_path') . "</h4>";
    echo "<p>" . get_string('identity_map_intro', 'block_student_path') . "</p>";
    echo "<p class='mb-2'><strong>" . get_string('identity_map_opportunities', 'block_student_path') . "</strong></p>";
    echo "<ul class='mb-3'>";
    echo "<li>" . get_string('identity_map_self_knowledge', 'block_student_path') . "</li>";
    echo "<li>" . get_string('identity_map_goals', 'block_student_path') . "</li>";
    echo "<li>" . get_string('identity_map_action_plan', 'block_student_path') . "</li>";
    echo "</ul>";
    echo "<p class='mb-0'>" . get_string('identity_map_conclusion', 'block_student_path') . "</p>";
    echo "</div>";
    echo "</div>";
    
    if ($error) {
        echo "<div class='alert alert-danger m-3'>" . get_string('required_message', 'block_student_path') . "</div>";
    }
    
    echo "<div class='card-body'>";
    ?>
    <form method="POST" action="<?php echo $CFG->wwwroot; ?>/blocks/student_path/save.php" class="mform">
        
        <!-- Sección 1: Información Personal -->
        <fieldset class="mb-4">
            <legend class="h4 text-primary"><?php echo get_string('personal_info', 'block_student_path'); ?></legend>
            <div class="row">
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?php echo get_string('name', 'block_student_path'); ?></label>
                    <div class="form-control-plaintext bg-light p-2 border rounded">
                        <?php echo htmlspecialchars($USER->firstname . ' ' . $USER->lastname); ?>
                    </div>
                    <small class="form-text text-muted">Este campo se toma automáticamente de tu perfil de Moodle</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="program" class="form-label fw-bold">
                        <?php echo get_string('program', 'block_student_path'); ?> 
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="program" name="program" class="form-control" required 
                           value="<?php echo ($entry ? htmlspecialchars($entry->program) : ''); ?>" 
                           placeholder="<?php echo get_string('program_placeholder', 'block_student_path'); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="admission_year" class="form-label fw-bold">
                        <?php echo get_string('admission_year', 'block_student_path'); ?> 
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number" id="admission_year" name="admission_year" class="form-control" required 
                           min="2000" max="2030" value="<?php echo ($entry ? $entry->admission_year : ''); ?>" 
                           placeholder="2024">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?php echo get_string('email', 'block_student_path'); ?></label>
                    <div class="form-control-plaintext bg-light p-2 border rounded">
                        <?php echo htmlspecialchars($USER->email); ?>
                    </div>
                    <small class="form-text text-muted">Este campo se toma automáticamente de tu perfil de Moodle</small>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label fw-bold">
                        <?php echo get_string('code', 'block_student_path'); ?> 
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="code" name="code" class="form-control" required 
                           value="<?php echo ($entry ? htmlspecialchars($entry->code) : ''); ?>" 
                           placeholder="<?php echo get_string('code_placeholder', 'block_student_path'); ?>">
                </div>
                
            </div>
        </fieldset>
        
        <!-- Sección 2: Autodescubrimiento -->
        <fieldset class="mb-4">
            <legend class="h4 text-primary"><?php echo get_string('self_discovery', 'block_student_path'); ?></legend>
            <p class="text-muted mb-3"><?php echo get_string('self_discovery_description', 'block_student_path'); ?></p>
            
            <div class="mb-3">
                <label for="personality_aspects" class="form-label fw-bold">
                    <?php echo get_string('personality_aspects', 'block_student_path'); ?>
                </label>
                <textarea id="personality_aspects" name="personality_aspects" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('personality_aspects_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->personality_aspects) : ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="professional_interests" class="form-label fw-bold">
                    <?php echo get_string('professional_interests', 'block_student_path'); ?>
                </label>
                <textarea id="professional_interests" name="professional_interests" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('professional_interests_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->professional_interests) : ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="emotional_skills" class="form-label fw-bold">
                    <?php echo get_string('emotional_skills', 'block_student_path'); ?>
                </label>
                <textarea id="emotional_skills" name="emotional_skills" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('emotional_skills_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->emotional_skills) : ''); ?></textarea>
            </div>
            
        </fieldset>
        
        <!-- Sección 3: Metas y Aspiraciones -->
        <fieldset class="mb-4">
            <legend class="h4 text-primary"><?php echo get_string('goals_aspirations', 'block_student_path'); ?></legend>
            <p class="text-muted mb-3"><?php echo get_string('goals_aspirations_description', 'block_student_path'); ?></p>
            
            <div class="mb-3">
                <textarea id="goals_aspirations" name="goals_aspirations" class="form-control" rows="6" 
                          placeholder="<?php echo get_string('goals_aspirations_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->goals_aspirations) : ''); ?></textarea>
            </div>
            
        </fieldset>
        
        <!-- Sección 4: Plan de Acción -->
        <fieldset class="mb-4">
            <legend class="h4 text-primary"><?php echo get_string('action_plan', 'block_student_path'); ?></legend>
            <p class="text-muted mb-3"><?php echo get_string('action_plan_description', 'block_student_path'); ?></p>
            
            <div class="mb-3">
                <textarea id="action_plan" name="action_plan" class="form-control" rows="6" 
                          placeholder="<?php echo get_string('action_plan_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->action_plan) : ''); ?></textarea>
            </div>
            
        </fieldset>
        
        <!-- Campos ocultos -->
        <input type="hidden" name="cid" value="<?php echo $courseid; ?>">
        <?php if ($entry): ?>
            <input type="hidden" name="edit" value="1">
        <?php endif; ?>
        
    </form>
    </div>
    
    <!-- Botones de acción en el footer -->
    <div class="card-footer">
        <div class="d-flex gap-2">
            <button type="submit" form="studentpath_form" class="btn btn-primary">
                <i class="fa fa-save" aria-hidden="true"></i>
                <?php echo get_string('save_profile', 'block_student_path'); ?>
            </button>
            <a href="<?php echo new moodle_url('/course/view.php', array('id' => $courseid)); ?>" class="btn btn-secondary">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                <?php echo get_string('cancel', 'block_student_path'); ?>
            </a>
        </div>
        <small class="text-muted mt-2 d-block">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            Los campos marcados con <span class="text-danger">*</span> son obligatorios
        </small>
    </div>
    </div> <!-- card -->
    
    <!-- JavaScript para mejorar la experiencia -->
    <script>
    // Asignar ID único al formulario para que funcione con el botón
    document.querySelector('form.mform').id = 'studentpath_form';
    
    // Auto-guardar en localStorage como borrador
    const form = document.querySelector('form.mform');
    const formElements = form.querySelectorAll('input[type="text"], input[type="number"], textarea');
    
    // Cargar datos guardados
    formElements.forEach(element => {
        const saved = localStorage.getItem('studentpath_' + element.name);
        if (saved && !element.value) {
            element.value = saved;
        }
        
        // Guardar cambios
        element.addEventListener('input', function() {
            localStorage.setItem('studentpath_' + this.name, this.value);
        });
    });
    
    // Limpiar localStorage al enviar
    form.addEventListener('submit', function() {
        formElements.forEach(element => {
            localStorage.removeItem('studentpath_' + element.name);
        });
    });
    </script>
    <?php
}

echo "</div>";
echo $OUTPUT->footer();
?>
