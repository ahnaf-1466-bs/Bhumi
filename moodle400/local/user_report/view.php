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
 * View page for local_user_report.
 *
 * @package    local_user_report
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_user_report\form\user_report_form;


require('../../config.php');

require_once($CFG->dirroot.'/local/user_report/lib.php');
require_once($CFG->dirroot.'/local/user_report/classes/form/user_report_form.php');

global $CFG, $PAGE, $DB, $OUTPUT, $USER;

if(!is_siteadmin($USER)) {
    return redirect(new moodle_url('/'), 'Unauthorized', null, \core\output\notification::NOTIFY_ERROR);
}

$PAGE->set_url('/local/user_report/view.php');
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('pagetitle', 'local_user_report'));
$PAGE->set_heading(get_string('pagetitle', 'local_user_report'));

$courseid = -1;     // All courses.
$userfilter = 0;    // All users.

$actionurl = new moodle_url('/local/user_report/view.php');
$mform = new user_report_form($actionurl);

if ($mform->is_cancelled()) {
    //Back to view.php
    redirect(new moodle_url('/local/user_report/view.php'), get_string('cancelled_message', 'local_user_report'));
} else if ($fromform = $mform->get_data()) {
    $courseid = $fromform->courseid;
    $userfilter = $fromform->userfilter;
}

$users = local_user_report_get_filtered_users($courseid, $userfilter);

$userslist = array();
foreach ($users as $user) {
    $temp = [];
    $temp['id'] = $user->id;
    $temp['firstname'] = $user->firstname;
    $temp['lastname'] = $user->lastname;
    $temp['email'] = $user->email;

    array_push($userslist, $temp);
}
$pageheader = 'User Report';
if ($courseid != -1) {
    $coursefullname = $DB->get_record('course', array('id' => $courseid))->fullname;
    $pageheader .= " for '$coursefullname' course";
}

if ($userfilter == 1) {
    $pageheader .= " (Only students)";
} else if ($userfilter == 2) {
    $pageheader .= " (Only teachers)";
}

echo $OUTPUT->header();

$templatecontext = [
    'page_header' => $pageheader,
    'users' => array_values($userslist),
    'usercount' => count($userslist),
    'profile_link' => new moodle_url('/user/profile.php'),
    'edit_link' => new moodle_url('/user/editadvanced.php')
];

$mform->display();
echo $OUTPUT->render_from_template('local_user_report/view', $templatecontext);
echo $OUTPUT->footer();


