<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * All the steps to restore mod_quickcodeia are defined here.
 *
 * @package     mod_quickcodeia
 * @category    backup
 * @copyright   QuickCodeIA
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// More information about the backup process: {@link https://docs.moodle.org/dev/Backup_API}.
// More information about the restore process: {@link https://docs.moodle.org/dev/Restore_API}.

/**
 * Defines the structure step to restore one mod_quickcodeia activity.
 */
class restore_quickcodeia_activity_structure_step extends restore_activity_structure_step {

    /**
     * Defines the structure to be restored.
     *
     * @return restore_path_element[].
     */
    protected function define_structure() {
        $paths = [];
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('quickcodeia', '/quickcodeia');

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Processes the quickcodeia restore data.
     *
     * @param array $data Parsed element data.
     */
    protected function process_quickcodeia($data) {
        return;
    }

    /**
     * Defines post-execution actions.
     */
    protected function after_execute() {
        return;
    }
}
