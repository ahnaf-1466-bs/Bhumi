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

class ourstory extends moodleform {

    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('textarea', 'ourstory', get_string('ourstory', 'local_dcms')." (Bengali)"); // Add elements to your form
        $mform->addRule('ourstory', get_string('required'), 'required', null, 'client');

        $mform->addElement('textarea', 'ourstory_bn', get_string('ourstory', 'local_dcms')." (Bengali)"); // Add elements to your form

        $this->add_action_buttons();
    }

//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
