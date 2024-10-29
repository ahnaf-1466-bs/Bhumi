<?php
//moodleform is defined in formslib.php
require_once("$CFG->libdir/formslib.php");

class edit_question extends moodleform
{
    //Add elements to form
    public function definition()
    {
        global $CFG;
       
        $mform = $this->_form; // Don't forget the underscore!
        // $mform->addElement('hidden','id');


        $mform->addElement("text", 'question', get_string("question","mod_coursefeedback"),array("placeholder" => get_string("question_text","mod_coursefeedback"), 'size' => '75', 'maxlength' => '200'));
        $mform->setType('question', PARAM_TEXT);
        $mform->addRule('question', get_string("question_field_required","mod_coursefeedback"), 'required', null, 'server');

        // Type (Course/Instructor)
        $typeoptions = array(
            'course' => 'Course',
            'instructor' => 'Instructor',
        );
        $mform->addElement('select', 'type', 'Type of question', $typeoptions);
        $mform->setType('type', PARAM_TEXT);
        $mform->addRule('type', get_string('required'), 'required', null, 'client');

        $this->add_action_buttons();
    }
    //Custom validation should be added here
    function validation($data, $files)
    {
        return array();
    }
}
