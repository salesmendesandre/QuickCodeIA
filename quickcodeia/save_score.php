<?php
// ============================================================================
//  QuickCode IA – Guardar calificación de un intento
//  mod/quickcodeia/save_score.php
// ============================================================================

require(__DIR__ . '/../../config.php');
require_login();

header('Content-Type: application/json');

// ---------------------------------------------------------------------------
// Validar método HTTP
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

// ---------------------------------------------------------------------------
// Leer y validar el cuerpo de la petición
// ---------------------------------------------------------------------------
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['score']) || !is_numeric($data['score']) || !isset($data['cmid'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos inválidos']);
    exit;
}

$cmid  = (int)$data['cmid'];
$score = (float)$data['score'];

// ---------------------------------------------------------------------------
// Obtener información del módulo y verificar permisos
// ---------------------------------------------------------------------------
$cm         = get_coursemodule_from_id('quickcodeia', $cmid, 0, false, MUST_EXIST);
$quickcodeia = $DB->get_record('quickcodeia', ['id' => $cm->instance], '*', MUST_EXIST);
$context    = context_module::instance($cm->id);

require_capability('mod/quickcodeia:view', $context);

// ---------------------------------------------------------------------------
// Determinar el usuario al que se aplicará la nota
// ---------------------------------------------------------------------------
$userid = $USER->id;

if (isset($data['userid']) && is_numeric($data['userid'])) {
    $requestedid = (int)$data['userid'];

    // Solo se permite calificar a otros si se tiene el permiso adecuado
    if (has_capability('mod/quickcodeia:grade', $context)) {
        $userid = $requestedid;
    } else if ($requestedid !== $USER->id) {
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para calificar a otros usuarios']);
        exit;
    }
}

// ---------------------------------------------------------------------------
// Aplicar la calificación usando la función del módulo
// ---------------------------------------------------------------------------
require_once($CFG->dirroot . '/mod/quickcodeia/lib.php');
quickcodeia_update_grades($quickcodeia, $userid, $score);

// ---------------------------------------------------------------------------
// Respuesta de éxito
// ---------------------------------------------------------------------------
echo json_encode([
    'status' => 'ok',
    'score'  => $score,
    'userid' => $userid
]);
