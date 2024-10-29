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



class founder extends moodleform {

    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('filemanager', 'draftid', get_string('draftid', 'local_dcms')); // Add elements to your form
        $mform->addRule('draftid', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'foundername', get_string('foundername', 'local_dcms')); // Add elements to your form
        $mform->setType('foundername', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('foundername', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'foundername_bn', get_string('foundername', 'local_dcms')." (Bengali)"); // Add elements to your form
        $mform->setType('foundername_bn', PARAM_NOTAGS); 

        $mform->addElement('text', 'founderdeg', get_string('founderdeg', 'local_dcms')); // Add elements to your form
        $mform->setType('founderdeg', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('founderdeg', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'founderdeg_bn', get_string('founderdeg', 'local_dcms')." (Bengali)"); // Add elements to your form
        $mform->setType('founderdeg_bn', PARAM_NOTAGS);         

        $mform->addElement('text', 'email', get_string('email', 'local_dcms')); // Add elements to your form
        $mform->setType('email', PARAM_TEXT);                   //Set type of element

        $mform->addElement('text', 'tier', get_string('tier', 'local_dcms'));   // Add elements to your form
        $mform->setType('tier', PARAM_INT); // Set type of element
        $mform->setDefault('tier', '1');    // Set default value.

        $this->add_action_buttons();
    }


//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
