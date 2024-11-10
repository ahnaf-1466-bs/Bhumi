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
 * Create coupon form.
 *
 * @package    local_discount
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_discount\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');

class edit_coupon_form extends moodleform {

    //Add elements to form
    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        // Select course from select dropdown.

        // Get dynamic data for all courses
        $options_data = local_discount_get_all_courses();

        $options = array();
        foreach($options_data as $data) {
            if($data->id != 1) {  
                $options[$data->id] = $data->fullname;
            }
        }
        $mform->addElement('select', 'course_id', get_string('select_a_course', 'local_discount'), $options);
        $mform->addRule('course_id', get_string('required'), 'required', null, 'client');

        // Limit
        $mform->addElement('text', 'max_use', get_string('max_users', 'local_discount'));
        $mform->setType('max_use', PARAM_INT);
        $mform->addRule('max_use', get_string('required'), 'required', null, 'client');

        // Discount percentage.
        $mform->addElement('text', 'discount_percentage', get_string('discount_percentage', 'local_discount'));
        $mform->setType('discount_percentage', PARAM_FLOAT);
        $mform->addRule('discount_percentage', get_string('required'), 'required', null, 'client');

        // Type (Public/Private).
        $coupon_options = array(
            0 => 'Public',
            1 => 'Private',
        );
        $mform->addElement('select', 'type', get_string('type', 'local_discount'), $coupon_options);
        $mform->addRule('type', get_string('required'), 'required', null, 'client');

        // Time expired - Date time field.
        $mform->addElement('date_time_selector', 'timeexpired', get_string('expiration_date', 'local_discount'));
        $mform->addRule('timeexpired', get_string('required'), 'required', null, 'client');


        $this->add_action_buttons();
    }


    // Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}