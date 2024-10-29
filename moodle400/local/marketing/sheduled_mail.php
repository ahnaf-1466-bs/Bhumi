<?php

require_once(dirname(__FILE__) . '/../../config.php');

global $DB;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get the form data
    $course_id = $_POST['course_id'];
    $mail_subject = $_POST['mail_subject'];
    $mail_body = $_POST['mail_body'];
    $scheduled_time = $_POST['scheduled_time'];

 
    $enrolled_students = $DB->get_records_sql("
        SELECT u.username, u.firstname, u.lastname, u.email, u.id
        FROM {user} AS u
        INNER JOIN {user_enrolments} AS ue ON ue.userid = u.id
        INNER JOIN {enrol} AS e ON e.id = ue.enrolid
        INNER JOIN {role_assignments} AS ra ON ra.userid = u.id
        INNER JOIN {role} AS r ON r.id = ra.roleid
        WHERE e.courseid = :courseid
        AND r.shortname = 'student'
    ", ['courseid' => $course_id]);

  
    foreach ($enrolled_students as $user) {
   
        date_default_timezone_set('Asia/Dhaka');
        $scheduled_timestamp = strtotime($scheduled_time);
        $table = 'local_marketing_emails';

        $data = new stdClass();   
        $data->user_id = $user->id;
        $data->user_email = $user->email;
        $data->mail_subject = $mail_subject;
        $data->mail_body = $mail_body;
        $data->sent_status = 0;
        $data->scheduled_time = $scheduled_timestamp;
        $data->last_modified_time = time();
       
        $DB->insert_record($table, $data);
    
    }


}