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
 * Library functions for local discount plugin.
 *
 * @package    local_discount
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;


/**
 * Return the list of all courses with id and fullname for coupon creation form options.
 * 
 * @return array $courses
 */
function local_discount_get_all_courses() {
    global $DB;

    $courses = $DB->get_records('course', array(), '', 'id, fullname');
    return $courses;

}

/**
 * Generates a unique coupon code.
 * 
 * @return string $code;
 */
function local_discount_generate_coupon_code() {
    $code = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 8);
    return $code;
}


/**
 * Returns the list of all coupons.
 * 
 * @return array $coupons;
 */
function local_discount_get_all_coupons() {
    global $DB;
    $sql = 'SELECT ld.id, ld.course_id, ld.coupon_code, ld.type, 
            ld.max_use, ld.active, ld.discount_percentage, 
            ld.timecreated, ld.timemodified, ld.timeexpired, c.fullname, u.firstname
            FROM {local_discount} ld
            LEFT JOIN {course} c ON ld.course_id = c.id
            LEFT JOIN {user} u ON ld.created_by = u.id
            WHERE ld.deleted=0
            ORDER BY ld.timecreated DESC';

    $coupons = $DB->get_recordset_sql($sql);
    return $coupons;
}

/**
 * Returns the coupon record for a specific user.
 * 
 * @return object $record;
 */
function local_discount_get_coupon_info ($couponcode, $courseid) {
    global $DB;
    // var_dump($couponcode);
    $sql = "SELECT * FROM {local_discount} 
            WHERE coupon_code='". $couponcode . "' AND course_id=" . $courseid;
    $record = $DB->get_record_sql($sql);
    
    return $record;
}

/**
 * Returns the course fee.
 * 
 * @return object $record;
 */
function local_discount_get_course_fee ($courseid) {
    global $DB;
    $record = $DB->get_record('enrol', array('courseid' => $courseid, 'enrol' => 'fee'));
    
    return $record;
}
/**
 * Returns the coupon record for a specific user.
 * 
 * @return object $record;
 */
function local_discount_get_used_coupon_info($couponcode, $courseid, $userid) {
    global $DB;
    $sql = "SELECT * FROM {local_discount_used_coupon} 
            WHERE coupon_code='". $couponcode . "' AND course_id=" . $courseid . " AND used_by=" . $userid;
    $record = $DB->get_record_sql($sql);
    
    return $record;
}

/**
 * Returns the public coupon info.
 * 
 * 
 */
function local_discount_get_public_valid_coupon($courseid) {
    global $DB;
    $sql = "SELECT * FROM {local_discount} 
            WHERE course_id=$courseid AND timeexpired >" . time() . " AND active=1 AND type=0 
            ORDER BY timecreated DESC LIMIT 1";
    $record = $DB->get_record_sql($sql);
    
    return $record;
}

function local_discount_get_user_used_coupon($courseid, $userid) {
    global $DB;
    $sql = "SELECT * FROM {local_discount_used_coupon} 
            WHERE course_id=" . $courseid . " AND used_by=" . $userid;
    $record = $DB->get_record_sql($sql);
    
    return $record;
} 

function local_discount_get_used_coupon_count ($couponcode) {
    global $DB;
    $sql = "SELECT COUNT(coupon_code) as coupon_code_count FROM {local_discount_used_coupon} 
            WHERE coupon_code='". $couponcode . "'";
    $record = $DB->get_record_sql($sql);

    return $record;
}


function local_discount_insert_used_coupon ($coupondata, $enrollinfo, $courseid, $userid, $amount) {
    global $DB;
    
    $record = new stdClass();

    $record->course_id = $courseid;
    $record->coupon_code = $coupondata->coupon_code;
    $record->used_by = $userid;
    $record->used_at = time();
    $record->type = $coupondata->type;
    $record->discount_percentage = $coupondata->discount_percentage;
    $record->amount = $amount;
    $record->currency = $enrollinfo->currency;
    $record->is_enrolled = 0;
    $record->timemodified = time();

    return $DB->insert_record('local_discount_used_coupon', $record);
}

function local_discount_update_used_coupon($usedcouponinfo, $coupondata, $enrollinfo, $courseid, $userid, $amount) {
    global $DB;

    $record = new stdClass();

    $record->id = $usedcouponinfo->id;
    $record->course_id = $courseid;
    $record->coupon_code = $coupondata->coupon_code;
    $record->used_by = $userid;
    $record->used_at = time();
    $record->type = $coupondata->type;
    $record->discount_percentage = $coupondata->discount_percentage;
    $record->amount = $amount;
    $record->currency = $enrollinfo->currency;
    $record->is_enrolled = 0;
    $record->timemodified = time();

    return $DB->update_record('local_discount_used_coupon', $record);

}