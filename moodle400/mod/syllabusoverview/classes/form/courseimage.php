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
 * @package     mod_syllabusoverview
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_syllabusoverview\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');

class courseimage extends moodleform
{
    public function definition() {
        $mform = $this->_form;
        $html = '<div class="row">
        <div class="col">
        <strong class="text-center">Upload Course Image</strong>
        </div>
        </div>';
        $mform->addElement('html', $html);
        $mform->addElement('filemanager', 'course_image', 'Image', null, ['accepted_types' => array('png', 'jpg', 'jpeg')]);
        $mform->addRule('course_image', 'Please Upload Course Image', 'required');


        $this->add_action_buttons();

    }

//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}