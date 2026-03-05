<?php
// Mantén este archivo protegido
define('OPENAI_API_KEY', 'TU_API_KEY_AQUI');

// Configuración de la Base de Datos
$db = new SQLite3(__DIR__ . '/agenda_ia.db');

// Inicializar tabla si no existe
$db->exec("CREATE TABLE IF NOT EXISTS tareas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT,
    fecha_evento DATE,
    fecha_recordatorio DATE,
    etiqueta TEXT,
    completado INTEGER DEFAULT 0
)");
?>