<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

define('OPENAI_API_KEY', 'TU_API_KEY_AQUI');
$db = new SQLite3(__DIR__ . '/agenda.db');

$db->exec("CREATE TABLE IF NOT EXISTS tareas (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    titulo TEXT,
    fecha_evento DATE,
    fecha_recordatorio DATE,
    etiqueta TEXT
)");