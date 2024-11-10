<?php
require_once(__DIR__ . '/../../config.php');
require_once('./locallib.php');
require_once('./classes/form/create_form.php');

try {
    require_login();
} catch (Exception $exception) {
    print_r($exception);
}

$PAGE->set_url(new moodle_url('/local/marketing/create.php'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('managepagetitle', 'local_marketing'));

$form = new create_form();

if ($form->is_cancelled()) {
    redirect(new moodle_url('/local/marketing/manage.php'), get_string('create_cancelled_message', 'local_marketing'));
} 
else if ($formdata = $form->get_data()) {

    create_mail_record($formdata);
    redirect(new moodle_url('/local/marketing/manage.php'), get_string('insert_successfull', 'local_marketing'));
}

echo $OUTPUT->header();
?>

<h1 style='margin-bottom:40px;'><?php echo get_string('pluginname', 'local_marketing'); ?></h1>

<?php
$form->display();
echo $OUTPUT->footer();
?>
