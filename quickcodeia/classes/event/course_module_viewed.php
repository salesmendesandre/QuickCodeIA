<?php
namespace mod_quickcodeia\event;

defined('MOODLE_INTERNAL') || die();

class course_module_viewed extends \core\event\base {

    public static function get_name() {
        return get_string('event:course_module_viewed', 'mod_quickcodeia');
    }

    public function get_description() {
        return "The user with id '{$this->userid}' viewed the QuickCode IA module with id '{$this->objectid}'.";
    }

    public function get_url() {
        return new \moodle_url('/mod/quickcodeia/view.php', ['id' => $this->contextinstanceid]);
    }

    public static function get_objectid_mapping() {
        return ['db' => 'quickcodeia', 'restore' => 'quickcodeia'];
    }

    public static function get_other_mapping() {
        return false;
    }

    protected function init() {
        $this->data['crud'] = 'r'; // read
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'quickcodeia';
    }
}
