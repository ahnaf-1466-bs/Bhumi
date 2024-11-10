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
 * Private syllabusoverview module utility functions
 *
 * @package mod_syllabusoverview
 * @copyright 2021 Brain Station 23 LTD.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/filelib.php");
require_once("$CFG->libdir/resourcelib.php");
require_once("$CFG->dirroot/mod/syllabusoverview/lib.php");


/**
 * File browsing support class
 */
class syllabusoverview_content_file_info extends file_info_stored {
    public function get_parent() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->browser->get_file_info($this->context);
        }
        return parent::get_parent();
    }
    public function get_visible_name() {
        if ($this->lf->get_filepath() === '/' and $this->lf->get_filename() === '.') {
            return $this->topvisiblename;
        }
        return parent::get_visible_name();
    }
}

function syllabusoverview_get_editor_options($context) {
    global $CFG;
    return array('subdirs'=>1, 'maxbytes'=>$CFG->maxbytes, 'maxfiles'=>-1, 'changeformat'=>1, 'context'=>$context, 'noclean'=>1, 'trusttext'=>0);
}

/**
 * This function create or edit a single record.
 *
 * @param int|null $id
 * @param edit_form $mform
 * @return void
 * @throws moodle_exception
 */
function mod_syllabusoverview_edit_score(edit_form $mform, int $id = null) {
    global $DB;
    if ($mform->is_cancelled()) {
        //Back to manage.php
        redirect(new moodle_url('/mod/syllabusoverview/manage.php'));
    } else if ($fromform = $mform->get_data()) {
        // Handing the form data.
        $recordstoinsert = new stdClass();
        $recordstoinsert->team1 = $fromform->team1;
        $recordstoinsert->team2 = $fromform->team2;
        $recordstoinsert->goal1 = $fromform->goal1;
        $recordstoinsert->goal2 = $fromform->goal2;
        if ($fromform->id) {
            // Update the record.
            $recordstoinsert->id = $fromform->id;
            $DB->update_record('mod_syllabusoverview', $recordstoinsert);
            // Go back to manage page.
            redirect(new moodle_url('/mod/syllabusoverview/manage.php'), get_string('updatethanks', 'local_footballscore'));

        } else {
            // Insert the record.
            $DB->insert_record('mod_syllabusoverview', $recordstoinsert);
            // Go back to manage page.
            redirect(new moodle_url('/mod/syllabusoverview/manage.php'), get_string('insertthanks', 'local_footballscore'));
        }
    }

    /**
     * This function init the edit_form class and return the object.
     *
     * @param int|null $id
     * @return edit_form
     * @throws dml_exception
     */
    function mod_syllabusoverview_init_form(int $id = null): edit_form {
        global $DB;

        $actionurl = new moodle_url('/mod/syllabusoverview/feature.php');

        if ($id) {
            $score = $DB->get_record('mod_syllabusoverview', array('id' => $id));
            $mform = new edit_form($actionurl, $score);
        } else {
            $mform = new edit_form($actionurl);
        }
        return $mform;
    }

}
