<?php

require_once('../../config.php');

require_login();

echo "<h2>Diagnóstico y Reparación - Student Path DB</h2>";

// Verificar si la tabla existe
$dbman = $DB->get_manager();
$table_exists = $dbman->table_exists('student_path');

echo "<p><strong>Tabla student_path existe:</strong> " . ($table_exists ? 'SÍ' : 'NO') . "</p>";

if ($table_exists) {
    // Contar registros
    $count = $DB->count_records('student_path');
    echo "<p><strong>Total de registros:</strong> $count</p>";
    
    // Verificar estructura de la tabla
    echo "<h3>Verificación de Campos:</h3>";
    $required_fields = [
        'vocational_areas', 'vocational_areas_secondary', 'personality_strengths', 
        'personality_weaknesses', 'vocational_description', 'emotional_skills_level',
        'goal_short_term', 'goal_medium_term', 'goal_long_term',
        'action_short_term', 'action_medium_term', 'action_long_term'
    ];
    
    $missing_fields = [];
    
    try {
        $columns = $DB->get_columns('student_path');
        echo "<h4>Campos existentes:</h4><ul>";
        foreach ($columns as $column) {
            echo "<li><strong>" . $column->name . "</strong>: " . $column->type . "</li>";
        }
        echo "</ul>";
        
        echo "<h4>Verificación de campos requeridos:</h4><ul>";
        foreach ($required_fields as $field) {
            $exists = array_key_exists($field, $columns);
            echo "<li><strong>$field:</strong> " . ($exists ? '<span style="color: green;">EXISTE</span>' : '<span style="color: red;">FALTA</span>') . "</li>";
            if (!$exists) {
                $missing_fields[] = $field;
            }
        }
        echo "</ul>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error al obtener estructura: " . $e->getMessage() . "</p>";
    }
    
    // Si faltan campos, ofrecer reparación
    if (!empty($missing_fields)) {
        echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; margin: 10px 0;'>";
        echo "<h4>⚠️ Campos faltantes detectados</h4>";
        echo "<p>Los siguientes campos necesarios no existen: <strong>" . implode(', ', $missing_fields) . "</strong></p>";
        
        if (isset($_GET['repair']) && $_GET['repair'] == '1') {
            echo "<h4>🔧 Ejecutando reparación...</h4>";
            
            try {
                // Ejecutar el script de upgrade manualmente
                require_once(dirname(__FILE__) . '/db/upgrade.php');
                
                // Obtener la versión actual del plugin
                $current_version = $DB->get_field('config_plugins', 'value', 
                    array('plugin' => 'block_student_path', 'name' => 'version'));
                
                echo "<p>Versión actual del plugin: " . ($current_version ?: 'No encontrada') . "</p>";
                
                // Forzar ejecución del upgrade desde una versión anterior
                $result = xmldb_block_student_path_upgrade(2024090700); // Versión anterior
                
                if ($result) {
                    echo "<p style='color: green;'>✅ Reparación completada exitosamente!</p>";
                    echo "<p><a href='test_web.php'>Recargar página para verificar</a></p>";
                } else {
                    echo "<p style='color: red;'>❌ Error durante la reparación</p>";
                }
                
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error en reparación: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p><a href='test_web.php?repair=1' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>🔧 Reparar Base de Datos</a></p>";
        }
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
        echo "<h4>✅ Estructura de base de datos correcta</h4>";
        echo "<p>Todos los campos requeridos están presentes.</p>";
        echo "</div>";
    }
    
    // Probar inserción simple si todo está bien
    if (empty($missing_fields)) {
        echo "<h3>Prueba de Funcionalidad:</h3>";
        try {
            // Verificar si ya existe un registro de prueba
            $existing_test = $DB->get_record('student_path', array('user' => $USER->id, 'course' => 1, 'code' => 'TEST123'));
            
            if ($existing_test) {
                echo "<p style='color: orange;'>Registro de prueba ya existe con ID: " . $existing_test->id . "</p>";
                echo "<p><a href='test_web.php?delete_test=1'>Eliminar registro de prueba</a></p>";
            } else if (isset($_GET['test_insert']) && $_GET['test_insert'] == '1') {
                // Crear registro de prueba
                $test_record = new stdClass();
                $test_record->user = $USER->id;
                $test_record->course = 1;
                $test_record->name = 'Test User';
                $test_record->program = 'Test Program';
                $test_record->admission_year = 2024;
                $test_record->email = 'test@example.com';
                $test_record->code = 'TEST123';
                $test_record->vocational_areas = 'C';
                $test_record->vocational_areas_secondary = 'I';
                $test_record->personality_strengths = 'Test strengths';
                $test_record->created_at = time();
                $test_record->updated_at = time();
                
                $id = $DB->insert_record('student_path', $test_record);
                if ($id) {
                    echo "<p style='color: green;'>✅ Inserción de prueba exitosa! ID: $id</p>";
                } else {
                    echo "<p style='color: red;'>❌ Error en la inserción de prueba</p>";
                }
            } else {
                echo "<p><a href='test_web.php?test_insert=1'>Probar inserción de datos</a></p>";
            }
            
            // Eliminar registro de prueba si se solicita
            if (isset($_GET['delete_test']) && $_GET['delete_test'] == '1') {
                $deleted = $DB->delete_records('student_path', array('user' => $USER->id, 'course' => 1, 'code' => 'TEST123'));
                if ($deleted) {
                    echo "<p style='color: blue;'>🗑️ Registro de prueba eliminado</p>";
                    echo "<p><a href='test_web.php'>Recargar página</a></p>";
                }
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error en prueba: " . $e->getMessage() . "</p>";
        }
    }
    
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
    echo "<h4>❌ Tabla no existe</h4>";
    echo "<p>La tabla student_path no existe. Es necesario instalar el plugin.</p>";
    
    if (isset($_GET['create_table']) && $_GET['create_table'] == '1') {
        echo "<h4>🔧 Creando tabla...</h4>";
        try {
            require_once(dirname(__FILE__) . '/db/install.php');
            xmldb_block_student_path_install();
            echo "<p style='color: green;'>✅ Tabla creada exitosamente!</p>";
            echo "<p><a href='test_web.php'>Recargar página</a></p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error al crear tabla: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p><a href='test_web.php?create_table=1' style='background: #007cba; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px;'>🔧 Crear Tabla</a></p>";
    }
    echo "</div>";
}

echo "<hr>";
echo "<p><a href='" . $CFG->wwwroot . "/course/view.php?id=1'>← Volver al curso</a></p>";
echo "<p><strong>Nota:</strong> Después de cualquier reparación, ve a <em>Administración del sitio → Notificaciones</em> para ejecutar las actualizaciones de Moodle.</p>";

?>
