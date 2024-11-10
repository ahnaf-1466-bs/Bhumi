<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     local_dcms
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dcms\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');



class feedback extends moodleform {

    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('text', 'feedbackname', get_string('feedbackname', 'local_dcms')); // Add elements to your form
        $mform->setType('feedbackname', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('feedbackname', get_string('required'), 'required', null, 'client');

        //Set type of element

        $mform->addElement('text', 'company', get_string('company', 'local_dcms')); // Add elements to your form
        $mform->setType('company', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('company', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'position', get_string('position', 'local_dcms')); // Add elements to your form
        $mform->setType('position', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('position', get_string('required'), 'required', null, 'client');


        $mform->addElement('filemanager', 'draftid', get_string('draftid', 'local_dcms')); // Add elements to your form
        $mform->addRule('draftid', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'subject', get_string('subject', 'local_dcms')); // Add elements to your form
        $mform->setType('subject', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('subject', get_string('required'), 'required', null, 'client');

        $mform->addElement('textarea', 'feedbacktext', get_string('feedbacktext', 'local_dcms')); // Add elements to your form
        $mform->setType('feedbacktext', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('feedbacktext', get_string('required'), 'required', null, 'client');

        // Bangla Form

        $mform->addElement('text', 'feedbackname_bn', get_string('feedbackname_bn', 'local_dcms')); // Add elements to your form
        $mform->setType('feedbackname', PARAM_NOTAGS);

        $mform->addElement('text', 'company_bn', get_string('company_bn', 'local_dcms')); // Add elements to your form
        $mform->setType('company', PARAM_NOTAGS);                   //Set type of element

        $mform->addElement('text', 'position_bn', get_string('position_bn', 'local_dcms')); // Add elements to your form
        $mform->setType('position', PARAM_NOTAGS);                   //Set type of element

        $mform->addElement('text', 'subject_bn', get_string('subject_bn', 'local_dcms')); // Add elements to your form
        $mform->setType('subject', PARAM_NOTAGS);                   //Set type of element

        $mform->addElement('textarea', 'feedbacktext_bn', get_string('feedbacktext_bn', 'local_dcms')); // Add elements to your form
        $mform->setType('feedbacktext', PARAM_NOTAGS);                   //Set type of element

        $this->add_action_buttons();
    }


//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
