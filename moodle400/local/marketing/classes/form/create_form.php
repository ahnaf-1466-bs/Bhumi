<?php

require_once("$CFG->libdir/formslib.php");
require_once(__DIR__ . '/../../../../config.php');


class create_form extends moodleform
{
    
    public function definition()
    {
        $mform = $this->_form;

        $course_list = get_course_list();
        $course_options = array();
        foreach ($course_list as $course) {
            $course_options[$course['id']] = $course['fullname'];
        }

        $mform->addElement('select', 'course_id', get_string('studentsofcourse', 'local_marketing'), $course_options);
        $mform->setType('course_id', PARAM_INT);

        $mform->addElement('text', 'mail_subject', get_string('mailsubject', 'local_marketing'));
        $mform->setType('mail_subject', PARAM_TEXT);

        $mform->addElement('textarea', 'mail_body',  get_string('mailbody', 'local_marketing'));
        $mform->setType('mail_body', PARAM_TEXT);

        $mform->addElement('date_time_selector', 'scheduled_time',  get_string('sheduling', 'local_marketing'));
        $mform->setType('scheduled_time', PARAM_INT);

        $this->add_action_buttons(true, 'Submit');
    }

     // Custom validation should be added here
        function validation($data, $files) {
            return array();
        }
    
}
