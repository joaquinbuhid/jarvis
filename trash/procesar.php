<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['prompt_usuario'])) {
    $input = $_POST['prompt_usuario'];
    $hoy = date('Y-m-d');

    $promptIA = "Extrae datos de: '$input'. Hoy es $hoy. 
    Reglas: 1. Etiqueta 'examen' o 'entrega' = recordatorio 7 días antes. 
    2. Otro = 1 día antes. 
    Responde SOLO JSON: {\"titulo\":\"\", \"fecha_evento\":\"YYYY-MM-DD\", \"fecha_recordatorio\":\"YYYY-MM-DD\", \"etiqueta\":\"\"}";

    $ch = curl_init('https://api.openai.com/v1/chat/completions');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        "model" => "gpt-4o-mini",
        "messages" => [["role" => "user", "content" => $promptIA]],
        "temperature" => 0
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENAI_API_KEY
    ]);

    $res = curl_exec($ch);
    $res_json = json_decode($res, true);
    
    if (isset($res_json['choices'][0]['message']['content'])) {
        $datos = json_decode($res_json['choices'][0]['message']['content'], true);
        
        $stmt = $db->prepare("INSERT INTO tareas (titulo, fecha_evento, fecha_recordatorio, etiqueta) VALUES (:t, :fe, :fr, :e)");
        $stmt->bindValue(':t', $datos['titulo']);
        $stmt->bindValue(':fe', $datos['fecha_evento']);
        $stmt->bindValue(':fr', $datos['fecha_recordatorio']);
        $stmt->bindValue(':e', $datos['etiqueta']);
        $stmt->execute();
    }
    
    // Volver al dashboard después de procesar
    header("Location: index.php?success=1");
    exit();
}