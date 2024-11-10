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


class syllabus extends moodleform {
    public function definition() {
        global $DB;
        $mform = $this->_form; // Don't forget the underscore!
        $messageid = optional_param('messageid', 0, PARAM_INT);
        $data = $DB->get_record('syllabusoverview_syllabus', array('id' => $messageid));

        if($data != null) {
            $mform->addElement('text', 'heading', get_string('heading', 'mod_syllabusoverview')); // Add elements to your form
            $mform->setType('heading', PARAM_NOTAGS);

            $mform->addElement('text', 'heading_bangla', get_string('heading_bangla', 'mod_syllabusoverview')); // Add elements to your form
            $mform->setType('heading_bangla', PARAM_NOTAGS);

            $mform->addElement
            (
                'editor',
                'body',
                get_string('body', 'mod_syllabusoverview'),
                null
            )->setValue(array('text' => $data->body));

            $mform->addElement
            (
                'editor',
                'body_bangla',
                get_string('body_bangla', 'mod_syllabusoverview'),
                null
            )->setValue(array('text' => $data->body_bangla));

        } else {
            $mform->addElement('text', 'heading', get_string('heading', 'mod_syllabusoverview')); // Add elements to your form
            $mform->setType('heading', PARAM_NOTAGS);                   //Set type of element
//            $mform->addRule('heading', get_string('required'), 'required', null, 'client');

            $mform->addElement('text', 'heading_bangla', get_string('heading_bangla', 'mod_syllabusoverview')); // Add elements to your form
            $mform->setType('heading_bangla', PARAM_NOTAGS);

            $mform->addElement('editor', 'body', get_string('body', 'mod_syllabusoverview')); // Add elements to your form

            $mform->addElement('editor', 'body_bangla', get_string('body_bangla', 'mod_syllabusoverview'));
        }

        $this->add_action_buttons();
    }

//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
