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
 * Course feedback module admin settings and defaults
 *
 * @package   mod_coursefeedback
 * @copyright 2023 Brain Station 23 Ltd.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {
    //--- general settings -----------------------------------------------------------------------------------
    $settings->add(new admin_setting_configtext('coursefeedback/courseratingtext',
        get_string('courseratingtext', 'coursefeedback'),
        get_string('courseratingtext_help', 'coursefeedback'), '', PARAM_RAW));

    $settings->add(new admin_setting_configtext('coursefeedback/coursecommenttext',
        get_string('coursecommenttext', 'coursefeedback'),
        get_string('coursecommenttext_help', 'coursefeedback'), '', PARAM_RAW));
}
