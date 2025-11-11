<?php

// Carga las dependencias de Composer
require 'vendor/autoload.php';

use Google\Cloud\Spanner\SpannerClient;

// --- CONFIGURACIÓN ---
// (Estos son los datos que me diste)
$instanceId = 'ejemplospanner';
$databaseId = 'cloudstay';
// ---------------------

echo "<h1>Prueba de conexión a Spanner</h1>";
echo "<p>Instancia: $instanceId<br>Base de Datos: $databaseId</p>";

try {
    // Cuando se ejecuta en App Engine, la autenticación es automática.
    // No necesitas un archivo de credenciales JSON.
    $spanner = new SpannerClient();

    // Obtiene la instancia y la base de datos
    $instance = $spanner->instance($instanceId);
    $database = $instance->database($databaseId);

    // Ejecuta una consulta simple ("ping") para verificar la conexión
    $results = $database->execute('SELECT 1');

    // Si la consulta funciona, $results tendrá al menos una fila
    foreach ($results as $row) {
        if (isset($row[0]) && $row[0] == 1) {
            echo "<h2><font color='green'>¡Conexión Exitosa!</font></h2>";
        } else {
            echo "<h2><font color='orange'>Conexión extraña (no se recibió '1').</font></h2>";
        }
    }

} catch (\Exception $e) {
    // Si algo falla (permisos, API, no existe la BD, etc.)
    echo "<h2><font color='red'>Error en la Conexión:</font></h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}