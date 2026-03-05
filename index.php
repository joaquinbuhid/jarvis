<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard IA</title>
    <style>
        body { font-family: sans-serif; background: #f0f2f5; padding: 40px; color: #333; }
        .container { max-width: 900px; margin: auto; }
        .form-group { background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        input { width: 75%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; }
        button { padding: 12px 20px; background: #007bff; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .card { background: #fff; padding: 20px; border-radius: 10px; border-top: 5px solid #ccc; }
        .alerta { border-top-color: #e74c3c; background: #fff5f5; border: 1px solid #ffc1c1; }
        .badge { font-size: 0.7em; padding: 3px 8px; border-radius: 10px; background: #eee; text-transform: uppercase; }
        .dias { font-weight: bold; color: #e74c3c; display: block; margin-top: 10px; }
    </style>
</head>
<body>

<div class="container">
    <h1>Mi Dashboard Inteligente</h1>
    
    <div class="form-group">
        <form action="procesar.php" method="POST">
            <input type="text" name="prompt_usuario" placeholder="Ej: Entregar reporte el 12 de marzo, etiqueta trabajo" required>
            <button type="submit">Añadir con IA</button>
        </form>
    </div>

    <div class="grid">
        <?php
        $res = $db->query("SELECT * FROM tareas ORDER BY fecha_evento ASC");
        $hoy = new DateTime();

        while ($f = $res->fetchArray(SQLITE3_ASSOC)):
            $f_evento = new DateTime($f['fecha_evento']);
            $f_recordatorio = new DateTime($f['fecha_recordatorio']);
            
            // Lógica: Si hoy es >= fecha de aviso, se activa la alerta
            $activar_alerta = ($hoy >= $f_recordatorio && $hoy < $f_evento);
            $diff = $hoy->diff($f_evento)->format("%a");
        ?>
            <div class="card <?php echo $activar_alerta ? 'alerta' : ''; ?>">
                <span class="badge"><?php echo $f['etiqueta']; ?></span>
                <h3><?php echo $f['titulo']; ?></h3>
                <small>Fecha evento: <?php echo $f['fecha_evento']; ?></small>
                
                <?php if ($activar_alerta): ?>
                    <span class="dias">⚠️ ¡Faltan <?php echo $diff; ?> días!</span>
                <?php else: ?>
                    <p>Faltan: <?php echo $diff; ?> días</p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>