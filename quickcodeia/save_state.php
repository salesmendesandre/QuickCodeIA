<?php
// ============================================================================
//  QuickCode IA – Guardar el estado actual de un intento de ejercicio
//  Guardar como: mod/quickcodeia/save_state.php
// ============================================================================

require(__DIR__ . '/../../config.php');
require_login();

header('Content-Type: application/json');

// ---------------------------------------------------------------------------
// Validar que el método sea POST
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// ---------------------------------------------------------------------------
// Leer y validar el cuerpo JSON
// ---------------------------------------------------------------------------
$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Cuerpo JSON inválido']);
    exit;
}

// ---------------------------------------------------------------------------
// Validar y obtener cmid
// ---------------------------------------------------------------------------
if (!isset($data['cmid']) || !is_numeric($data['cmid'])) {
    http_response_code(400);
    echo json_encode(['error' => 'cmid es obligatorio y debe ser numérico']);
    exit;
}
$cmid = (int)$data['cmid'];

// ---------------------------------------------------------------------------
// Validar módulo y permisos
// ---------------------------------------------------------------------------
$cm      = get_coursemodule_from_id('quickcodeia', $cmid, 0, false, MUST_EXIST);
$context = context_module::instance($cmid);



// Determinar el usuario al que se le guarda el estado (el profesor puede indicar otro)
$userid = $USER->id;
if (isset($data['userid']) && is_numeric($data['userid'])) {
    if (has_capability('mod/quickcodeia:viewsubmissions', $context)) {
        $userid = (int)$data['userid'];
    }
}

// Validar permisos mínimos del usuario actual
require_capability('mod/quickcodeia:view', $context);

// ---------------------------------------------------------------------------
// Construir objeto de registro
// ---------------------------------------------------------------------------
$record = new stdClass();
$record->userid = $userid;
$record->cmid   = $cmid;

if (isset($data['code'])) {
    $record->code = $data['code'];
}
if (isset($data['terminal_output'])) {
    $record->terminal_output = $data['terminal_output'];
}
if (isset($data['execution_count'])) {
    $record->execution_count = (int)$data['execution_count'];
}
if (isset($data['hints_used'])) {
    $record->hints_used = (int)$data['hints_used'];
}
if (isset($data['console_calls'])) {
    $record->console_calls = (int)$data['console_calls'];
}
if (isset($data['chat_state'])) {
    $record->chat_state = json_encode($data['chat_state'], JSON_UNESCAPED_UNICODE);
}
if (isset($data['autoscore'])) {
    $record->autoscore = (float)$data['autoscore'];
}
if (isset($data['teacherFeedback'])) {
    $record->teacher_feedback = $data['teacherFeedback'];
}
if (isset($data['currentStatus'])) {
    $record->current_status = $data['currentStatus'];
}

$currentStatus = $data['currentStatus'] ?? null;

if ($currentStatus !== 'corrected') {
    $record->timemodified = time();
}

$module = $DB->get_record('quickcodeia', ['id' => $cm->instance], '*', MUST_EXIST);

// Solo aplicar restricción de fecha si NO es una corrección
if ($currentStatus !== 'corrected' && $module->duedate && time() > $module->duedate) {
    http_response_code(403);
    echo json_encode(['error' => 'La entrega se ha cerrado. La fecha límite ha pasado.', 'code' => 'duedate']);
    exit;
}


// ---------------------------------------------------------------------------
// Insertar o actualizar el estado en la base de datos
// ---------------------------------------------------------------------------
$exists = $DB->get_record('quickcodeia_state', [
    'userid' => $userid,
    'cmid'   => $cmid
]);

if ($exists) {
    $record->id = $exists->id;
    $record->timecreated = $exists->timecreated;
    $DB->update_record('quickcodeia_state', $record);
} else {
    $record->timecreated = time();
    $DB->insert_record('quickcodeia_state', $record);
}

// ---------------------------------------------------------------------------
// Éxito
// ---------------------------------------------------------------------------
echo json_encode(['status' => 'ok']);
