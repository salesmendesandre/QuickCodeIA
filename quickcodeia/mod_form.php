<?php
// ============================================================================
//  QuickCode IA – Formulario de configuración del módulo
//   mod/quickcodeia/mod_form.php
// ============================================================================

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Formulario de creación/edición de actividades QuickCodeIA.
 *
 * @package     mod_quickcodeia
 * @copyright   QuickCodeIA
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 o later
 */
class mod_quickcodeia_mod_form extends moodleform_mod {

    /**
     * Define los elementos del formulario
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // -------------------------------------------------------------------
        // Sección general
        // -------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Nombre de la actividad
        $mform->addElement('text', 'name', get_string('quickcodeianame', 'mod_quickcodeia'), ['size' => '64']);
        $mform->setType('name', !empty($CFG->formatstringstriptags) ? PARAM_TEXT : PARAM_CLEANHTML);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'quickcodeianame', 'mod_quickcodeia');

        // Introducción (estándar)
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // -------------------------------------------------------------------
        // Configuración específica de QuickCodeIA
        // -------------------------------------------------------------------
        $mform->addElement('static', 'label1', 'quickcodeiasettings', get_string('quickcodeiasettings', 'mod_quickcodeia'));
        $mform->addElement('header', 'quickcodeiafieldset', get_string('quickcodeiafieldset', 'mod_quickcodeia'));

        // Enunciado del ejercicio
        $mform->addElement('editor', 'statement_editor', get_string('statement', 'mod_quickcodeia'), null);
        $mform->setType('statement_editor', PARAM_RAW);

        // Código de solución (oculto al estudiante)
        $mform->addElement('textarea', 'solutioncode', get_string('solutioncode', 'mod_quickcodeia'), 'wrap="virtual" rows="10" cols="60"');
        $mform->setType('solutioncode', PARAM_RAW);

        // Lenguaje de programación
        $mform->addElement('select', 'language',
            get_string('language', 'quickcodeia'),
            ['python' => 'Python', 'java' => 'Java', 'c' => 'C', 'verilog' => 'Verilog']
        );
        $mform->setDefault('language', 'python');
        $mform->addHelpButton('language', 'language', 'quickcodeia');

        // Fecha de entrega
        $mform->addElement('date_time_selector', 'duedate',
            get_string('duedate', 'quickcodeia'), ['optional' => true]
        );
        $mform->setDefault('duedate', 0);
        $mform->addHelpButton('duedate', 'duedate', 'quickcodeia');

        // Permitir pistas IA
        $mform->addElement('advcheckbox', 'enablehints', get_string('enablehints', 'mod_quickcodeia'));
        $mform->setDefault('enablehints', 0);

        // Permitir consola IA
        $mform->addElement('advcheckbox', 'enableconsole', get_string('enableconsole', 'mod_quickcodeia'));
        $mform->setDefault('enableconsole', 0);

        // Número máximo de pistas
        $mform->addElement('text', 'maxhints', get_string('maxhints', 'mod_quickcodeia'));
        $mform->setType('maxhints', PARAM_INT);
        $mform->setDefault('maxhints', 0);

        // Elementos estándar de módulo (visibilidad, grupos, etc.)
        $this->standard_coursemodule_elements();

        // Botones estándar (guardar/cancelar)
        $this->add_action_buttons();
    }

    /**
     * Preprocesamiento de datos al cargar la edición de una instancia
     *
     * @param array $defaultvalues Valores por defecto cargados desde la base de datos
     */
    public function data_preprocessing(&$defaultvalues) {
        if (isset($defaultvalues['statement'])) {
            $draftid = file_get_submitted_draft_itemid('statement');
            $defaultvalues['statement_editor']['text']    = $defaultvalues['statement'];
            $defaultvalues['statement_editor']['format']  = FORMAT_HTML;
            $defaultvalues['statement_editor']['itemid']  = $draftid;
        }
    }
}
