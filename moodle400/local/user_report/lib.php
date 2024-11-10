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
 * Library functions for local_user_report.
 *
 * @package    local_user_report
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

/**
 * Return the list of all courses with id and fullname for entry creation form options.
 * 
 * @return array $courses
 */
function local_user_report_get_all_courses() {
    global $DB;

    $courses = $DB->get_records('course', array(), '', 'id, fullname');
    return $courses;
}


/**
 * Return the list of all users with filters and course id and page title.
 * 
 * @return array $users
 */
function local_user_report_get_filtered_users($courseid, $userfilter) {
    global $DB;
    $query = "SELECT u.id, u.username, u.firstname, u.lastname, u.email
	            FROM {user} u";

    if($courseid == -1) {
        if($userfilter == 0) {
            $query .= " WHERE u.id!=1";
        }
        if($userfilter != 0) {
            $query .= " WHERE id IN (
                SELECT userid
                FROM {role_assignments}
                WHERE roleid = ";
        }
        if ($userfilter == 1) {
            // Only students of the whole site.
            $query .= " 5)";

        } else if ($userfilter == 2) {
            // Only teacher of the whole site.
            $query .= " 3)";
        }
    } else {
        // All students of a course.
        $query .= " JOIN {role_assignments} ra ON u.id = ra.userid
            JOIN {context} ctx ON ra.contextid = ctx.id
            JOIN {course} c ON ctx.instanceid = c.id
            WHERE c.id = $courseid ";
        if($userfilter == 1) {
            // Only students of a course
            $query .= " AND ra.roleid = 5";
            
        } else if ($userfilter == 2) {
            // Only teachers of a course.
            $query .= " AND ra.roleid = 3";
        } 
    }

    $users = $DB->get_records_sql($query);
    return $users;
}