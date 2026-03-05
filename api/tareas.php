<?php
require_once 'config.php';

$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $res = $db->query("SELECT * FROM tareas ORDER BY fecha_evento ASC");
    $tareas = [];
    while($f = $res->fetchArray(SQLITE3_ASSOC)) { $tareas[] = $f; }
    echo json_encode($tareas);
}

if ($metodo === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $prompt = "Hoy es ".date('Y-m-d').". Extrae de: '".$data['texto']."'. 
               Si es 'examen', recordatorio 7 días antes. Otro, 1 día antes. 
               Responde SOLO JSON: {\"titulo\":\"\", \"fecha_evento\":\"YYYY-MM-DD\", \"fecha_recordatorio\":\"YYYY-MM-DD\", \"etiqueta\":\"\"}";

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "model" => "gpt-4o-mini",
        "messages" => [["role" => "user", "content" => $prompt]],
        "temperature" => 0
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . OPENAI_API_KEY]);

    $res = curl_exec($ch);
    $ia_data = json_decode(json_decode($res, true)['choices'][0]['message']['content'], true);

    $stmt = $db->prepare("INSERT INTO tareas (titulo, fecha_evento, fecha_recordatorio, etiqueta) VALUES (:t, :fe, :fr, :e)");
    $stmt->bindValue(':t', $ia_data['titulo']);
    $stmt->bindValue(':fe', $ia_data['fecha_evento']);
    $stmt->bindValue(':fr', $ia_data['fecha_recordatorio']);
    $stmt->bindValue(':e', $ia_data['etiqueta']);
    $stmt->execute();
    
    echo json_encode(["status" => "ok"]);
}