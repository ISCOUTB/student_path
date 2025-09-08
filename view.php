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

// Verificar si existe información previa
require_once(dirname(__FILE__) . '/lib.php');
$entry = get_student_path($USER->id, $courseid);

// Asegurar que las propiedades nuevas existan (para compatibilidad con registros antiguos)
if ($entry) {
    if (!property_exists($entry, 'vocational_areas')) {
        $entry->vocational_areas = '';
    }
    if (!property_exists($entry, 'vocational_areas_secondary')) {
        $entry->vocational_areas_secondary = '';
    }
    if (!property_exists($entry, 'personality_strengths')) {
        $entry->personality_strengths = '';
    }
    if (!property_exists($entry, 'personality_weaknesses')) {
        $entry->personality_weaknesses = '';
    }
    if (!property_exists($entry, 'vocational_description')) {
        $entry->vocational_description = '';
    }
    if (!property_exists($entry, 'emotional_skills_level')) {
        $entry->emotional_skills_level = '';
    }
    if (!property_exists($entry, 'goal_short_term')) {
        $entry->goal_short_term = '';
    }
    if (!property_exists($entry, 'goal_medium_term')) {
        $entry->goal_medium_term = '';
    }
    if (!property_exists($entry, 'goal_long_term')) {
        $entry->goal_long_term = '';
    }
    if (!property_exists($entry, 'action_short_term')) {
        $entry->action_short_term = '';
    }
    if (!property_exists($entry, 'action_medium_term')) {
        $entry->action_medium_term = '';
    }
    if (!property_exists($entry, 'action_long_term')) {
        $entry->action_long_term = '';
    }
}

echo $OUTPUT->header();

// Mostrar enlace de administración para administradores del sitio
if (has_capability('moodle/site:config', context_system::instance())) {
    echo '<div class="alert alert-info" style="margin-bottom: 20px;">';
    echo '<div style="display: flex; justify-content: space-between; align-items: center;">';
    echo '<span><strong>🔧 Panel de Administrador</strong></span>';
    echo '<a href="' . new moodle_url('/blocks/student_path/admin_manage.php') . '" class="btn btn-primary btn-sm">';
    echo get_string('admin_manage_title', 'block_student_path') . '</a>';
    echo '</div>';
    echo '</div>';
}

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
    echo "<div class='col-md-6'>";
    echo "<h5>" . get_string('personality_strengths', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->personality_strengths ?? '')) . "</p>";
    echo "</div>";
    echo "<div class='col-md-6'>";
    echo "<h5>" . get_string('personality_weaknesses', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->personality_weaknesses ?? '')) . "</p>";
    echo "</div>";
    echo "<div class='col-md-6'>";
    echo "<h5>" . get_string('vocational_areas', 'block_student_path') . "</h5>";
    if ($entry->vocational_areas ?? '') {
        $area_label = get_string('vocational_area_' . strtolower($entry->vocational_areas), 'block_student_path');
        echo "<p class='text-muted'>" . $area_label . "</p>";
    } else {
        echo "<p class='text-muted'>No especificado</p>";
    }
    echo "</div>";
    echo "<div class='col-md-6'>";
    echo "<h5>" . get_string('vocational_areas_secondary', 'block_student_path') . "</h5>";
    if ($entry->vocational_areas_secondary ?? '') {
        $area_label_secondary = get_string('vocational_area_' . strtolower($entry->vocational_areas_secondary), 'block_student_path');
        echo "<p class='text-muted'>" . $area_label_secondary . "</p>";
    } else {
        echo "<p class='text-muted'>Ninguna seleccionada</p>";
    }
    echo "</div>";
    echo "<div class='col-md-6'>";
    echo "<h5>" . get_string('vocational_description', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->vocational_description ?? '')) . "</p>";
    echo "</div>";
    echo "<div class='col-md-12'>";
    echo "<h5>" . get_string('emotional_skills_level', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->emotional_skills_level ?? '')) . "</p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    // Sección 3: Metas
    echo "<div class='mb-4'>";
    echo "<h4 class='text-primary'>" . get_string('goals_aspirations', 'block_student_path') . "</h4>";
    echo "<div class='row'>";
    echo "<div class='col-md-4'>";
    echo "<h5>" . get_string('goal_short_term', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->goal_short_term ?? '')) . "</p>";
    echo "</div>";
    echo "<div class='col-md-4'>";
    echo "<h5>" . get_string('goal_medium_term', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->goal_medium_term ?? '')) . "</p>";
    echo "</div>";
    echo "<div class='col-md-4'>";
    echo "<h5>" . get_string('goal_long_term', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->goal_long_term ?? '')) . "</p>";
    echo "</div>";
    echo "</div>";
    echo "</div>";
    
    // Sección 4: Plan de Acción
    echo "<div class='mb-4'>";
    echo "<h4 class='text-primary'>" . get_string('action_plan', 'block_student_path') . "</h4>";
    echo "<div class='row'>";
    echo "<div class='col-md-4'>";
    echo "<h5>" . get_string('action_short_term', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->action_short_term ?? '')) . "</p>";
    echo "</div>";
    echo "<div class='col-md-4'>";
    echo "<h5>" . get_string('action_medium_term', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->action_medium_term ?? '')) . "</p>";
    echo "</div>";
    echo "<div class='col-md-4'>";
    echo "<h5>" . get_string('action_long_term', 'block_student_path') . "</h5>";
    echo "<p class='text-muted'>" . nl2br(htmlspecialchars($entry->action_long_term ?? '')) . "</p>";
    echo "</div>";
    echo "</div>";
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
                <label for="personality_strengths" class="form-label fw-bold">
                    <?php echo get_string('personality_strengths', 'block_student_path'); ?>
                </label>
                <textarea id="personality_strengths" name="personality_strengths" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('personality_strengths_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->personality_strengths ?? '') : ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="personality_weaknesses" class="form-label fw-bold">
                    <?php echo get_string('personality_weaknesses', 'block_student_path'); ?>
                </label>
                <textarea id="personality_weaknesses" name="personality_weaknesses" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('personality_weaknesses_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->personality_weaknesses ?? '') : ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="vocational_areas" class="form-label fw-bold">
                    <?php echo get_string('vocational_areas', 'block_student_path'); ?>
                    <span class="text-danger">*</span>
                </label>
                <p class="text-muted small"><?php echo get_string('vocational_areas_instruction', 'block_student_path'); ?></p>
                
                <?php
                $vocational_areas_list = [
                    'C' => get_string('vocational_area_c', 'block_student_path'),
                    'H' => get_string('vocational_area_h', 'block_student_path'),
                    'A' => get_string('vocational_area_a', 'block_student_path'),
                    'S' => get_string('vocational_area_s', 'block_student_path'),
                    'I' => get_string('vocational_area_i', 'block_student_path'),
                    'D' => get_string('vocational_area_d', 'block_student_path'),
                    'E' => get_string('vocational_area_e', 'block_student_path'),
                ];
                ?>
                
                <select id="vocational_areas" name="vocational_areas" class="form-control" required>
                    <option value="">Selecciona un área vocacional...</option>
                    <?php foreach ($vocational_areas_list as $code => $label): ?>
                        <option value="<?php echo $code; ?>" <?php echo ($entry && ($entry->vocational_areas ?? '') == $code) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="vocational_areas_secondary" class="form-label fw-bold">
                    <?php echo get_string('vocational_areas_secondary', 'block_student_path'); ?>
                </label>
                <p class="text-muted small"><?php echo get_string('vocational_areas_secondary_instruction', 'block_student_path'); ?></p>
                
                <select id="vocational_areas_secondary" name="vocational_areas_secondary" class="form-control">
                    <option value="">Selecciona un área vocacional adicional (opcional)...</option>
                    <?php foreach ($vocational_areas_list as $code => $label): ?>
                        <option value="<?php echo $code; ?>" <?php echo ($entry && ($entry->vocational_areas_secondary ?? '') == $code) ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="vocational_description" class="form-label fw-bold">
                    <?php echo get_string('vocational_description', 'block_student_path'); ?>
                </label>
                <textarea id="vocational_description" name="vocational_description" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('vocational_description_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->vocational_description ?? '') : ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="emotional_skills_level" class="form-label fw-bold">
                    <?php echo get_string('emotional_skills_level', 'block_student_path'); ?>
                </label>
                <textarea id="emotional_skills_level" name="emotional_skills_level" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('emotional_skills_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->emotional_skills_level ?? '') : ''); ?></textarea>
            </div>
            
        </fieldset>
        
        <!-- Sección 3: Metas -->
        <fieldset class="mb-4">
            <legend class="h4 text-primary"><?php echo get_string('goals_aspirations', 'block_student_path'); ?></legend>
            <p class="text-muted mb-3"><?php echo get_string('goals_aspirations_description', 'block_student_path'); ?></p>
            
            <!-- SMART Goals explanation -->
            <div class="alert alert-info mb-4">
                <h5><?php echo get_string('smart_goals_intro', 'block_student_path'); ?></h5>
                <ul class="mb-2">
                    <li><?php echo get_string('smart_specific', 'block_student_path'); ?></li>
                    <li><?php echo get_string('smart_measurable', 'block_student_path'); ?></li>
                    <li><?php echo get_string('smart_achievable', 'block_student_path'); ?></li>
                    <li><?php echo get_string('smart_relevant', 'block_student_path'); ?></li>
                    <li><?php echo get_string('smart_temporal', 'block_student_path'); ?></li>
                </ul>
                <p class="mb-0"><?php echo get_string('smart_example', 'block_student_path'); ?></p>
            </div>
            
            <div class="mb-3">
                <label for="goal_short_term" class="form-label fw-bold">
                    <?php echo get_string('goal_short_term', 'block_student_path'); ?>
                </label>
                <textarea id="goal_short_term" name="goal_short_term" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('goal_short_term_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->goal_short_term ?? '') : ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="goal_medium_term" class="form-label fw-bold">
                    <?php echo get_string('goal_medium_term', 'block_student_path'); ?>
                </label>
                <textarea id="goal_medium_term" name="goal_medium_term" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('goal_medium_term_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->goal_medium_term ?? '') : ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="goal_long_term" class="form-label fw-bold">
                    <?php echo get_string('goal_long_term', 'block_student_path'); ?>
                </label>
                <textarea id="goal_long_term" name="goal_long_term" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('goal_long_term_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->goal_long_term ?? '') : ''); ?></textarea>
            </div>
            
        </fieldset>
        
        <!-- Sección 4: Plan de Acción -->
        <fieldset class="mb-4">
            <legend class="h4 text-primary"><?php echo get_string('action_plan', 'block_student_path'); ?></legend>
            <p class="text-muted mb-3"><?php echo get_string('action_plan_description', 'block_student_path'); ?></p>
            
            <div class="alert alert-info mb-4">
                <p class="mb-0"><?php echo get_string('action_plan_template', 'block_student_path'); ?></p>
            </div>
            
            <div class="mb-3">
                <label for="action_short_term" class="form-label fw-bold">
                    <?php echo get_string('action_short_term', 'block_student_path'); ?>
                </label>
                <textarea id="action_short_term" name="action_short_term" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('action_short_term_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->action_short_term ?? '') : ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="action_medium_term" class="form-label fw-bold">
                    <?php echo get_string('action_medium_term', 'block_student_path'); ?>
                </label>
                <textarea id="action_medium_term" name="action_medium_term" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('action_medium_term_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->action_medium_term ?? '') : ''); ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="action_long_term" class="form-label fw-bold">
                    <?php echo get_string('action_long_term', 'block_student_path'); ?>
                </label>
                <textarea id="action_long_term" name="action_long_term" class="form-control" rows="4" 
                          placeholder="<?php echo get_string('action_long_term_placeholder', 'block_student_path'); ?>"><?php echo ($entry ? htmlspecialchars($entry->action_long_term ?? '') : ''); ?></textarea>
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
    
    // Validación de áreas vocacionales
    function validateVocationalAreas() {
        const primaryCheckboxes = document.querySelectorAll('.vocational-checkbox-primary:checked');
        const errorMessage = document.getElementById('vocational-error');
        
        if (primaryCheckboxes.length < 2) {
            if (!errorMessage) {
                const error = document.createElement('div');
                error.id = 'vocational-error';
                error.className = 'text-danger small mt-1';
                error.textContent = '<?php echo get_string('vocational_areas_validation', 'block_student_path'); ?>';
                document.getElementById('vocational_areas_primary').appendChild(error);
            }
            return false;
        } else {
            if (errorMessage) {
                errorMessage.remove();
            }
            return true;
        }
    }
    
    // Agregar listeners a los checkboxes principales
    document.querySelectorAll('.vocational-checkbox-primary').forEach(checkbox => {
        checkbox.addEventListener('change', validateVocationalAreas);
    });
    
    // Validar antes de enviar el formulario
    document.querySelector('form.mform').addEventListener('submit', function(e) {
        if (!validateVocationalAreas()) {
            e.preventDefault();
            document.getElementById('vocational_areas_primary').scrollIntoView({ behavior: 'smooth' });
            return false;
        }
    });
    
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
