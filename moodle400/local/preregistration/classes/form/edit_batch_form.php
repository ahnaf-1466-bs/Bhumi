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
 * Create batch form.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_preregistration\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');

class edit_batch_form extends moodleform {

    //Add elements to form
    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        // Name of the batch
        $mform->addElement('text', 'name', get_string('name', 'local_preregistration'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        // Description of the batch
        $mform->addElement('text', 'description', get_string('description', 'local_preregistration'), 'size="50"');
        $mform->setType('description', PARAM_TEXT);

        // Select course from select dropdown.

        // Get dynamic data for all courses
        $options_data = local_preregistration_get_all_courses();

        $options = array();
        foreach($options_data as $data) {
            if($data->id != 1) {  
                $options[$data->id] = $data->fullname;
            }
        }
        $mform->addElement('select', 'course_id', get_string('select_a_course', 'local_preregistration'), $options);
        $mform->addRule('course_id', get_string('required'), 'required', null, 'client');

        // Start Date for pre registration - Date field.
        $mform->addElement('date_selector', 'startdate', get_string('startdate', 'local_preregistration'));
        $mform->addRule('startdate', get_string('required'), 'required', null, 'client');

        // End Date for pre registration - Date field.
        $mform->addElement('date_selector', 'enddate', get_string('enddate', 'local_preregistration'));
        $mform->addRule('enddate', get_string('required'), 'required', null, 'client');

        // Active status of the batch.
        $activeoptions = array(
            1 => 'Active',
            0 => 'Inactive'
        );
        $mform->addElement('select', 'active', get_string('active', 'local_preregistration'), $activeoptions);
        $mform->addRule('active', get_string('required'), 'required', null, 'client');

        $this->add_action_buttons();
    }


    // Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}