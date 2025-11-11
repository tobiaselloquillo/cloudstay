<?php
require __DIR__ . '/../vendor/autoload.php';

use Google\Cloud\Spanner\SpannerClient;

// Leer variables de entorno
$projectId = getenv('GOOGLE_CLOUD_PROJECT') ?: null;
$instanceId = getenv('SPANNER_INSTANCE') ?: null;
$databaseId = getenv('SPANNER_DATABASE') ?: null;
$port = getenv('PORT') ?: 8080;

header('Content-Type: text/plain; charset=utf-8');

if (!$instanceId || !$databaseId) {
    http_response_code(500);
    echo "Falta configurar SPANNER_INSTANCE o SPANNER_DATABASE.\n";
    echo "SPANNER_INSTANCE={$instanceId}\n";
    echo "SPANNER_DATABASE={$databaseId}\n";
    exit(1);
}

try {
    // Construir cliente Spanner (si projectId es null usa ADC)
    $spannerOptions = [];
    if ($projectId) {
        $spannerOptions['projectId'] = $projectId;
    }

    $spanner = new SpannerClient($spannerOptions);
    $instance = $spanner->instance($instanceId);
    $database = $instance->database($databaseId);

    // Prueba simple: ejecutar SELECT 1 AS connected
    $sql = 'SELECT 1 AS connected';
    $result = $database->execute($sql);

    $connected = false;
    foreach ($result->rows() as $row) {
        // $row viene como arreglo asociativo
        $connected = isset($row['connected']) && $row['connected'] == 1;
    }

    if ($connected) {
        echo "Conexión a Cloud Spanner OK.\n";
        echo "Proyecto: " . ($projectId ?: 'ADC') . "\n";
        echo "Instancia: $instanceId\n";
        echo "Base de datos: $databaseId\n";
    } else {
        http_response_code(500);
        echo "No se obtuvo respuesta correcta desde Spanner.\n";
    }

    // Cerrar (opcional)
    $database->close();
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error conectando a Spanner: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
