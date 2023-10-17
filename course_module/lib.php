<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   block_course_module
 * @copyright 2020, Succeed Technologies <platforms@succeedtech.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/lib/completionlib.php');

/**
 * This function is used to get the course module with completion status of the user.
 * @author Arun B S.
 * @param $course_id int.
 */
function get_activity_data($course_id) {

    global $DB, $USER;

    $sql = "SELECT cm.id,
        m.name,
        cm.instance,
        FROM_UNIXTIME(cm.added, '%d %b %Y') AS date_created
        FROM {course_modules} cm
        JOIN {modules} m ON m.id = cm.module
        WHERE cm.course = :course";

    $course_modules = array_values(
        $DB->get_records_sql(
            $sql,
            array(
                'course' => $course_id
            )
        )
    );

    foreach ($course_modules as $module) {
        $data = $DB->get_record($module->name, array('id' => $module->instance));
        $module->module_name = $data->name;
        $completionInfo = new completion_info($course_id);

        $isCompleted = $completionInfo->get_completion_data(
            $module->id,
            $USER->id,
            array()
        );

        $module->completed = $isCompleted['completionstate'];
    }

    return $course_modules;
}
