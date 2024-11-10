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

class generate_report_form extends moodleform {


    //Add elements to form
    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        // Select course from select dropdown.

        // Get dynamic data for all courses
        $options_data = local_acc_report_get_all_courses();

        $options = array();
        $options[-1] = "All Courses";
        foreach($options_data as $data) {
            if($data->id != 1) {  
                $options[$data->id] = $data->fullname;
            }
        }
        // Course select
        $mform->addElement('select', 'courseid', get_string('select_a_course', 'local_acc_report'), $options);
        $mform->setType('courseid', PARAM_INT);

        // Date select field (From).
        $mform->addElement('date_time_selector', 'from', get_string('from', 'local_acc_report'));
        $mform->setType('from', PARAM_INT);
        $mform->setDefault('from', time() - (60 * 60 * 24) * 30); // 30 days before.

        // Date select field (To).
        $mform->addElement('date_time_selector', 'to', get_string('to', 'local_acc_report'));
        $mform->setType('to', PARAM_INT);

        // Show every entry for this course.
        $mform->addElement('advcheckbox','showall', get_string('show_all', 'local_acc_report'));
        $mform->setType('showall', PARAM_BOOL);
        $mform->setDefault('showall', 0);

        $mform->disabledIf('from', 'showall', 'checked');
        $mform->disabledIf('to', 'showall', 'checked');


        $this->add_action_buttons(false, get_string('generate_report_btn', 'local_acc_report'));
    }



    // Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}