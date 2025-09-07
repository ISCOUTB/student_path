<?php

require_once('../../config.php');

require_login();

// Solo administradores pueden ejecutar este script
require_capability('moodle/site:config', context_system::instance());

echo "<h2>🔧 Forzar Actualización de Base de Datos - Student Path</h2>";

if (isset($_GET['force']) && $_GET['force'] == '1') {
    echo "<h3>Ejecutando actualización forzada...</h3>";
    
    try {
        // Incluir el script de upgrade
        require_once(dirname(__FILE__) . '/db/upgrade.php');
        
        // Obtener la versión actual
        $current_version = $DB->get_field('config_plugins', 'value', 
            array('plugin' => 'block_student_path', 'name' => 'version'));
        
        echo "<p><strong>Versión actual en DB:</strong> " . ($current_version ?: 'No encontrada') . "</p>";
        
        // Obtener la versión del archivo
        require_once(dirname(__FILE__) . '/version.php');
        echo "<p><strong>Versión en archivo:</strong> " . $plugin->version . "</p>";
        
        // Forzar actualización desde versión anterior
        echo "<h4>Ejecutando xmldb_block_student_path_upgrade()...</h4>";
        $result = xmldb_block_student_path_upgrade(2024090700);
        
        if ($result) {
            // Actualizar la versión en la base de datos
            $record = $DB->get_record('config_plugins', 
                array('plugin' => 'block_student_path', 'name' => 'version'));
            
            if ($record) {
                $record->value = $plugin->version;
                $DB->update_record('config_plugins', $record);
                echo "<p style='color: green;'>✅ Versión actualizada en config_plugins</p>";
            } else {
                // Crear registro si no existe
                $new_record = new stdClass();
                $new_record->plugin = 'block_student_path';
                $new_record->name = 'version';
                $new_record->value = $plugin->version;
                $DB->insert_record('config_plugins', $new_record);
                echo "<p style='color: green;'>✅ Registro de versión creado en config_plugins</p>";
            }
            
            echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
            echo "<h4>✅ Actualización completada exitosamente!</h4>";
            echo "<p>La base de datos ha sido actualizada con los nuevos campos.</p>";
            echo "</div>";
        } else {
            echo "<p style='color: red;'>❌ Error durante la actualización</p>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
        echo "<h4>❌ Error durante la actualización</h4>";
        echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
        echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
        echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
        echo "</div>";
    }
    
    echo "<p><a href='force_upgrade.php'>← Volver</a> | <a href='test_web.php'>Probar base de datos</a></p>";
    
} else {
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; margin: 10px 0;'>";
    echo "<h4>⚠️ Advertencia</h4>";
    echo "<p>Este script forzará la ejecución del script de actualización de la base de datos.</p>";
    echo "<p><strong>Úsalo solo si:</strong></p>";
    echo "<ul>";
    echo "<li>Los campos de la base de datos no se han creado correctamente</li>";
    echo "<li>El formulario no guarda o carga datos</li>";
    echo "<li>Aparecen errores de 'Undefined property'</li>";
    echo "</ul>";
    echo "<p><strong>Antes de continuar:</strong> Haz una copia de seguridad de la base de datos.</p>";
    echo "</div>";
    
    // Mostrar información actual
    echo "<h3>Estado Actual:</h3>";
    $current_version = $DB->get_field('config_plugins', 'value', 
        array('plugin' => 'block_student_path', 'name' => 'version'));
    
    require_once(dirname(__FILE__) . '/version.php');
    
    echo "<p><strong>Versión en DB:</strong> " . ($current_version ?: 'No encontrada') . "</p>";
    echo "<p><strong>Versión en archivo:</strong> " . $plugin->version . "</p>";
    
    if ($current_version != $plugin->version) {
        echo "<p style='color: orange;'>⚠️ Las versiones no coinciden - Se requiere actualización</p>";
    } else {
        echo "<p style='color: green;'>✅ Las versiones coinciden</p>";
    }
    
    // Verificar campos
    $dbman = $DB->get_manager();
    if ($dbman->table_exists('student_path')) {
        $required_fields = [
            'vocational_areas', 'vocational_areas_secondary', 'personality_strengths', 
            'personality_weaknesses', 'vocational_description', 'emotional_skills_level',
            'goal_short_term', 'goal_medium_term', 'goal_long_term',
            'action_short_term', 'action_medium_term', 'action_long_term'
        ];
        
        $columns = $DB->get_columns('student_path');
        $missing_fields = [];
        
        foreach ($required_fields as $field) {
            if (!array_key_exists($field, $columns)) {
                $missing_fields[] = $field;
            }
        }
        
        if (!empty($missing_fields)) {
            echo "<p style='color: red;'>❌ Campos faltantes: " . implode(', ', $missing_fields) . "</p>";
            echo "<p><strong>Recomendación:</strong> Ejecutar la actualización forzada.</p>";
        } else {
            echo "<p style='color: green;'>✅ Todos los campos requeridos están presentes</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ La tabla 'student_path' no existe</p>";
    }
    
    echo "<div style='margin: 20px 0;'>";
    echo "<a href='force_upgrade.php?force=1' style='background: #dc3545; color: white; padding: 12px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;'>🔧 FORZAR ACTUALIZACIÓN</a>";
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='test_web.php'>🧪 Probar base de datos</a> | <a href='" . $CFG->wwwroot . "/course/view.php?id=1'>← Volver al curso</a></p>";

?>
