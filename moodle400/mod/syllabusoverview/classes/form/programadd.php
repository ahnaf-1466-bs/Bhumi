<?php

namespace mod_syllabusoverview\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');
class programadd extends moodleform
{
    public function definition()
    {
        global $CFG, $DB;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('textarea', 'name', 'Title');
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        $mform->addElement('textarea', 'name_bangla', 'Title (in bangla)');
        $mform->setType('name_bangla', PARAM_NOTAGS);

        $mform->addElement('textarea', 'value', 'Value');
        $mform->addRule('value', get_string('required'), 'required', null, 'client');

        $mform->addElement('textarea', 'value_bangla', 'Value (in bangla)');
        $mform->setType('value_bangla', PARAM_NOTAGS);

        $this->add_action_buttons();

    }
}