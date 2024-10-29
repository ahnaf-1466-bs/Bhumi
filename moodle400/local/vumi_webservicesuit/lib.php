<?php
/**
 * Library of interface functions and constants for lict
 *
 * @package    local
 * @subpackage vumi_webservicesuit
 * @author     Brain station 23 ltd <brainstation-23.com>
 * @copyright  2020 Brain station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_customcert\certificate;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/completionlib.php");
require_once($CFG->dirroot.'/user/profile/lib.php');

/**
 * @param $courseid
 * @return false|mixed
 * @throws dml_exception
 */
function vumi_webservicesuit_get_cert_cmid ($courseid){
    global $DB;
    $sql = "SELECT CM.id as id 
            FROM {course_modules} CM 
            JOIN {modules} M ON CM.module=M.id 
            WHERE M.name='customcert' AND CM.course=:courseid";

    $data = $DB->get_record_sql($sql, array('courseid' => $courseid));
    return $data;
}

/**
 * @param int $certid
 * @param int $userid
 * @return mixed
 * @throws dml_exception
 */

function issued_date(int $certid, int $userid) {
    global $DB;
    $sql = "SELECT id, FROM_UNIXTIME(timecreated) AS issuedtime 
            FROM {customcert_issues}
            WHERE userid = ".$userid." AND customcertid = ". $certid;
    $data = $DB->get_record_sql($sql, array('userid' => $userid, 'customcertid' => $certid));

    return $data;
}

/**
 * @param $certcmid
 * @param $userid
 * @return \mod_customcert\template
 * @throws coding_exception
 * @throws dml_exception
 */
function vumi_webservicesuit_get_certificate ($certcmid, $userid) {
    global $DB;
    $cm = get_coursemodule_from_id('customcert', $certcmid->id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $customcert = $DB->get_record('customcert', array('id' => $cm->instance), '*', MUST_EXIST);
    $template = $DB->get_record('customcert_templates', array('id' => $customcert->templateid), '*', MUST_EXIST);

    // Create new customcert issue record if one does not already exist.
    if (!$DB->record_exists('customcert_issues', array('userid' => $userid, 'customcertid' => $customcert->id))) {
        \mod_customcert\certificate::issue_certificate($customcert->id, $userid);
    }

    // Set the custom certificate as viewed.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm, $userid);

    $template = new \mod_customcert\template($template);
    $template->generate_pdf(false, $userid);

    return $template;
}

/**
 * @param $certcmid
 * @param $userid
 * @return string|null
 * @throws coding_exception
 * @throws dml_exception
 */

function vumi_webservicesuit_get_certificate_sample($certcmid,$userid){
    global $DB;
    $cm = get_coursemodule_from_id('customcert', $certcmid->id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $customcert = $DB->get_record('customcert', array('id' => $cm->instance), '*', MUST_EXIST);
    $template = $DB->get_record('customcert_templates', array('id' => $customcert->templateid), '*', MUST_EXIST);

    // Set the custom certificate as viewed.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);

    $temp = new \mod_customcert\template($template);
    $template->id = $temp->get_id();
    $template->name = $temp->get_name();
    $template->context = $temp->get_context();

    $templates = generate_pdf_sample(false, $userid, false, $template);

    return $templates;
}

/**
 * @param bool $preview
 * @param int|null $userid
 * @param bool $return
 * @param stdClass $template
 * @return string|void
 * @throws coding_exception
 * @throws dml_exception
 */
function generate_pdf_sample(bool $preview = false, int $userid = null, bool $return = false, stdClass $template) {
    global $CFG, $DB;

    $user = new stdClass();
    $user->id = $userid;
    $user->firstname = 'Sample';
    $user->lastname = 'Name';
    $user->email = 'Sample@email.com';

    require_once($CFG->libdir . '/pdflib.php');

    // Get the pages for the template, there should always be at least one page for each template.
    if ($pages = $DB->get_records('customcert_pages', array('templateid' => $template->id), 'sequence ASC')) {
        // Create the pdf object.
        $pdf = new \pdf();

        $customcert = $DB->get_record('customcert', ['templateid' => $template->id]);

        // If the template belongs to a certificate then we need to check what permissions we set for it.
        if (!empty($customcert->protection)) {
            $protection = explode(', ', $customcert->protection);
            $pdf->SetProtection($protection);
        }

        if (empty($customcert->deliveryoption)) {
            $deliveryoption = certificate::DELIVERY_OPTION_INLINE;
        } else {
            $deliveryoption = $customcert->deliveryoption;
        }

        // Remove full-stop at the end, if it exists, to avoid "..pdf" being created and being filtered by clean_filename.
        $filename = rtrim(format_string($template->name, true, ['context' => $template->context]), '.');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetTitle($filename);
        $pdf->SetAutoPageBreak(true, 0);

        // This is the logic the TCPDF library uses when processing the name. This makes names
        // such as 'الشهادة' become empty, so set a default name in these cases.
        $filename = preg_replace('/[\s]+/', '_', $filename);
        $filename = preg_replace('/[^a-zA-Z0-9_\.-]/', '', $filename);

        if (empty($filename)) {
            $filename = get_string('certificate', 'customcert');
        }

        $filename = clean_filename($filename . '.pdf');
        // Loop through the pages and display their content.
        foreach ($pages as $page) {
            // Add the page to the PDF.
            if ($page->width > $page->height) {
                $orientation = 'L';
            } else {
                $orientation = 'P';
            }
            $pdf->AddPage($orientation, array($page->width, $page->height));
            $pdf->SetMargins($page->leftmargin, 0, $page->rightmargin);
            // Get the elements for the page.
            if ($elements = $DB->get_records('customcert_elements', array('pageid' => $page->id), 'sequence ASC')) {
                // Loop through and display.
                foreach ($elements as $element) {
                    // Get an instance of the element class.
                    if ($e = \mod_customcert\element_factory::get_element_instance($element)) {
                        $e->render($pdf, $preview, $user);
                    }
                }
            }
        }
        ob_end_clean();
        if ($return) {
            return $pdf->Output('', 'S');
        }
        ob_end_clean();
        $pdf->Output($filename, $deliveryoption);
    }
}

/**
 * @param $course_id
 * @param $user_id
 * @return float|int
 * @throws dml_exception
 */
function vumi_webservicesuit_get_completion_percentage($course_id,$user_id){
    // Currently completion is calculated based on all activity completed
    // The table mdl_course_completions holds the data for all course completion record of users
    // But it depends on cronjob.
    global $DB;
    $sql = "SELECT CM.* 
            FROM {course_modules} CM
            JOIN {modules} M ON CM.module=M.id
            WHERE M.name!='customcert' AND CM.course=:course AND CM.idnumber IS NOT NULL";

    $modules = $DB->get_records_sql($sql,array('course' => $course_id));
    $course_object = vumi_webservicesuit_get_course_by_courseid($course_id);
    $cinfo = new completion_info($course_object);

    $completed_modules_count  = 0;
    $incompleted_modules_count = 0;
    foreach ($modules as $activity){
        $cdata = $cinfo->get_data($activity, false, $user_id);
        if ($cdata->completionstate == COMPLETION_COMPLETE || $cdata->completionstate == COMPLETION_COMPLETE_PASS) {
            $completed_modules_count  = $completed_modules_count  + 1;
        }
        else{
            $incompleted_modules_count = $incompleted_modules_count + 1;
        }
    }
    $total = $completed_modules_count+$incompleted_modules_count;
    $percentage = ($completed_modules_count/$total) * 100;

    return $percentage;
}

/**
 * @param $course_id
 * @return false|mixed|stdClass
 * @throws dml_exception
 */

function vumi_webservicesuit_get_course_by_courseid($course_id){
    global $DB;
    $course = $DB->get_record('course', array('id'=>$course_id));
    return $course;
}


function generate_certificate ($userid, $certid) {
    global $CFG;
    return $CFG->wwwroot . '/local/certificate/index.php?id=' . $certid . '&userid=' . $userid;
}

/**
 * Certificate Varification in respect of course or certificate exists or not.
 * @param int $customcertid
 * @param string $code
 * @return mixed
 * @throws dml_exception
 */
function check_cert_exist(int $customcertid, string $code) {
    global $DB;
    $SQL = "SELECT * 
            FROM {customcert_issues} ci 
            JOIN {customcert} cr ON ci.customcertid = cr.id
            WHERE ci.customcertid = ". $customcertid;

    $data = $DB->get_record_sql($SQL, ['customcertid' => $customcertid]);
    $course = $DB->get_record('course', ['id' => $data->course]);

    return $course;

}