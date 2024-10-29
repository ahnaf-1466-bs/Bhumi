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
 * @package    local_book_seat
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$functions = array(
    'local_book_seat_store_userinfo' => array(
        'classname'     => 'local_book_seat_external',
        'methodname'    => 'store_userinfo',
        'classpath'     => 'local/book_seat/externallib.php',
        'description'   => 'store seat booking information',
        'type'          => 'write',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),

    'local_book_seat_get_book_seat_info' => array(
        'classname'     => 'local_book_seat_external',
        'methodname'    => 'get_book_seat_info',
        'classpath'     => 'local/book_seat/externallib.php',
        'description'   => 'get seat booking information',
        'type'          => 'read',
        'ajax'          => true,
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);

