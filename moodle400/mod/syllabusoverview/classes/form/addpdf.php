<?php

namespace mod_syllabusoverview\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');

class addpdf extends moodleform
{
    public function definition()
    {
        global $CFG, $DB;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('filemanager', 'programpdf', get_string('programpdf', 'mod_syllabusoverview')); // Add elements to your form
        $mform->addRule('programpdf', get_string('maximumchars', '', 512), 'maxlength', 255, 'client');
        $mform->addRule('programpdf', get_string('required'), 'required', null, 'client');

        $this->add_action_buttons();
    }
}