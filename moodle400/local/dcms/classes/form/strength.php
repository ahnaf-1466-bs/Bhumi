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



class strength extends moodleform {

    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('textarea', 'strengthname', get_string('strengthname', 'local_dcms')." (English)"); // Add elements to your form
        $mform->setType('strengthname', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('strengthname', get_string('required'), 'required', null, 'client');

        $mform->addElement('textarea', 'strengthname_bn', get_string('strengthname', 'local_dcms')." (Bengali)"); // Add elements to your form
        $mform->setType('strengthname_bn', PARAM_NOTAGS);         

        $mform->addElement('textarea', 'strengthbody', get_string('strengthbody', 'local_dcms')." (English)"); // Add elements to your form
        $mform->setType('strengthbody', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('strengthbody', get_string('required'), 'required', null, 'client');

        $mform->addElement('textarea', 'strengthbody_bn', get_string('strengthbody', 'local_dcms')." (Bengali)"); // Add elements to your form
        $mform->setType('strengthbody_bn', PARAM_NOTAGS);                   //Set type of element

        $this->add_action_buttons();
    }


//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
