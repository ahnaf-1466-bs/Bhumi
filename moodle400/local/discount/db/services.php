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
 * discount external functions and service definitions.
 *
 * @package    local_discount
 * @copyright  2023 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'local_discount_verify_coupon' => array(
        'classname'     => 'local_discount_external',
        'methodname'    => 'verify_coupon',
        'classpath'     => 'local/discount/classes/external.php',
        'description'   => 'Get the coupon info according to user and course',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'local_discount_get_expire_time_by_courseid' => array(
        'classname'     => 'local_discount_external',
        'methodname'    => 'get_expire_time_by_courseid',
        'classpath'     => 'local/discount/classes/external.php',
        'description'   => 'Get the latest coupon expiration time by courseid',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'local_discount_find_coupon_by_userid' => array(
        'classname'     => 'local_discount_external',
        'methodname'    => 'find_coupon_by_userid',
        'classpath'     => 'local/discount/classes/external.php',
        'description'   => 'Get the coupon already used or public coupon for the user with courseid',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);
