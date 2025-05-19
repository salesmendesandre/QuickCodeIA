/**
 * QuickCode IA – puente frontend entre Moodle y el iframe
 * 
 * Funcionalidades:
 *   - Carga el estado inicial del alumno (desde PHP)
 *   - Envía/recibe mensajes postMessage entre Moodle e iframe
 *   - Guarda puntuación y estado (solo si no está en modo readonly)
 */

/**
 * Carga el estado inicial del alumno y lo envía al iframe.
 * @param {number} cmid - ID del módulo (course module ID)
 * @param {number|null} userid - ID del usuario (opcional)
 */
function loadInitialState(cmid, userid = null) {
  const url = new URL(M.cfg.wwwroot + '/mod/quickcodeia/load_state.php');
  url.searchParams.append('cmid', cmid);
  if (userid) {
    url.searchParams.append('userid', userid);
  }

  fetch(url)
    .then(res => res.json())
    .then(data => {
      console.log('[Moodle] Estado recuperado:', data);
      if (data.status === 'ok') {
        const iframe = document.getElementById('quickcodeframe');
        iframe.contentWindow.postMessage(
          { event: 'state_loaded', ...data },
          '*'
        );
      }
    })
    .catch(err => console.error('[Moodle] Error al recuperar estado:', err));
}

// Esperar a que cargue el DOM
document.addEventListener('DOMContentLoaded', () => {
  const iframe = document.getElementById('quickcodeframe');
  console.log('[Moodle] iframe:', iframe);

  // Verificar que el payload global esté definido
  if (typeof moodleQuickcodePayload !== 'undefined') {
    const { cmId, userId, readonly = false } = moodleQuickcodePayload;

    // Cuando el iframe termine de cargar
    iframe.onload = () => {
      // Enviar configuración inicial
      iframe.contentWindow.postMessage(moodleQuickcodePayload, '*');
      // Cargar y enviar estado guardado
      loadInitialState(cmId, userId);
    };

    // Escuchar mensajes del iframe
    window.addEventListener('message', ({ data }) => {
      if (!data || typeof data !== 'object') return;
      console.log('[Moodle] Mensaje recibido del iframe:', data);

      // Si está en modo solo lectura, no hacer nada
      if (readonly) return;

      // -------------------------------------------------------------
      // Guardar puntuación enviada por el iframe
      // -------------------------------------------------------------
      // Guardar puntuación enviada por el iframe
      if (data.event === 'score_submitted') {
        fetch(M.cfg.wwwroot + '/mod/quickcodeia/save_score.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ ...data, sesskey: M.cfg.sesskey })
        })
          .then(async r => {
            if (!r.ok) {
              const error = await r.json();
              throw new Error(error.error || 'Unknown server error');
            }
            return r.json();
          })
          .then(r => console.log('[Moodle] Score guardado:', r))
          .catch(err => {
            console.error('[Moodle] Error al guardar score:', err);
            iframe.contentWindow.postMessage({
              event: 'error',
              source: 'moodle',
              type: 'score',
              message: 'Error saving score',
              detail: err.message || err.toString()
            }, '*');
          });
      }

      // Guardar estado del ejercicio (código, output, etc.)
      if (data.event === 'state_save') {
        fetch(M.cfg.wwwroot + '/mod/quickcodeia/save_state.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ ...data, sesskey: M.cfg.sesskey })
        })
          .then(async r => {
            if (!r.ok) {
              const error = await r.json();
              throw error;  // ← lanza el objeto JSON tal cual
            }
            return r.json();
          })
          .then(r => console.log('[Moodle] Estado guardado:', r))
          .catch(err => {
            console.error('[Moodle] Error al guardar estado:', err);

            iframe.contentWindow.postMessage({
              event: 'error',
              source: 'moodle',
              type: 'state',
              message: err.error || 'Error saving exercise state',
              code: err.code || null,
              detail: err
            }, '*');
          });

      }
    });
  }
});
