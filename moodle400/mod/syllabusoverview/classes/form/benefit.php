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
 * @package     mod_syllabusoverview
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabusoverview\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');



class benefit extends moodleform {

    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('hidden', 'benefit_id');
        $mform->setType('benefit_id', PARAM_INT);
        $mform->addElement('filemanager', 'content_pic', get_string('content_pic', 'mod_syllabusoverview')); // Add elements to your form
        $mform->addRule('content_pic', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addRule('content_pic', get_string('required'), 'required', null, 'client');



        $mform->addElement('text', 'content', get_string('content', 'mod_syllabusoverview')); // Add elements to your form
        $mform->setType('content', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('content', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'content_bangla', get_string('content_bangla', 'mod_syllabusoverview')); // Add elements to your form
        $mform->setType('content_bangla', PARAM_NOTAGS);


        $this->add_action_buttons();
    }


//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
