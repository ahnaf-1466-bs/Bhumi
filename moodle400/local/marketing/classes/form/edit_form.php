<?php

require_once("$CFG->libdir/formslib.php");
require_once(__DIR__ . '/../../../../config.php');

class edit_form extends moodleform
{
    public function definition()
    {
        global $DB;

        $mform = $this->_form;

        $course_options = array();
        $courses = $DB->get_records('course', array(), 'fullname', 'id, fullname');

        foreach ($courses as $course) {
            $course_options[$course->id] = $course->fullname;
        }

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('select', 'course_id', get_string('studentsofcourse', 'local_marketing'), $course_options);
        $mform->setType('course_id', PARAM_INT);

        $mform->addElement('text', 'mail_subject', get_string('mailsubject', 'local_marketing'));
        $mform->setType('mail_subject', PARAM_TEXT);

        $mform->addElement('textarea', 'mail_body',  get_string('mailbody', 'local_marketing'));
        $mform->setType('mail_body', PARAM_TEXT);

        $mform->addElement('date_time_selector', 'scheduled_time',  get_string('sheduling', 'local_marketing'));
        $mform->setType('scheduled_time', PARAM_INT);

        $this->add_action_buttons(true, 'Update');
    }

    // Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

    function set_data($data) {
        $this->_form->setDefault('course_id', $data->course_id);
        $this->_form->setDefault('mail_subject', $data->mail_subject);
        $this->_form->setDefault('mail_body', $data->mail_body);
        $this->_form->setDefault('scheduled_time', $data->scheduled_time);
        $this->_form->setDefault('id', $data->id);
    }
}
