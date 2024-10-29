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
 * User report generate form local_user_report.
 *
 * @package    local_user_report
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_book_seat\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');

class book_seat_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG;
        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('text', 'username', 'Username');
        $mform->setType('username', PARAM_RAW);
        $mform->addRule('username', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'email', 'Email');
        $mform->setType('email', PARAM_RAW);
        $mform->addRule('email', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'phone', 'Phone Number');
        $mform->setType('phone', PARAM_RAW);
        $mform->addRule('phone', get_string('required'), 'required', null, 'client');

        $this->add_action_buttons();
    }



    // Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}