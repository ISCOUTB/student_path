<?php
/**
 * Renderer - Student Path Block
 *
 * @package    block_student_path
 * @copyright  2026 SAVIO - Sistema de Aprendizaje Virtual Interactivo (UTB)
 * @author     SAVIO Development Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class block_student_path_renderer extends plugin_renderer_base {
    
    /**
     * Renderiza la vista de "No iniciado"
     */
    public function render_not_started_view($test_type) {
        $icon = 'fa-circle-o';
        $title = get_string('not_started', 'block_student_path');
        $message = get_string('not_started_desc', 'block_student_path'); 
        
        // Fallback strings if they don't exist
        if ($title == '[[not_started]]') $title = 'No iniciado';
        if ($message == '[[not_started_desc]]') $message = 'Aún no has realizado este test.';

        // Custom icons per test type
        switch ($test_type) {
            case 'learning_style': $icon = 'fa-lightbulb-o'; break;
            case 'personality': $icon = 'fa-users'; break;
            case 'chaside': $icon = 'fa-graduation-cap'; break;
            case 'tmms24': $icon = 'fa-heart'; break;
            case 'student_path': $icon = 'fa-map-signs'; break;
        }

        $data = [
            'icon' => $icon,
            'title' => $title,
            'message' => $message
        ];

        return $this->render_from_template('block_student_path/not_started', $data);
    }

    /**
     * Renderiza la vista de "En Progreso"
     */
    public function render_in_progress_view($answered, $total, $color_class) {
        $percent = ($total > 0) ? round(($answered / $total) * 100) : 0;
        $a = new stdClass();
        $a->answered = $answered;
        $a->total = $total;
        
        // Determinamos si el número debe ir afuera (negro) o adentro (blanco)
        $is_low = ($percent < 15); 
        $text_class = $is_low ? 'is-low' : 'is-high';

        $data = [
            'title' => get_string('in_progress_title', 'block_student_path'),
            'color_class' => $color_class,
            'percent' => $percent,
            'text_class' => $text_class,
            'details' => get_string('questions_answered', 'block_student_path', $a),
            'show_warning' => ($answered >= $total),
            'warning_msg' => get_string('remind_submit_test', 'block_student_path')
        ];

        return $this->render_from_template('block_student_path/in_progress', $data);
    }

    /**
     * Renderiza el resumen del estilo de aprendizaje
     */
    public function render_learning_style_summary($data) {
        return $this->render_from_template('block_student_path/learning_style_summary', $data);
    }

    public function render_personality_summary($data) {
        return $this->render_from_template('block_student_path/personality_summary', $data);
    }

    public function render_chaside_summary($data) {
        return $this->render_from_template('block_student_path/chaside_summary', $data);
    }

    public function render_tmms24_summary($data) {
        return $this->render_from_template('block_student_path/tmms24_summary', $data);
    }

    public function render_student_path_content($data) {
        return $this->render_from_template('block_student_path/student_path_content', $data);
    }

    /**
     * Renderiza una alerta simple
     */
    public function render_alert($message, $type = 'danger') {
        $data = [
            'message' => $message,
            'type' => $type
        ];
        return $this->render_from_template('block_student_path/alert', $data);
    }
}
