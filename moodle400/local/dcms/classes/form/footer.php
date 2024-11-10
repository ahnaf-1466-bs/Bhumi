<?php
// This file is part of Moodle Course Rollover Plugin
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
 * @package     local_dcms
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_dcms\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');

class footer extends moodleform {

    public function definition() {
        global $DB;
        $id = optional_param('id', 0, PARAM_INT);
        $mform = $this->_form; // Don't forget the underscore!
        $data = $DB->get_record('dcms_footer', array('id' => $id));

        if($data != null) {
            $mform->addElement('textarea', 'name', get_string('name', 'local_dcms'))->setValue($data->name); // Add elements to your form
            $mform->addElement('textarea', 'title', get_string('title', 'local_dcms'))->setValue($data->title); // Add elements to your form
            $mform->addElement
            (
                'editor',
                'description',
                get_string('footer', 'local_dcms'),
                null
            )->setValue(array('text' => $data->description));

            // Bengali
            $mform->addElement('textarea', 'name_bn', get_string('name_bn', 'local_dcms'))->setValue($data->name_bn); // Add elements to your form
            $mform->addElement('textarea', 'title_bn', get_string('title_bn', 'local_dcms'))->setValue($data->title_bn); // Add elements to your form

            $mform->addElement
            (
                'editor',
                'description_bn',
                get_string('description_bn', 'local_dcms'),
                null
            )->setValue(array('text' => $data->description_bn));


        } else {
            $mform->addElement('textarea', 'name', get_string('name', 'local_dcms')); // Add elements to your form
            $mform->addElement('textarea', 'title', get_string('title', 'local_dcms')); // Add elements to your form
            $mform->addElement('editor', 'description', get_string('description', 'local_dcms')); // Add elements to your form

            // Bengali Fields
            $mform->addElement('textarea', 'name_bn', get_string('name_bn', 'local_dcms')); // Add elements to your form
            $mform->addElement('textarea', 'title_bn', get_string('title_bn', 'local_dcms')); // Add elements to your form
            $mform->addElement('editor', 'description_bn', get_string('description_bn', 'local_dcms')); // Add elements to your form
        }

        $this->add_action_buttons();
    }

//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}
