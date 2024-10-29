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



class programpdf extends moodleform {

    public function definition() {
        global $CFG, $DB;
        $messageid = optional_param('messageid', 0, PARAM_INT);

        $mform = $this->_form; // Don't forget the underscore!

        $data = $DB->get_record('syllabusoverview_prog_pdfurl', array('id' => $messageid));

        if($data != null) {
            $mform->addElement('textarea', 'programstructure', get_string('programstructure', 'mod_syllabusoverview'), null
            )->setValue($data->programstructure);
            $mform->addRule('programstructure', get_string('required'), 'required', null, 'client');

            $mform->addElement('text', 'deadline', get_string('deadline', 'mod_syllabusoverview'), null
            )->setValue($data->deadline);
            $mform->setType('deadline', PARAM_RAW);
            $mform->addRule('deadline', get_string('required'), 'required', null, 'client');

            $mform->addElement('text', 'length', get_string('length', 'mod_syllabusoverview'), null
            )->setValue($data->length);
            $mform->setType('length', PARAM_RAW);
            $mform->addRule('length', get_string('required'), 'required', null, 'client');

            $mform->addElement('text', 'fee', get_string('fee', 'mod_syllabusoverview'), null
            )->setValue($data->fee);
            $mform->setType('fee', PARAM_RAW);
            $mform->addRule('fee', get_string('required'), 'required', null, 'client');


        }
        else {
            $mform->addElement('textarea', 'programstructure', get_string('programstructure', 'mod_syllabusoverview')); // Add elements to your form
            $mform->addRule('programstructure', get_string('required'), 'required', null, 'client');

            $mform->addElement('text', 'deadline', get_string('deadline', 'mod_syllabusoverview')); // Add elements to your form
            $mform->setType('deadline', PARAM_RAW);
            $mform->addRule('deadline', get_string('required'), 'required', null, 'client');

            $mform->addElement('text', 'length', get_string('length', 'mod_syllabusoverview')); // Add elements to your form
            $mform->setType('length', PARAM_RAW);                   //Set type of element
            $mform->addRule('length', get_string('required'), 'required', null, 'client');

            $mform->addElement('text', 'fee', get_string('fee', 'mod_syllabusoverview')); // Add elements to your form
            $mform->setType('fee', PARAM_RAW);
            $mform->addRule('fee', get_string('required'), 'required', null, 'client');
        }


        $mform->addElement('filemanager', 'programpdf', get_string('programpdf', 'mod_syllabusoverview')); // Add elements to your form
        $mform->addRule('programpdf', get_string('maximumchars', '', 512), 'maxlength', 255, 'client');
        $mform->addRule('programpdf', get_string('required'), 'required', null, 'client');

        $this->add_action_buttons();
    }


//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
