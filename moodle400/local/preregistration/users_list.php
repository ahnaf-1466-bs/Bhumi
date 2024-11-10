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
 * Users list of a batch.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/local/preregistration/lib.php');

global $CFG, $PAGE, $DB;

$batchid = required_param('batchid', PARAM_INT);

$PAGE->set_url('/local/preregistration/users_list.php');
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_preregistration'));
$PAGE->set_heading(get_string('pluginname', 'local_preregistration'));

// Get batch info.
$batchinfo = $DB->get_record('local_preregistration_batch', array('id' => $batchid));
// Get users list by batchid.
$userslist = local_preregistration_get_userslist_by_batchid($batchid);

$course = $DB->get_record('course', array('id' => $batchinfo->courseid));

echo $OUTPUT->header();

$templatecontext = [
    'goback' => new moodle_url('/local/preregistration/view.php'),
    'users' => array_values($userslist),
    'coursefullname' => $course->fullname,
    'totalusers' => count($userslist),
];

echo $OUTPUT->render_from_template('local_preregistration/users_list', $templatecontext);

echo $OUTPUT->footer();
