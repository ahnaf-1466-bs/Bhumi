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
 * Edit email templatess.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_preregistration\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');

class email_template_form extends moodleform {

    //Add elements to form
    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        // Email subject for students.
        $mform->addElement('text', 'subject_student', get_string('email_subject_student', 'local_preregistration'));
        $mform->setType('subject_student', PARAM_TEXT);
        $mform->addRule('subject_student', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('subject_student', 'subject_stu', 'local_preregistration');


        // Email body for students.
        $mform->addElement('textarea', 'emailbody_student', get_string('email_body_student', 'local_preregistration'));
        $mform->setType('emailbody_student', PARAM_TEXT);
        $mform->addRule('emailbody_student', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('emailbody_student', 'emailbody_stu', 'local_preregistration');

        // Email subject for admins.
        $mform->addElement('text', 'subject_admin', get_string('email_subject_admin', 'local_preregistration'));
        $mform->setType('subject_admin', PARAM_TEXT);
        $mform->addRule('subject_admin', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('subject_admin', 'email_sub_admin', 'local_preregistration');

        // Email body for admins.
        $mform->addElement('textarea', 'emailbody_admin', get_string('email_body_admin', 'local_preregistration'));
        $mform->setType('emailbody_admin', PARAM_TEXT);
        $mform->addRule('emailbody_admin', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('emailbody_admin', 'email_body_admins', 'local_preregistration');
  
        $this->add_action_buttons();
    }


    // Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}