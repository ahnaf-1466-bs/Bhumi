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
 * Plugin's library functions.
 *
 * @package    local_marketing
 * @copyright  2023 Brainstation23
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */




 function local_marketing_display_mails(){
    global $DB, $OUTPUT;
    $mailrecords = $DB->get_records('local_marketing_emails');

    foreach ($mailrecords as $mailrecord) {
        $mailrecord->sent_status = $mailrecord->sent_status;
        $course = $DB->get_record('course', array('id' => $mailrecord->course_id), 'fullname');
        $mailrecord->course_name = strlen($course->fullname) > 30 ? substr($course->fullname, 0, 30) . '...' : $course->fullname;

        // Limit the mail subject and body to 20 characters and append "..."
        $mailrecord->mail_subject = strlen($mailrecord->mail_subject) > 20 ? substr($mailrecord->mail_subject, 0, 20) . '...' : $mailrecord->mail_subject;
        $mailrecord->mail_body = strlen($mailrecord->mail_body) > 20 ? substr($mailrecord->mail_body, 0, 20) . '...' : $mailrecord->mail_body;

        // Convert the scheduled time and last modified time to human-readable format.
        $mailrecord->scheduled_time = userdate($mailrecord->scheduled_time, '%d %B %Y, %I:%M %p');
        $mailrecord->last_modified_time = userdate($mailrecord->last_modified_time, '%d %B %Y, %I:%M %p');
    }


    // Data to be passed in the manage template.
    $templatecontext = (object) [
        'texttodisplay' => array_values($mailrecords),
        'editurl' => new moodle_url('/local/marketing/edit.php'),
        'inserturl' => new moodle_url('/local/marketing/create.php'),
        'userslist' => new moodle_url('/local/marketing/userslist.php'),
    ];
    
    echo $OUTPUT->render_from_template('local_marketing/manage', $templatecontext);
    
}


function get_course_list() {
    global $DB, $OUTPUT;

    $courses = $DB->get_records_sql("SELECT * FROM {course} WHERE startdate > 0 AND enddate > 0");

    $course_list = array();
    foreach ($courses as $course) {
        $course_list[] = array(
            'id' => $course->id,
            'fullname' => $course->fullname,
        );
    }

    return $course_list;
}

function create_mail_record($formdata){

    global $DB;
   
    $course_id = $formdata->course_id;
    $mail_subject = $formdata->mail_subject;
    $mail_body = $formdata->mail_body;

    $scheduled_time = $formdata->scheduled_time;
    date_default_timezone_set('Asia/Dhaka');
    $scheduled_date = DateTime::createFromFormat('U', $scheduled_time);

    $sql = "SELECT u.id, u.firstname, u.lastname, u.email 
            FROM mdl_user AS u 
            JOIN mdl_role_assignments AS ra ON ra.userid = u.id 
            JOIN mdl_context AS ctx ON ctx.id = ra.contextid 
            JOIN mdl_course AS c ON c.id = ctx.instanceid 
            WHERE c.id=:courseid
            AND ra.roleid=5;";
    $enrolled_students = $DB->get_records_sql($sql, ['courseid' => $course_id]);
    
    $scheduled_timestamp = $scheduled_date->getTimestamp();    
    $table = 'local_marketing_emails';
    $data = new stdClass();   
    $data->mail_subject = $mail_subject;
    $data->mail_body = $mail_body;
    $data->course_id = $course_id;
    $data->sent_status = 0;
    $data->scheduled_time = $scheduled_timestamp;
    $data->last_modified_time = time(); 
    $email_id_ref = $DB->insert_record($table, $data);


    foreach ($enrolled_students as $user) {
        // Insert record in local_marketing_users table
        // var_dump($user);
        $table = 'local_marketing_users';
        $data = new stdClass();
        $data->scheduled_email_id = $email_id_ref;
        $data->user_id = $user->id;
        $data->user_email = $user->email;
        $data->sent_status = 0;
        $data->last_modified_time = time();
        $DB->insert_record($table, $data);
    }

}


function update_mail_record($formdata) {

    global $DB;

    $mail_id = $formdata->id;
    $course_id = $formdata->course_id;
    $mail_subject = $formdata->mail_subject;
    $mail_body = $formdata->mail_body;

    $scheduled_time = $formdata->scheduled_time;
    date_default_timezone_set('Asia/Dhaka');
    $scheduled_date = DateTime::createFromFormat('U', $scheduled_time);
    $scheduled_timestamp = $scheduled_date->getTimestamp();

    $table = 'local_marketing_emails';
    $data = new stdClass();
    $data->id = $mail_id;
    $data->mail_subject = $mail_subject;
    $data->mail_body = $mail_body;
    $data->course_id = $course_id;
    $data->scheduled_time = $scheduled_timestamp;
    $data->last_modified_time = time();
    $DB->update_record($table, $data);
}


function delete_mail_record($email_id) {
    global $DB;

    // Delete all associated user records.
    $DB->delete_records('local_marketing_users', ['scheduled_email_id' => $email_id]);

    // Delete the email record.
    $DB->delete_records('local_marketing_emails', ['id' => $email_id]);
}



function process_scheduled_mail() {
    global $DB;
    
    $emails = $DB->get_records_select('local_marketing_emails', 'sent_status = 0');

    foreach ($emails as $email) {

        if (time() >= $email->scheduled_time) {
            $sentstatus = 1;
            $users = $DB->get_records('local_marketing_users', array('scheduled_email_id' => $email->id, 'sent_status' => 0));

            foreach ($users as $user) {
                $useremail = new stdClass();
                $useremail->email = $user->user_email;
                $useremail->id = $user->id;
                $useremail->username = $user->user_email;

                $sender = new stdClass();
                $sender->email = 'tester@example.com';
                $sender->id = -98;

                // send email
                ob_start();
                $success = email_to_user($useremail, $sender, $email->mail_subject, $email->mail_body, '', '', '', false);
                ob_end_clean();
                var_dump('Email sent!');
                // send_email($user->user_email, $email->mail_subject, $email->mail_body);
                if($success) {
                    $user->sent_status = 1;
                    $user->last_modified_time = time();
                    $DB->update_record('local_marketing_users', $user);
                } else {
                    $sentstatus = 0;
                }
            }
            $email->sent_status = $sentstatus;
            $email->last_modified_time = time();
            $DB->update_record('local_marketing_emails', $email);
        }
    }
}


function local_marketing_get_userslist_by_id($id) {
    global $DB;
    $sql = "SELECT lmu.id, u.username, u.firstname, u.email, lmu.sent_status, lmu.last_modified_time
            FROM {local_marketing_users} lmu
            LEFT JOIN {local_marketing_emails} lme ON lme.id=lmu.scheduled_email_id
            LEFT JOIN {user} u ON u.id=lmu.user_id
            WHERE lme.id=$id";

    $users = $DB->get_records_sql($sql);
    return $users;
}

function local_marketing_convert_timestamp_to_date($timestamp) {
    $timezone = 'Asia/Dhaka';
    $date =  new DateTime('@'. $timestamp);
    $date->setTimezone(new DateTimeZone($timezone));
    return $date->format('d F, Y');
}

