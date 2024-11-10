<?php

require_once(__DIR__ . '/../../config.php');
require_once('./locallib.php');
require_once('./classes/form/edit_form.php');

try {
    require_login();
} catch (Exception $exception) {
    print_r($exception);
}


$PAGE->set_url(new moodle_url('/local/marketing/edit.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('managepagetitle', 'local_marketing'));

$id = optional_param('id', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

if($id && $delete) {
    delete_mail_record($id);
    redirect(new moodle_url('/local/marketing/manage.php'), get_string('deleted', 'local_marketing'));
}



$email = $DB->get_record('local_marketing_emails', ['id' => $id]);
$form = new edit_form(null, ['id' => $id]);
$form->set_data($email);

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/marketing/manage.php'), get_string('edit_cancelled_message', 'local_marketing'));
} 
else if ($formdata = $form->get_data()) {
    // Your code to save the form data goes here
    update_mail_record($formdata);
    redirect(new moodle_url('/local/marketing/manage.php'), get_string('edit_successfull', 'local_marketing'));
}

echo $OUTPUT->header();

echo "<h1 style='margin-bottom:40px;'>" . get_string('pluginname', 'local_marketing') . "</h1>";

$form->display();

echo $OUTPUT->footer();