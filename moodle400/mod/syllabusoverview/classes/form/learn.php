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



class learn extends moodleform {

    public function definition() {
        $messageid = optional_param('messageid', 0, PARAM_INT);

        global $CFG, $DB;
        $mform = $this->_form; // Don't forget the underscore!
        $data = $DB->get_record('syllabusoverview_learn', array('id' => $messageid));

        if($data != null) {
            $mform->addElement
            (
                'editor',
                'learn',
                get_string('learn', 'mod_syllabusoverview'),
                null
            )->setValue(array('text' => $data->learn));

            $mform->addElement
            (
                'editor',
                'learn_bangla',
                get_string('learn_bangla', 'mod_syllabusoverview'),
                null
            )->setValue(array('text' => $data->learn_bangla));

        } else {
            $mform->addElement('editor', 'learn', get_string('learn', 'mod_syllabusoverview')); // Add elements to your form

            $mform->addElement('editor', 'learn_bangla', get_string('learn_bangla', 'mod_syllabusoverview'));
        }

        $this->add_action_buttons();
    }

//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
