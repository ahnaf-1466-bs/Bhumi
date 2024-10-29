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
 * External webservice functions.
 *
 * @package    local_discount
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_recentlyaccesseditems\external;

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->libdir/externallib.php");

/**
 * local_discount_external class for API.
 *
 * @package    local_discount
 * @copyright  2023 Brain Station 23 Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class local_discount_external extends external_api {
    /**
     * Returns description of method get parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function verify_coupon_parameters() {
        return new external_function_parameters(
            array (
                'coupon_code' => new external_value(PARAM_TEXT, 'Coupon code'),
                'userid' => new external_value(PARAM_INT, 'User ID'),
                'courseid' => new external_value(PARAM_INT, 'Course ID')
            )
        );
    }

    /**
     * Verify whether the coupon is valid and inserts a record as claimed/used
     * after verification.
     * 
     * If the coupon code is valid, active and has expiration time, 
     * then insert coupon_code in db
     * 
     * If the new coupon code is entered again by the same user and course but the user is not enrolled 
     * then update the coupon code in same row
     * 
     * If the user uses same coupon code, but didn't enroll with that code,
     * then return the previously saved amount and discount percentage
     * 
     * Else the statuscode or errorcode comments are stated below in comments.
     *
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function verify_coupon($couponcode, $userid, $courseid) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/local/discount/lib.php");
        
        $enrollinfo = local_discount_get_course_fee($courseid);
        
        $amount = 0;
        if($enrollinfo) {
            $amount = $enrollinfo->cost;
        }
        
        $discount_percentage = 0;
        $status = false;
        $statuscode = 200;
        
        $coupondata = local_discount_get_coupon_info($couponcode, $courseid);
        if($coupondata && $enrollinfo) {
        
            $usedcouponinfo = local_discount_get_used_coupon_info($couponcode, $courseid, $userid);
            $userusedcoupon = local_discount_get_user_used_coupon($courseid, $userid);
            $couponusecount = local_discount_get_used_coupon_count($couponcode);
            $amount = $enrollinfo->cost;

            // If the coupon is valid and if this is a new coupon for the user and course,
            // then insert coupon data.
            if($coupondata->timeexpired > time() && $coupondata->active && !$usedcouponinfo && $couponusecount->coupon_code_count < $coupondata->max_use && !$userusedcoupon)  {
                
                // Calculate the amount after discount. 
                $amount = $enrollinfo->cost - (($enrollinfo->cost * $coupondata->discount_percentage) / 100);
                $discount_percentage = $coupondata->discount_percentage;
                // Insert the record of used coupon.
                $id = local_discount_insert_used_coupon($coupondata, $enrollinfo, $courseid, $userid, $amount);
                if($id) {
                    // Insert operation successful.
                    $status = true;
                } else {
                    $status = false;
                    $statuscode = 4010; // Insert operation failed.
                }      
                
            } else if($coupondata->timeexpired > time() && $coupondata->active && $userusedcoupon && !$usedcouponinfo && $couponusecount->coupon_code_count < $coupondata->max_use ) {
                // Handle the condition when the user comes with a new coupon code for a same course
                
                // If the user is not enrolled already in specific course, we can update the new coupon in previous record. 
                if(!$userusedcoupon->is_enrolled) {
                    // Calculate the amount after discount. 
                    $amount = $enrollinfo->cost - (($enrollinfo->cost * $coupondata->discount_percentage) / 100);
                    $discount_percentage = $coupondata->discount_percentage;
                    $status = local_discount_update_used_coupon($userusedcoupon, $coupondata, $enrollinfo, $courseid, $userid, $amount);   
                } else {
                    $statuscode = 4004;     // User is already enrolled.
                }
            } else if($coupondata->timeexpired > time() && $coupondata->active && $usedcouponinfo && $couponusecount->coupon_code_count < $coupondata->max_use) {
                // Handle if the user entered the same coupon code before but didn't enrol with this code
                if(!$usedcouponinfo->is_enrolled) {
                    if ($usedcouponinfo->amount != $enrollinfo->cost) {
                        $amount = $enrollinfo->cost - (($enrollinfo->cost * $coupondata->discount_percentage) / 100);
                        local_discount_update_used_coupon($usedcouponinfo, $coupondata, $enrollinfo, $courseid, $userid, $amount);
                        $usedcouponinfo = $DB->get_record('local_discount_used_coupon', ['coupon_code' => $couponcode, 'course_id' => $courseid, 'used_by' => $userid]);
                    }

                    $amount = $usedcouponinfo->amount;
                    $discount_percentage = $usedcouponinfo->discount_percentage;
                    $status = true;
                } else {
                    $statuscode = 4004;     // User is already enrolled.
                }
            } else {
                if($coupondata->timeexpired <= time()) {
                    $statuscode = 4002;      // Coupon is expired.
                } else if(!$coupondata->active) {
                    $statuscode = 4003;     // Coupon is not active.
                }else if($usedcouponinfo && $usedcouponinfo->is_enrolled) {
                    $statuscode = 4004;      // Coupon is used by this user and already got enrolled.
                } else if($couponusecount->coupon_code_count >= $coupondata->max_use) {
                    $statuscode = 4005;      // Coupon max use limit exceeded.
                } else {
                    $statuscode = 4007;      // Unknown failure code.
                }
            }
        } else {
            if(!$coupondata) {
                $statuscode = 4001;         // Coupon not found. 
            } else if(!$enrollinfo) {
                $statuscode = 4006;         // Ernol info not found. 
            }             
        }

        $result = [
            'amount' => $amount,
            'discount_percentage' => $discount_percentage,
            'status' => $status,
            'statuscode' => $statuscode,
        ];
        return $result;
        
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function verify_coupon_returns() {
        return new external_single_structure(
            array(
                'amount' => new external_value(PARAM_FLOAT, 'Amount', VALUE_OPTIONAL),
                'discount_percentage' => new external_value(PARAM_FLOAT, 'Discount percentage'),
                'status' => new external_value(PARAM_RAW, 'Status', VALUE_OPTIONAL),
                'statuscode' => new external_value(PARAM_INT, 'Status Code', VALUE_OPTIONAL),
            )
        );
    }

    /**
     * Returns description of method find_coupon_by_userid parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function find_coupon_by_userid_parameters() {
        return new external_function_parameters(
            array (
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'userid' => new external_value(PARAM_INT, 'Userid')
            )
        );
    }

    /**
     * Get the coupon already used or public coupon for the user with courseid.
     * 
     * Find coupon by userid and courseid
     * 
     * If the user already used a coupon for this course, 
     * then return the coupon for that user.
     * 
     * Otherwise, check if there is any public coupon for this course,
     * if yes, then apply a coupon for this userid and courseid
     * else, return a message "No coupon found".
     *
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function find_coupon_by_userid($courseid, $userid) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/local/discount/lib.php");
        $couponcode = null;
        $message = get_string('no_coupon_found', 'local_discount');

        $coupondata = $DB->get_record('local_discount_used_coupon', array('course_id' => $courseid, 'used_by' => $userid));
        if($coupondata) {
            $couponcode = $coupondata->coupon_code;
            $result = self::verify_coupon($couponcode, $userid, $courseid);

            $message = get_string('coupon_found', 'local_discount');
        } else {
            $coupon = local_discount_get_public_valid_coupon($courseid);

            if($coupon) {
                $couponcode = $coupon->coupon_code;
                $result = self::verify_coupon($couponcode, $userid, $courseid);
            
                $message = get_string('coupon_found', 'local_discount');
            } else {
                $message = get_string('no_coupon_found', 'local_discount');
            }
        }


        // Enroll info of the course.
        $enrolinfo = $DB->get_record('enrol', array('enrol' => 'fee', 'courseid' => $courseid));
        $courseinfo = $DB->get_record('course', array('id' => $courseid));
        
        $cost = 0;
        $itemid = 0;
        if($enrolinfo) {
            $cost = $enrolinfo->cost;
            $itemid = $enrolinfo->id;
        }
        
        $component = 'enrol_fee';
        $paymentarea = 'fee';
         
        $description = 'Enrolment in ' . $courseinfo->shortname;

        $result = [
            'courseid' => $courseid,
            'userid' => $userid,
            'cost' => $cost,
            'itemid' => $itemid,
            'component' => $component,
            'paymentarea' => $paymentarea,
            'description' => $description,
            'coupon_code' => $couponcode,
            'amount' => $result['amount'],
            'discount_percentage' => $result['discount_percentage'],
            'status' => $result['status'],
            'statuscode' => $result['statuscode'],
            'message' => $message,
        ];
        return $result;
        
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function find_coupon_by_userid_returns() {
        return new external_single_structure(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id', VALUE_OPTIONAL),
                'userid' => new external_value(PARAM_INT, 'User id', VALUE_OPTIONAL),
                'cost' => new external_value(PARAM_FLOAT, 'Course cost', VALUE_OPTIONAL),
                'component' => new external_value(PARAM_RAW, 'Enrol componentn', VALUE_OPTIONAL),
                'paymentarea' => new external_value(PARAM_RAW, 'Enrol payment area', VALUE_OPTIONAL),
                'itemid' => new external_value(PARAM_INT, 'Item id in enrol table', VALUE_OPTIONAL),
                'description' => new external_value(PARAM_RAW, 'Course description', VALUE_OPTIONAL),
                'coupon_code' => new external_value(PARAM_RAW, 'Coupon code', VALUE_OPTIONAL),
                'amount' => new external_value(PARAM_FLOAT, 'Amount after discount', VALUE_OPTIONAL),
                'discount_percentage' => new external_value(PARAM_FLOAT, 'Discount percentage', VALUE_OPTIONAL),
                'status' => new external_value(PARAM_RAW, 'Status', VALUE_OPTIONAL),
                'statuscode' => new external_value(PARAM_INT, 'Status code', VALUE_OPTIONAL),
                'message' => new external_value(PARAM_RAW, 'Message', VALUE_OPTIONAL),
            )
        );
    }


    /**
     * Returns description of method get parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function get_expire_time_by_courseid_parameters() {
        return new external_function_parameters(
            array (
                'courseid' => new external_value(PARAM_INT, 'Course ID')
            )
        );
    }

    /**
     * Get latest discount coupon expiry time.
     *
     * @return array of warnings and status result
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function get_expire_time_by_courseid($courseid) {
        global $CFG, $DB;
        require_once($CFG->dirroot . "/local/discount/lib.php");
        
        $couponsarray = $DB->get_records('local_discount', array('course_id' => $courseid, 'active' => 1, 'type' => 0), 'timecreated DESC', '*', 0, 1);
        foreach($couponsarray as $coupon) {
            $coupondata = $coupon;
        }

        $result = [
            'courseid' => $courseid,
            'timeexpired' => $coupondata->timeexpired
        ];
        return $result;
        
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function get_expire_time_by_courseid_returns() {
        return new external_single_structure(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id', VALUE_OPTIONAL),
                'timeexpired' => new external_value(PARAM_INT, 'Time expired', VALUE_OPTIONAL),
            )
        );
    }
    
}
