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
 * Edit email templates for students and admin.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use local_preregistration\form\email_template_form;

require('./../../config.php');
require_once('./lib.php');
require_once($CFG->dirroot.'/local/preregistration/classes/form/email_template_form.php');
try {
    require_login();
} catch (Exception $exception) {
    print_r($exception);
}

global $CFG, $PAGE, $DB, $USER;

// $id = optional_param('id', 0, PARAM_INT);
// $delete = optional_param('delete', 0, PARAM_INT);

$PAGE->set_url('/local/preregistration/email_templates.php');
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('email_template_settings', 'local_preregistration'));
$PAGE->set_heading(get_string('email_template_settings', 'local_preregistration'));


$actionurl = new moodle_url('/local/preregistration/email_templates.php');
$mform = new email_template_form($actionurl);

if ($mform->is_cancelled()) {
    //Back to view.php
    redirect(new moodle_url('/local/preregistration/view.php'), get_string('cancelled', 'local_preregistration'));
} else if ($fromform = $mform->get_data()) {

    set_config('subject_student', $fromform->subject_student, 'local_preregistration');
    set_config('emailbody_student', $fromform->emailbody_student, 'local_preregistration');
    set_config('subject_admin', $fromform->subject_admin, 'local_preregistration');
    set_config('emailbody_admin', $fromform->emailbody_admin, 'local_preregistration');
    
    redirect(new moodle_url('/local/preregistration/view.php'), get_string('update_message', 'local_preregistration'));   
}

// Set form data if any;
$formdata = new stdClass();

$formdata->subject_student = get_config('local_preregistration', 'subject_student');
$formdata->emailbody_student = get_config('local_preregistration', 'emailbody_student');
$formdata->subject_admin = get_config('local_preregistration', 'subject_admin');
$formdata->emailbody_admin = get_config('local_preregistration', 'emailbody_admin');

$mform->set_data($formdata);

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
