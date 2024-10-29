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
 * Library functions for the plugin local_preregistration.
 *
 * @package    local_preregistration
 * @copyright  2023 Brain Station 23 Ltd.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Return the list of all courses with id and fullname for coupon creation form options.
 * 
 * @return array $courses
 */
function local_preregistration_get_all_courses() {
    global $DB;

    $courses = $DB->get_records('course', array(), '', 'id, fullname');
    return $courses;

}

/**
 * Return the list of all batches.
 * 
 * @return array $batches
 */
function local_preregistration_get_batches() {
    global $DB;
    $sql = 'SELECT lpb.id, lpb.courseid, lpb.name, lpb.description, lpb.startdate, lpb.enddate,
                    lpb.active, lpb.timecreated, lpb.timemodified, c.fullname
            FROM {local_preregistration_batch} lpb
            LEFT JOIN {course} c ON lpb.courseid = c.id
            ORDER BY lpb.timecreated DESC';

    $batches = $DB->get_recordset_sql($sql);
    return $batches;
}

/**
 * Convert timestamp to date format.
 * 
 * @return string $time
 */
function local_preregistration_convert_to_date($timestamp) {
    $timezone = 'Asia/Dhaka';
    $date =  new DateTime('@'. $timestamp);
    $date->setTimezone(new DateTimeZone($timezone));
    return $date->format('d F, Y');
}

/**
 * Delete batch data by id.
 * 
 */
function local_preregistration_delete_batch_by_id($id) {
    // TODO: Need to implement deleting all data associated with this batch.

    global $DB;
    $DB->delete_records('local_preregistration_batch', array('id' => $id));
}

/**
 * Delete batch details data by id (programdate/deadline etc.)
 * 
 */
function local_preregistration_delete_batch_data($id, $batchid) {
    global $DB;
    $DB->delete_records('local_preregistration_data', array('id' => $id, 'batchid' => $batchid));
}

/**
 * Get all program dates by batchid.
 * 
 * @param int $batchid
 * @return array $records
 * 
 */
function local_preregistration_get_programdates($batchid) {
    global $DB;

    $sql = "SELECT * FROM {local_preregistration_data} WHERE batchid = $batchid AND type='programdate'";
    $programdatesdata = $DB->get_records_sql($sql);

    $programdates = [];
    foreach($programdatesdata as $key => $value) {
        $temp = [];

        $temp['id'] = $value->id;
        $temp['batchid'] = $value->batchid;
        $temp['courseid'] = $value->courseid    ;
        $temp['type'] = $value->type;
        $temp['value'] = $value->value;
        $temp['timecreated'] = $value->timecreated;
        $temp['timemodified'] = $value->timemodified;

        // Converting timestamp to date time format.
        $temp['value'] = local_preregistration_convert_to_date($temp['value']);
        $temp['timecreated'] = local_preregistration_convert_to_date($temp['timecreated']);
        $temp['timemodified'] = local_preregistration_convert_to_date($temp['timemodified']);
        
        array_push($programdates, $temp);
    }
    return $programdates;
}

/**
 * Get batch details by type(cost/deadline/courselength)
 * 
 * There should be one deadline for one batchid.
 * @param int $batchid
 * @return object $deadline
 * 
 */
function local_preregistration_get_data_by_type($batchid, $type){
    global $DB;

    $sql = "SELECT * FROM {local_preregistration_data} WHERE batchid = $batchid AND type='$type'";
    $data = $DB->get_record_sql($sql);

    return $data;
}

function local_preregistration_get_userslist_by_batchid($batchid) {
    global $DB;

    $sql = "SELECT lpu.id, lpu.batchid, lpb.name AS batchname, c.fullname AS coursename, lpu.userid, lpu.name as username, lpu.email as useremail, lpu.timemodified 
            FROM {local_preregistration_users} lpu
            LEFT JOIN {local_preregistration_batch} lpb ON lpb.id=lpu.batchid
            LEFT JOIN {course} c ON c.id=lpu.courseid
            WHERE lpu.batchid = $batchid";
    $users = $DB->get_records_sql($sql);
    return $users;
}



function local_preregistration_send_reminder_email_to_admins() {
    var_dump('Sending email to all admin users...');
    global $DB;

    $batches = $DB->get_records('local_preregistration_batch', array('admin_email_sent' => 0));

    foreach($batches as $batch) {
        // Calculate time 7 days before the enddate.
        $tempdate = $batch->enddate - (60 * 60 * 24) * 7; // 7 days before enddate.
        // If 7 days remaining for the enddate to be expired, then we will send email to users of this batch.
        if(time() > $tempdate) {
            $adminusers = local_preregistration_get_adminusers();
            $adminemailsent = 1;
            foreach($adminusers as $userid) {
                $status = local_preregistration_send_email($userid, 'admin', $batch->courseid);
                if(!$status) {
                    $adminemailsent = 0;
                }
            }
            $batch->admin_email_sent = $adminemailsent;
            $DB->update_record('local_preregistration_batch', $batch);
        }
    }
    
}

function local_preregistration_send_reminder_email_to_students() {
    var_dump("Reminder email to students...");

    global $DB;

    $batches = $DB->get_records('local_preregistration_batch', array('student_email_sent' => 0));

    foreach($batches as $batch) {
        
        
        if(time() > $batch->enddate) {
            $usersofbatch = $DB->get_records('local_preregistration_users', array('batchid' => $batch->id));
            $studentemailsent = 1;
            foreach($usersofbatch as $user) {
                var_dump($user->userid);
                $status = local_preregistration_send_email($user->userid, 'student', $batch->courseid);
                if(!$status) {
                    $studentemailsent = 0;
                } else {
                    $user->email_sent = 1;
                    var_dump('Email sent to: ');
                    var_dump($user->id);
                    var_dump($user->email);
                    $DB->update_record('local_preregistration_users', $user);
                }
            }
            $batch->student_email_sent = $studentemailsent;
            $DB->update_record('local_preregistration_batch', $batch);

        }
    }

}

function local_preregistration_get_adminusers() {
    global  $CFG;
    $adminusers = explode(',', $CFG->siteadmins);
    return $adminusers;
}

function local_preregistration_send_email($userid, $type, $courseid) {
    global $DB, $CFG;
    
    $user = $DB->get_record('user', array('id' => $userid));

    $useremail = new stdClass();
    $useremail->email = $user->email;
    $useremail->id = $user->id;
    $course = $DB->get_record('course', array('id' => $courseid));
    $user = $DB->get_record('user', array('id' => $userid));

    $subject = '';
    $emailtext = '';
    if($type == 'student') {
        $subjecttemp = get_config('local_preregistration', 'subject_student');
        $emailbodytemp = get_config('local_preregistration','emailbody_student');

        $subject = str_replace('{{coursename}}', $course->fullname, $subjecttemp);
        $emailtext = str_replace('{{coursename}}', $course->fullname, $emailbodytemp);
        $emailtext = str_replace('{{username}}', $user->firstname, $emailtext);
    } else if($type == 'admin') {
        $subjecttemp = get_config('local_preregistration', 'subject_admin');
        $emailbodytemp = get_config('local_preregistration','emailbody_admin');

        $subject = str_replace('{{coursename}}', $course->fullname, $subjecttemp);
        $emailtext = str_replace('{{coursename}}', $course->fullname, $emailbodytemp);
        $emailtext = str_replace('{{username}}', $user->firstname, $emailtext);
    } else {
        $subjecttemp = get_string('preregistration_success_email_subject', 'local_preregistration');
        $emailbodytemp = get_string('preregistration_success_email_body', 'local_preregistration');

        $subject = str_replace('{{coursename}}', $course->fullname, $subjecttemp);
        $emailtext = str_replace('{{coursename}}', $course->fullname, $emailbodytemp);
        $emailtext = str_replace('{{username}}', $user->firstname, $emailtext);
    }
    
    $sender = new stdClass();
    $sender->email = 'tester@example.com';
    $sender->id = -98;

    // send email
    ob_start();
    $success = email_to_user($useremail, $sender, $subject, $emailtext, '', '', '', false);

    ob_end_clean();
    return $success;
}
