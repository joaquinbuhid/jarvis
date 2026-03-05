import React, { useState, useEffect } from 'react';

function App() {
  const [tareas, setTareas] = useState([]);
  const [input, setInput] = useState("");

  const cargarTareas = async () => {
    const res = await fetch('api/tareas.php');
    const data = await res.json();
    setTareas(data);
  };

  const agregarTarea = async (e) => {
    e.preventDefault();
    await fetch('api/tareas.php', {
      method: 'POST',
      body: JSON.stringify({ texto: input })
    });
    setInput("");
    cargarTareas();
  };

  useEffect(() => { cargarTareas(); }, []);

  return (
    <div style={{ padding: '20px', maxWidth: '800px', margin: 'auto' }}>
      <h1>Dashboard Inteligente</h1>
      <form onSubmit={agregarTarea}>
        <input 
          value={input} 
          onChange={(e) => setInput(e.target.value)}
          placeholder="Ej: Examen de redes el 15 de mayo..."
          style={{ width: '80%', padding: '10px' }}
        />
        <button type="submit">Añadir</button>
      </form>

      <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '15px', marginTop: '20px' }}>
        {tareas.map(t => {
          const hoy = new Date().toISOString().split('T')[0];
          const esAlerta = hoy >= t.fecha_recordatorio && hoy < t.fecha_evento;
          
          return (
            <div key={t.id} style={{ 
              padding: '15px', 
              borderRadius: '8px', 
              background: esAlerta ? '#ffebee' : '#fff',
              border: esAlerta ? '2px solid #ef5350' : '1px solid #ddd'
            }}>
              <small>{t.etiqueta.toUpperCase()}</small>
              <h3>{t.titulo}</h3>
              <p>📅 {t.fecha_evento}</p>
              {esAlerta && <b style={{color: '#d32f2f'}}>⚠️ ¡TIEMPO DE ESTUDIO!</b>}
            </div>
          );
        })}
      </div>
    </div>
  );
}

export default App;