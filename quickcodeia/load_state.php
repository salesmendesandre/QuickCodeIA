<?php
// ============================================================================
//  QuickCode IA – Endpoint AJAX para recuperar el estado guardado de un alumno
//  mod/quickcodeia/load_state.php
// ============================================================================

define('NO_OUTPUT_BUFFERING', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/externallib.php'); // Para clean_param_json()

header('Content-Type: application/json; charset=utf-8');

// ---------------------------------------------------------------------------
// 1. Parámetros
// ---------------------------------------------------------------------------
$cmid   = required_param('cmid', PARAM_INT);             // ID del módulo
$userid = optional_param('userid', $USER->id, PARAM_INT); // ID del alumno (por defecto, el actual)

// ---------------------------------------------------------------------------
// 2. Obtener registros y contexto
// ---------------------------------------------------------------------------
$cm      = get_coursemodule_from_id('quickcodeia', $cmid, 0, false, MUST_EXIST);
$course  = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$context = context_module::instance($cm->id);

require_login($course, false, $cm);

// ---------------------------------------------------------------------------
// 3. Verificación de permisos
//    - Los estudiantes solo pueden ver su propio estado (capacidad: view)
//    - Profesores pueden ver el de cualquiera (capacidad: viewsubmissions)
// ---------------------------------------------------------------------------
if ($userid != $USER->id) {
    require_capability('mod/quickcodeia:viewsubmissions', $context);
} else {
    require_capability('mod/quickcodeia:view', $context);
}

// ---------------------------------------------------------------------------
// 4. Obtener el estado desde la base de datos
// ---------------------------------------------------------------------------
$state = $DB->get_record('quickcodeia_state', [
    'userid' => $userid,
    'cmid'   => $cmid,
]);

if (!$state) {
    echo json_encode(['status' => 'empty']);
    exit;
}

// ---------------------------------------------------------------------------
// 5. Obtener la calificación desde el libro de calificaciones
// ---------------------------------------------------------------------------
require_once($CFG->libdir . '/gradelib.php');

$grade_item = grade_item::fetch([
    'itemtype'     => 'mod',
    'itemmodule'   => 'quickcodeia',
    'iteminstance' => $cm->instance,
    'courseid'     => $cm->course
]);

$grade = $grade_item ? $grade_item->get_grade($userid, true) : null;

// ---------------------------------------------------------------------------
// 6. Construir respuesta JSON
// ---------------------------------------------------------------------------
$response = [
    'status'          => 'ok',
    'cmid'            => $cmid,
    'userId'          => $userid,
    'code'            => $state->code ?? '',
    'terminalOutput'  => $state->terminal_output ?? '',
    'currentStatus'   => $state->current_status ?? '',
    'executionCount'  => (int) ($state->execution_count ?? 0),
    'hintsUsed'       => (int) ($state->hints_used ?? 0),
    'consoleCalls'    => (int) ($state->console_calls ?? 0),
    'chatState'       => $state->chat_state ? json_decode($state->chat_state, true) : null,
    'teacherFeedback' => $state->teacher_feedback ?? '',
    'autoScore'       => is_null($state->autoscore) ? null : (float) $state->autoscore,
    'feedback'        => $state->teacher_feedback ?? '',
    'score'           => is_object($grade) && isset($grade->finalgrade) ? (float) $grade->finalgrade : null,
];

// ---------------------------------------------------------------------------
// 7. Enviar respuesta
// ---------------------------------------------------------------------------
echo json_encode($response, JSON_UNESCAPED_UNICODE);
