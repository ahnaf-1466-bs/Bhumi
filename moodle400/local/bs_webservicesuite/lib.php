<?php
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
 * Library of interface functions and constants for lict
 *
 * @package    local
 * @subpackage bs_webservicesuite
 * @author     Brain station 23 ltd <brainstation-23.com>
 * @copyright  2023 Brain station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Past courses of a user.
 * @param int $userid
 * @return array
 * @throws dml_exception
 */

function past_course_of_user (int $userid) {
    global $DB;

    $sql = "SELECT c.id, c.fullname, c.shortname, c.category
                FROM {user} u
                INNER JOIN {user_enrolments} ue ON ue.userid = u.id
                INNER JOIN {enrol} e ON e.id = ue.enrolid
                INNER JOIN {course} c ON c.id = e.courseid
                WHERE u.id =".$userid." 
                AND c.enddate != 0 AND c.enddate<".time()."
                ORDER BY c.enddate DESC";
    $courserecords = $DB->get_records_sql($sql, ['userid' => $userid]);

    return $courserecords;
}

/**
 * Past Courses by date.
 * @return array
 * @throws dml_exception
 */
function past_course_by_date() {
    global $DB;

    $sql = "SELECT id, fullname, shortname, summary, FROM_UNIXTIME(enddate) as coursestartdate
                FROM {course}
                WHERE enddate != 0 AND enddate<".time()."
                ORDER BY enddate DESC";
    $courserecords = $DB->get_records_sql($sql);

    return $courserecords;
}

/**
 * Future courses by date query.
 * @return array
 * @throws dml_exception
 */
function future_course_by_date() {
    global $DB;

    $sql = "SELECT id, fullname, shortname, summary, FROM_UNIXTIME(startdate) as coursestartdate
                FROM {course}
                WHERE startdate>".time()."
                ORDER BY startdate;";
    $courserecords = $DB->get_records_sql($sql);

    return $courserecords;
}
