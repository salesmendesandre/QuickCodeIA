<?php
// ============================================================================
//  QuickCode IA – Vista del estudiante (carga el iframe interactivo)
//  mod/quickcodeia/view.php
// ============================================================================

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// ---------------------------------------------------------------------------
// 1. Parámetros de entrada
//    Se puede acceder vía ID de módulo (?id=) o ID de instancia (?q=)
// ---------------------------------------------------------------------------
$id = optional_param('id', 0, PARAM_INT); // course module ID (cmid)
$q  = optional_param('q',  0, PARAM_INT); // instancia directa del módulo

if ($id) {
    // Llamada con /mod/quickcodeia/view.php?id=CMID
    $cm             = get_coursemodule_from_id('quickcodeia', $id, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('quickcodeia', ['id' => $cm->instance], '*', MUST_EXIST);
} else {
    // Llamada con /mod/quickcodeia/view.php?q=INSTANCEID
    $moduleinstance = $DB->get_record('quickcodeia', ['id' => $q], '*', MUST_EXIST);
    $course         = $DB->get_record('course', ['id' => $moduleinstance->course], '*', MUST_EXIST);
    $cm             = get_coursemodule_from_instance('quickcodeia', $moduleinstance->id, $course->id, false, MUST_EXIST);
}

// ---------------------------------------------------------------------------
// 2. Control de acceso
// ---------------------------------------------------------------------------
require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// ---------------------------------------------------------------------------
// 3. Registrar evento de visualización (para estadísticas de Moodle)
// ---------------------------------------------------------------------------
$event = \mod_quickcodeia\event\course_module_viewed::create([
    'objectid' => $moduleinstance->id,
    'context'  => $context,
]);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('quickcodeia', $moduleinstance);
$event->trigger();

// ---------------------------------------------------------------------------
// 4. Configuración de la página
// ---------------------------------------------------------------------------
$PAGE->set_url('/mod/quickcodeia/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

// Mostrar fecha de entrega si está definida
if ($moduleinstance->duedate) {
    echo $OUTPUT->box(
        get_string('duedate', 'quickcodeia') . ': ' . userdate($moduleinstance->duedate),
        'generalbox'
    );
}

// ---------------------------------------------------------------------------
// 5. Mostrar el iframe con el entorno de programación
// ---------------------------------------------------------------------------
//$iframeurl = new moodle_url('http://localhost:5173/');
$iframeurl = new moodle_url('/mod/quickcodeia/iframe/index.html'); // Producción

echo html_writer::tag('iframe', '', [
    'src'             => $iframeurl,
    'width'           => '100%',
    'height'          => '700',
    'id'              => 'quickcodeframe',
    'style'           => 'border:none; overflow:hidden;',
    'allowfullscreen' => 'true',       // Atributo legacy
    'allow'           => 'fullscreen', // Permiso moderno
    'scrolling'       => 'no',
]);

// ---------------------------------------------------------------------------
// 6. Enviar configuración inicial al iframe mediante JS
// ---------------------------------------------------------------------------
$payload = [
    'event'          => 'init',
    'cmId'           => $cm->id,
    'userId'         => $USER->id,
    'viewMode'       => 'student',
    'statement'      => $moduleinstance->statement,
    'language'       => $moduleinstance->language,
    'dueDate'        => $moduleinstance->duedate,
    'enableHints'    => $moduleinstance->enablehints,
    'maxHints'       => $moduleinstance->maxhints,
    'enableConsole'  => $moduleinstance->enableconsole,
];

echo html_writer::script(
    'const moodleQuickcodePayload = ' . json_encode($payload, JSON_UNESCAPED_UNICODE) . ';'
);

// ---------------------------------------------------------------------------
// 7. Cargar el archivo JS que maneja comunicación con el iframe
// ---------------------------------------------------------------------------
$viewjs = new moodle_url('/mod/quickcodeia/view.js');
echo html_writer::script('', $viewjs);

// ---------------------------------------------------------------------------
// 8. Pie de página
// ---------------------------------------------------------------------------
echo $OUTPUT->footer();
