<?php

require_once('../../config.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

echo "<h2>🔍 Verificación de Campos de Base de Datos - Student Path</h2>";

try {
    // Verificar estructura de la tabla
    $dbman = $DB->get_manager();
    
    if (!$dbman->table_exists('student_path')) {
        echo "<p style='color: red;'>❌ La tabla 'student_path' no existe</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ La tabla 'student_path' existe</p>";
    
    // Obtener información de las columnas
    $columns = $DB->get_columns('student_path');
    
    echo "<h3>📋 Estructura de la Tabla:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Por Defecto</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>" . $column->name . "</strong></td>";
        echo "<td>" . $column->meta_type . "</td>";
        echo "<td>" . ($column->not_null ? 'NO' : 'SI') . "</td>";
        echo "<td>" . ($column->default_value ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Probar la consulta de admin_manage.php
    echo "<h3>🧪 Probando Consulta de Administración:</h3>";
    
    $sql = "SELECT sp.id, sp.user, sp.created_at, sp.updated_at,
                   u.firstname, u.lastname, u.email, u.username
            FROM {student_path} sp
            JOIN {user} u ON u.id = sp.user
            ORDER BY sp.updated_at DESC, u.lastname ASC
            LIMIT 5";
    
    try {
        $participations = $DB->get_records_sql($sql);
        echo "<p style='color: green;'>✅ Consulta ejecutada exitosamente</p>";
        echo "<p><strong>Registros encontrados:</strong> " . count($participations) . "</p>";
        
        if (!empty($participations)) {
            echo "<h4>📄 Muestra de datos:</h4>";
            echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Usuario</th><th>Nombre</th><th>Email</th><th>Creado</th><th>Modificado</th></tr>";
            
            foreach (array_slice($participations, 0, 3) as $p) {
                echo "<tr>";
                echo "<td>" . $p->id . "</td>";
                echo "<td>" . $p->user . "</td>";
                echo "<td>" . $p->firstname . " " . $p->lastname . "</td>";
                echo "<td>" . $p->email . "</td>";
                echo "<td>" . ($p->created_at ? userdate($p->created_at) : 'NULL') . "</td>";
                echo "<td>" . ($p->updated_at ? userdate($p->updated_at) : 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error en la consulta: " . $e->getMessage() . "</p>";
    }
    
    // Verificar campos específicos que causaron problemas
    echo "<h3>🔍 Verificación de Campos Específicos:</h3>";
    
    $required_fields = ['user', 'course', 'created_at', 'updated_at', 'vocational_areas', 'emotional_skills_level'];
    
    foreach ($required_fields as $field) {
        if (array_key_exists($field, $columns)) {
            echo "<p style='color: green;'>✅ Campo '$field' existe</p>";
        } else {
            echo "<p style='color: red;'>❌ Campo '$field' NO existe</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>🔗 Enlaces de Prueba:</h3>";
    echo "<p><a href='admin_manage.php' target='_blank'>🔧 Probar Página de Administración</a></p>";
    echo "<p><a href='view.php?cid=1' target='_blank'>👁️ Probar Vista de Usuario</a></p>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; margin: 10px 0;'>";
    echo "<h4>❌ Error durante la verificación</h4>";
    echo "<p><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Línea:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}

?>
