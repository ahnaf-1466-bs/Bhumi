<?php

namespace mod_syllabusoverview\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');
class addprogramdate extends moodleform
{
    public function definition()
    {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('text', 'name', 'Program Name'); // Add elements to your form
        $mform->setType('name', PARAM_NOTAGS);                   //Set type of element
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'name_bangla', 'Program Name (bangla)'); // Add elements to your form
        $mform->setType('name_bangla', PARAM_NOTAGS);

        $this->add_action_buttons();
    }
    //    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}