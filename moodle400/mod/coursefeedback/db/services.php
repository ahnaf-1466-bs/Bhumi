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
 * COURSEFEEDBACK external functions and service definitions.
 *
 * @package    mod_coursefeedback
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'mod_coursefeedback_view_coursefeedback' => array(
        'classname'     => 'mod_coursefeedback_external',
        'methodname'    => 'view_coursefeedback',
        'description'   => 'Simulate the view.php web interface coursefeedback: trigger events, completion, etc...',
        'type'          => 'write',
        'capabilities'  => 'mod/coursefeedback:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_coursefeedback_get_coursefeedbacks_by_courses' => array(
        'classname'     => 'mod_coursefeedback_external',
        'methodname'    => 'get_coursefeedbacks_by_courses',
        'description'   => 'Returns a list of coursefeedbacks in a provided list of courses, if no list is provided all coursefeedbacks that the user
                            can view will be returned.',
        'type'          => 'read',
        'capabilities'  => 'mod/coursefeedback:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'mod_coursefeedback_get_coursefeedback_questions' => array(
        'classname'     => 'mod_coursefeedback_external',
        'methodname'    => 'get_coursefeedback_questions',
        'description'   => 'Returns a list of coursefeedback question for specific cmid and feedbackid.',
        'type'          => 'read',
        'capabilities'  => 'mod/coursefeedback:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),

    'mod_coursefeedback_save_feedback_responses' => array(
        'classname'     => 'mod_coursefeedback_external',
        'methodname'    => 'save_feedback_responses',
        'description'   => 'Save feedback responses of a user.',
        'type'          => 'write',
        'capabilities'  => 'mod/coursefeedback:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
);
