<?php
// ============================================================================
//  QuickCode IA — Cadenas de idioma en español
//  mod/quickcodeia/lang/es/quickcodeia.php
// ============================================================================

defined('MOODLE_INTERNAL') || die();

// ---------------------------------------------------------------------------
// Metadatos del plugin
// ---------------------------------------------------------------------------
$string['pluginname']               = 'QuickCode IA';
$string['pluginadministration']     = 'Administración de QuickCode IA';

// ---------------------------------------------------------------------------
// Capacidades (como se muestran en el editor de roles)
// ---------------------------------------------------------------------------
$string['quickcodeia:addinstance']      = 'Agregar una nueva actividad QuickCode IA';
$string['quickcodeia:view']             = 'Ver la actividad QuickCode IA';
$string['quickcodeia:viewsubmissions']  = 'Ver entregas de QuickCode IA';

// ---------------------------------------------------------------------------
// Nombres del módulo y ayuda contextual
// ---------------------------------------------------------------------------
$string['modulename']        = 'QuickCode IA';
$string['modulenameplural']  = 'Actividades QuickCode IA';
$string['modulename_help']   = 'Este módulo permite a los docentes crear ejercicios de programación enriquecidos con pistas de IA y una consola interactiva.';

// ---------------------------------------------------------------------------
// Campos del formulario y configuración
// ---------------------------------------------------------------------------
$string['quickcodeianame']        = 'Nombre del ejercicio';
$string['quickcodeianame_help']   = 'Nombre de esta instancia del ejercicio de programación.';

$string['quickcodeiasettings']    = 'Ajustes de QuickCode IA';
$string['quickcodeiafieldset']    = 'Configuración del ejercicio';

$string['statement']       = 'Enunciado del ejercicio';
$string['statement_help']  = 'Descripción o instrucciones principales del ejercicio que verán los estudiantes.';

$string['solutioncode']       = 'Código de solución';
$string['solutioncode_help']  = 'Código de solución de ejemplo para el ejercicio, oculto a los estudiantes.';

$string['language']        = 'Lenguaje de programación';
$string['language_help']   = 'Selecciona el lenguaje de programación para el ejercicio.';

$string['duedate']         = 'Fecha de entrega';
$string['duedate_help']    = 'Fecha límite en la que el estudiante debe completar el ejercicio.';

$string['enablehints']      = 'Permitir pistas de IA';
$string['enablehints_help'] = 'Si está activado, los estudiantes podrán solicitar pistas generadas por IA mientras resuelven el ejercicio.';

$string['enableconsole']      = 'Permitir consola de ayuda con IA';
$string['enableconsole_help'] = 'Si está activado, los estudiantes tendrán acceso a una consola de ayuda con IA durante el ejercicio.';

$string['maxhints']        = 'Número máximo de pistas';
$string['maxhints_help']   = 'Cantidad máxima de pistas que un estudiante puede utilizar para este ejercicio.';

// ---------------------------------------------------------------------------
// Misceláneos
// ---------------------------------------------------------------------------
$string['openaihelp'] = 'Ayuda de consola con OpenAI';
$string['event:course_module_viewed'] = 'QuickCode IA visualizado';

// ---------------------------------------------------------------------------
// Entregas (vista del profesor)
// ---------------------------------------------------------------------------
$string['viewsubmissions']     = 'Ver entregas';
$string['lastmodified']        = 'Última modificación';
$string['score']               = 'Puntuación';
$string['view']                = 'Ver';
$string['nousers']             = 'No hay usuarios inscritos que puedan enviar esta actividad.';
$string['currentstatus']       = 'Estado actual';

$string['corrected']           = 'Corregido';
$string['submitted']           = 'Enviado';
$string['solving']             = 'Resolviendo';

$string['submissiondeleted']   = 'Entrega eliminada correctamente.';
$string['deletesubmission']    = 'Eliminar';
