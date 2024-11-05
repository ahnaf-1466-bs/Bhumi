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
 * COURSEFEEDBACK configuration form
 *
 * @package mod_coursefeedback
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/coursefeedback/locallib.php');
require_once($CFG->libdir.'/filelib.php');

class mod_coursefeedback_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;

        $mform = $this->_form;

        $config = get_config('coursefeedback');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->standard_intro_elements();

        $mform->addElement('header', 'formoptions', get_string('formoptions', 'coursefeedback'));

        $mform->addElement('selectyesno', 'iscommentrequired', get_string('iscommentrequired', 'coursefeedback'));

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();

        //-------------------------------------------------------
        // $mform->addElement('hidden', 'revision');
        // $mform->setType('revision', PARAM_INT);
        // $mform->setDefault('revision', 1);
    }

    // /**
    //  * Enforce defaults here.
    //  *
    //  * @param array $defaultvalues Form defaults
    //  * @return void
    //  **/
    // public function data_preprocessing(&$defaultvalues) {
    //     if ($this->current->instance) {
    //         $draftitemid = file_get_submitted_draft_itemid('coursefeedback');
    //         $defaultvalues['coursefeedback']['format'] = $defaultvalues['contentformat'];
    //         $defaultvalues['coursefeedback']['text']   = file_prepare_draft_area($draftitemid, $this->context->id, 'mod_coursefeedback',
    //                 'content', 0, coursefeedback_get_editor_options($this->context), $defaultvalues['content']);
    //         $defaultvalues['coursefeedback']['itemid'] = $draftitemid;
    //     }
    //     if (!empty($defaultvalues['displayoptions'])) {
    //         $displayoptions = (array) unserialize_array($defaultvalues['displayoptions']);
    //         if (isset($displayoptions['printintro'])) {
    //             $defaultvalues['printintro'] = $displayoptions['printintro'];
    //         }
    //         if (isset($displayoptions['printlastmodified'])) {
    //             $defaultvalues['printlastmodified'] = $displayoptions['printlastmodified'];
    //         }
    //         if (!empty($displayoptions['popupwidth'])) {
    //             $defaultvalues['popupwidth'] = $displayoptions['popupwidth'];
    //         }
    //         if (!empty($displayoptions['popupheight'])) {
    //             $defaultvalues['popupheight'] = $displayoptions['popupheight'];
    //         }
    //     }
    // }
}
