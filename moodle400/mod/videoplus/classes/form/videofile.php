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
 * @package     mod_videoplus
 * @author      Brain Station 23 Ltd.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_videoplus\form;
use moodleform;
global $CFG;
require_once($CFG->libdir.'/formslib.php');
class videofile extends moodleform
{
    public function definition() {
        $mform = $this->_form;
        $html = '<div class="row">
        <div class="col">
        <strong class="text-center">Upload Video File</strong>
        </div>
        </div>';
        $mform->addElement('html', $html);

        $mform->addElement('filemanager', 'draftid', get_string('videourl', 'mod_videoplus'));
        $mform->addRule('draftid', get_string('maximumchars', '', 512), 'maxlength', 255, 'client');
        $mform->addRule('draftid', get_string('required'), 'required');


        $this->add_action_buttons();

    }

//    Custom validation should be added here
    function validation($data, $files) {
        return array();
    }
}