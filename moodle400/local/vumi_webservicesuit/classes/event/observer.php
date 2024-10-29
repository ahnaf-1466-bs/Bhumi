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
 *
 * @category   external
 * @copyright  2022 Brain Statin 23 LTD
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

namespace local_vumi_webservicesuit\event;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot .'/local/vumi_webservicesuit/lib.php');
require_once($CFG->dirroot .'/local/vumi_webservicesuit/classes/user_enrolment.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/querylib.php');

use core\event\user_enrolment_created;
use core\task\manager;
use core_analytics\action;
use user_enrolment;

class observer {
    public static function on_user_enrolment (user_enrolment_created $event)
    {
        global $DB, $CFG;

        $event->get_data();
        $userid = $event->relateduserid;
        $courseid = $event->courseid;

        if ($DB->get_record('favourite', ['userid' => $userid, 'itemid' => $courseid])) {
            $DB->delete_records('favourite', ['userid' => $userid, 'itemid' => $courseid]);
        }
    }
}