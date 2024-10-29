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
 * External services.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die;

$functions = array(
    'local_preregistration_get_batch_data_by_courseid' => array(
        'classname'     => 'local_preregistration_external',
        'methodname'    => 'get_batch_data_by_courseid',
        'classpath'     => 'local/preregistration/classes/external.php',
        'description'   => 'Get the active batch data for a course',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'local_preregistration_add_user_to_batch' => array(
        'classname'     => 'local_preregistration_external',
        'methodname'    => 'add_user_to_batch',
        'classpath'     => 'local/preregistration/classes/external.php',
        'description'   => 'Add user to a batch',
        'type'          => 'write',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);

