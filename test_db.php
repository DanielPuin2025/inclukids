<?php
// Incluir la configuración local de la base de datos que está usando el servidor
$config = require __DIR__ . '/app/config/database.local.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4",
        $config['user'],
        $config['password']
    );

    echo "<h1>¡Conexión exitosa!</h1>";
    echo "<p>Conectado a la base de datos: <strong>" . $config['dbname'] . "</strong> en el host <strong>" . $config['host'] . "</strong></p>";

    // Consultar los docentes para ver si son los mismos de phpMyAdmin
    $stmt = $pdo->query("SELECT nombres, correo FROM docentes");
    $docentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h3>Docentes encontrados en esta base de datos:</h3><ul>";
    foreach ($docentes as $doc) {
        echo "<li>" . $doc['nombres'] . " (" . $doc['correo'] . ")</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error de conexión: " . $e->getMessage() . "</h3>";
}