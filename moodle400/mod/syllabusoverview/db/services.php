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
 * syllabusoverview external functions and service definitions.
 *
 * @package    mod_syllabusoverview
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.0
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(

    'mod_syllabusoverview_view_syllabusoverview' => array(
        'classname'     => 'mod_syllabusoverview_external',
        'methodname'    => 'view_syllabusoverview',
        'description'   => 'Simulate the view.php web interface page: trigger events, completion, etc...',
        'type'          => 'write',
        'capabilities'  => 'mod/syllabusoverview:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'mod_syllabusoverview_get_details_by_courseid' => array(
        'classname'     => 'mod_syllabusoverview_external',
        'methodname'    => 'get_syllabusoverview_by_courseid',
        'description'   => 'Returns a list course details by courseid.',
        'type'          => 'read',
        'capabilities'  => 'mod/syllabusoverview:view',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE),
    ),
);
