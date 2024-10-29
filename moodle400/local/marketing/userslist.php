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
 * Users list of a scheduled email
 *
 * @package    local_marketing
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/local/marketing/locallib.php');

global $CFG, $PAGE, $DB;

$id = required_param('id', PARAM_INT);

$PAGE->set_url(new moodle_url('/local/marketing/userslist.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_marketing'));
$PAGE->set_heading(get_string('pluginname', 'local_marketing'));

// Get batch info.
$scheduledemail = $DB->get_record('local_marketing_emails', array('id' => $id));
// Get users list by batchid.
$userslist = local_marketing_get_userslist_by_id($id);

$course = $DB->get_record('course', array('id' => $scheduledemail->course_id));

echo $OUTPUT->header();

$templatecontext = [
    'goback' => new moodle_url('/local/marketing/manage.php'),
    'users' => array_values($userslist),
    'coursefullname' => $course->fullname,
    'scheduled_time' => local_marketing_convert_timestamp_to_date($scheduledemail->scheduled_time),
    'totalusers' => count($userslist),
];

echo $OUTPUT->render_from_template('local_marketing/userslist', $templatecontext);

echo $OUTPUT->footer();
