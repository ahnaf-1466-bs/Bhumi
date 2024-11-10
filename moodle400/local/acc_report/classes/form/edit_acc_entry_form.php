<?php
// This file is part of Moodle - http://moodle.org/
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
 * Create accounting entry form.
 *
 * @package    local_acc_report
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_acc_report\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');

class edit_acc_entry_form extends moodleform {


    //Add elements to form
    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        // Select course from select dropdown.

        // Get dynamic data for all courses
        $options_data = local_acc_report_get_all_courses();

        $options = array();
        foreach($options_data as $data) {
            if($data->id != 1) {  
                $options[$data->id] = $data->fullname;
            }
        }
        // Course select
        $mform->addElement('select', 'courseid', get_string('select_a_course', 'local_acc_report'), $options);
        $mform->addRule('courseid', get_string('required'), 'required', null, 'client');
        $mform->setType('courseid', PARAM_INT);

        $types = array(
            'expense' => get_string('expense', 'local_acc_report'),
            'income' => get_string('income', 'local_acc_report')
        );
        // Select type from select dropdown.
        $mform->addElement('select', 'type', get_string('select_type', 'local_acc_report'), $types);
        $mform->addRule('type', get_string('required'), 'required', null, 'client');
        $mform->setType('type', PARAM_TEXT);

        // Amount field.
        $mform->addElement('text', 'amount', get_string('amount', 'local_acc_report'));
        $mform->setType('amount', PARAM_INT);
        $mform->addRule('amount', get_string('required'), 'required', null, 'client');
        
        // To Do: Need to make this a select option.
        // Currency.
        $currencies = local_acc_report_get_possible_currencies();
        $mform->addElement('select', 'currency', get_string('currency', 'local_acc_report'), $currencies);
        $mform->setType('currency', PARAM_TEXT);
        $mform->setDefault('currency', 'BDT');
        $mform->addRule('currency', get_string('required'), 'required', null, 'client');

        // Comment.
        $mform->addElement('text', 'comment', get_string('comment', 'local_acc_report'));
        $mform->setType('comment', PARAM_TEXT);
        $mform->addRule('comment', get_string('required'), 'required', null, 'client');

        $this->add_action_buttons();
    }


    // Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}