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
 * @package mod_videoplus
 * @copyright 2021 Brain Station 23 LTD.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in videoplus module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function videoplus_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_ARCHETYPE:           return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return true;

        default: return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function videoplus_reset_userdata($data) {

    // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
    // See MDL-9367.

    return array();
}

/**
 * List the actions that correspond to a view of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = 'r' and edulevel = LEVEL_PARTICIPATING will
 *       be considered as view action.
 *
 * @return array
 */
function videoplus_get_view_actions() {
    return array('view','view all');
}

/**
 * List the actions that correspond to a post of this module.
 * This is used by the participation report.
 *
 * Note: This is not used by new logging system. Event with
 *       crud = ('c' || 'u' || 'd') and edulevel = LEVEL_PARTICIPATING
 *       will be considered as post action.
 *
 * @return array
 */
function videoplus_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add videoplus instance.
 * @param stdClass $data
 * @param mod_videoplus_mod_form $mform
 * @return int new videoplus instance id
 */
function videoplus_add_instance($data, $mform = null) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    $cmid = $data->coursemodule;

    $data->timemodified = time();
    $displayoptions = array();

    $displayoptions['printheading'] = $data->printheading;
    $displayoptions['printintro']   = $data->printintro;
    $displayoptions['printlastmodified'] = $data->printlastmodified;
    $data->displayoptions = serialize($displayoptions);
    $data->coursemodule = $cmid;

    $data->id = $DB->insert_record('videoplus', $data);

    // we need to use context now, so we need to make sure all needed info is already in db
    $DB->set_field('course_modules', 'instance', $data->id, array('id'=>$cmid));
    $context = context_module::instance($cmid);

    if ($mform and !empty($data->videoplus['itemid'])) {
        $draftitemid = $data->videoplus['itemid'];
        $data->content = file_save_draft_area_files($draftitemid, $context->id, 'mod_videoplus', 'content', 0, videoplus_get_editor_options($context), $data->content);
        $DB->update_record('videoplus', $data);
    }

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'videoplus', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Update videoplus instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function videoplus_update_instance($data, $mform) {
    global $CFG, $DB;

    require_once("$CFG->libdir/resourcelib.php");

    $cmid        = $data->coursemodule;

    $data->timemodified = time();
    $data->id           = $data->instance;
    $data->revision++;

    $displayoptions = array();
    $displayoptions['printheading'] = $data->printheading;
    $displayoptions['printintro']   = $data->printintro;
    $displayoptions['printlastmodified'] = $data->printlastmodified;
    $data->displayoptions = serialize($displayoptions);

    $DB->update_record('videoplus', $data);

    $context = context_module::instance($cmid);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'videoplus', $data->id, $completiontimeexpected);

    return true;
}

/**
 * Delete videoplus instance.
 * @param int $id
 * @return bool true
 */
function videoplus_delete_instance($id) {
    global $DB;

    if (!$videoplus = $DB->get_record('videoplus', array('id'=>$id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('videoplus', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'videoplus', $id, null);

    // note: all context files are deleted automatically

    $DB->delete_records('videoplus', array('id'=>$videoplus->id));

    return true;
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param stdClass $coursemodule
 * @return cached_cm_info Info to customise main videoplus display
 */
function videoplus_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if (!$videoplus = $DB->get_record('videoplus', array('id'=>$coursemodule->instance),
        'id, name, display, displayoptions, intro, introformat')) {
        return NULL;
    }

    $info = new cached_cm_info();
    $info->name = $videoplus->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('videoplus', $videoplus, $coursemodule->id, false);
    }

    if ($videoplus->display != RESOURCELIB_DISPLAY_POPUP) {
        return $info;
    }

    $fullurl = "$CFG->wwwroot/mod/videoplus/view.php?id=$coursemodule->id&amp;inpopup=1";
    $options = empty($videoplus->displayoptions) ? array() : unserialize($videoplus->displayoptions);
    $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
    $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
    $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
    $info->onclick = "window.open('$fullurl', '', '$wh'); return false;";

    return $info;
}

/**
 * Lists all browsable file areas
 *
 * @package  mod_videoplus
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function videoplus_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['content'] = get_string('content', 'mod_videoplus');
    return $areas;
}

/**
 * File browsing support for videoplus module content area.
 *
 * @package  mod_videoplus
 * @category files
 * @param stdClass $browser file browser instance
 * @param stdClass $areas file areas
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param int $itemid item ID
 * @param string $filepath file path
 * @param string $filename file name
 * @return file_info instance or null if not found
 */
function videoplus_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    global $CFG;

    if (!has_capability('moodle/course:managefiles', $context)) {
        // students can not peak here!
        return null;
    }

    $fs = get_file_storage();

    if ($filearea === 'content') {
        $filepath = is_null($filepath) ? '/' : $filepath;
        $filename = is_null($filename) ? '.' : $filename;

        $urlbase = $CFG->wwwroot.'/pluginfile.php';
        if (!$storedfile = $fs->get_file($context->id, 'mod_videoplus', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_videoplus', 'content', 0);
            } else {
                // not found
                return null;
            }
        }
        require_once("$CFG->dirroot/mod/videoplus/locallib.php");
        return new videoplus_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true, false);
    }

    // note: videoplus_intro handled in file_browser automatically

    return null;
}

/**
 * Serves the videoplus files.
 *
 * @package  mod_videoplus
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if file not found, does not return if found - just send the file
 */
function videoplus_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);
    if (!has_capability('mod/videoplus:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        // intro is handled automatically in pluginfile.php
        return false;
    }

    // $arg could be revision number or index.html
    $arg = array_shift($args);
    if ($arg == 'index.html' || $arg == 'index.htm') {
        // serve videoplus content
        $filename = $arg;

        if (!$videoplus = $DB->get_record('videoplus', array('id'=>$cm->instance), '*', MUST_EXIST)) {
            return false;
        }

        // We need to rewrite the pluginfile URLs so the media filters can work.
        $content = file_rewrite_pluginfile_urls($videoplus->content, 'webservice/pluginfile.php', $context->id, 'mod_videoplus', 'content',
            $videoplus->revision);
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        $formatoptions->context = $context;

        // Remove @@PLUGINFILE@@/.
        $options = array('reverse' => true);
        $content = file_rewrite_pluginfile_urls($content, 'webservice/pluginfile.php', $context->id, 'mod_videoplus', 'content',
            $videoplus->revision, $options);
        $content = str_replace('@@PLUGINFILE@@/', '', $content);

        send_file($content, $filename, 0, 0, true, true);
    } else {
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_videoplus/$filearea/0/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            $videoplus = $DB->get_record('videoplus', array('id'=>$cm->instance), 'id, legacyfiles', MUST_EXIST);
            if ($videoplus->legacyfiles != RESOURCELIB_LEGACYFILES_ACTIVE) {
                return false;
            }
            if (!$file = resourcelib_try_file_migration('/'.$relativepath, $cm->id, $cm->course, 'mod_videoplus', 'content', 0)) {
                return false;
            }
            //file migrate - update flag
            $videoplus->legacyfileslast = time();
            $DB->update_record('videoplus', $videoplus);
        }

        // finally send the file
        send_stored_file($file, null, 0, $forcedownload, $options);
    }
}

/**
 * Serve the files.
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param context $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 *
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */

function mod_videoplus_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    $itemid = array_shift($args);
    $filename = array_pop($args);

    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    $fs = get_file_storage();

    $file = $fs->get_file($context->id, 'mod_videoplus', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

/**
 * Return a list of videoplus types
 * @param string $videoplustype current videoplus type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function videoplus_page_type_list($videoplustype, $parentcontext, $currentcontext) {
    $module_videoplustype = array('mod-videoplus-*'=>get_string('videoplus-mod-videoplus-x', 'mod_videoplus'));
    return $module_videoplustype;
}

/**
 * Export videoplus resource contents
 *
 * @return array of file content
 */
function videoplus_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = array();
    $context = context_module::instance($cm->id);

    $videoplus = $DB->get_record('videoplus', array('id'=>$cm->instance), '*', MUST_EXIST);

    // videoplus contents
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_videoplus', 'content', 0, 'sortorder DESC, id ASC', false);
    foreach ($files as $fileinfo) {
        $file = array();
        $file['type']         = 'file';
        $file['filename']     = $fileinfo->get_filename();
        $file['filepath']     = $fileinfo->get_filepath();
        $file['filesize']     = $fileinfo->get_filesize();
        $file['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/mod_videoplus/content/'.$videoplus->revision.$fileinfo->get_filepath().$fileinfo->get_filename(), true);
        $file['timecreated']  = $fileinfo->get_timecreated();
        $file['timemodified'] = $fileinfo->get_timemodified();
        $file['sortorder']    = $fileinfo->get_sortorder();
        $file['userid']       = $fileinfo->get_userid();
        $file['author']       = $fileinfo->get_author();
        $file['license']      = $fileinfo->get_license();
        $file['mimetype']     = $fileinfo->get_mimetype();
        $file['isexternalfile'] = $fileinfo->is_external_file();
        if ($file['isexternalfile']) {
            $file['repositorytype'] = $fileinfo->get_repository_type();
        }
        $contents[] = $file;
    }

    // videoplus html conent
    $filename = 'index.html';
    $videoplusfile = array();
    $videoplusfile['type']         = 'file';
    $videoplusfile['filename']     = $filename;
    $videoplusfile['filepath']     = '/';
    $videoplusfile['filesize']     = 0;
    $videoplusfile['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/mod_videoplus/content/' . $filename, true);
    $videoplusfile['timecreated']  = null;
    $videoplusfile['timemodified'] = $videoplus->timemodified;
    // make this file as main file
    $videoplusfile['sortorder']    = 1;
    $videoplusfile['userid']       = null;
    $videoplusfile['author']       = null;
    $videoplusfile['license']      = null;
    $contents[] = $videoplusfile;

    return $contents;
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
function videoplus_dndupload_register() {
    return array('types' => array(
        array('identifier' => 'text/html', 'message' => get_string('createpage', 'mod_videoplus')),
        array('identifier' => 'text', 'message' => get_string('createpage', 'mod_videoplus'))
    ));
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function videoplus_dndupload_handle($uploadinfo) {
    // Gather the required info.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '<p>'.$uploadinfo->displayname.'</p>';
    $data->introformat = FORMAT_HTML;
    if ($uploadinfo->type == 'text/html') {
        $data->content = clean_param($uploadinfo->content, PARAM_CLEANHTML);
    } else {
        $data->content = clean_param($uploadinfo->content, PARAM_TEXT);
    }
    $data->coursemodule = $uploadinfo->coursemodule;

    // Set the display options to the site defaults.
    $config = get_config('videoplus');
    $data->display = $config->display;
    $data->popupheight = $config->popupheight;
    $data->popupwidth = $config->popupwidth;
    $data->printheading = $config->printheading;
    $data->printintro = $config->printintro;
    $data->printlastmodified = $config->printlastmodified;

    return videoplus_add_instance($data, null);
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $videoplus       videoplus object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function videoplus_view($videoplus, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $videoplus->id
    );

    $event = \mod_videoplus\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('videoplus', $videoplus);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_url_for_videofile (int $courseid, int $cmid) {
    global $DB;
    $SQL = 'SELECT *
            FROM {videoplus_videofile} vv 
            JOIN {videoplus_videourl} vvu
            WHERE vv.course = vvu.course AND 
                vv.draftid = vvu.draftid AND
                vvu.course = :course AND vvu.cmid = :cmid';

    $message = $DB->get_records_sql($SQL, ['course' => $courseid, 'cmid' => $cmid]);

    return $message;
}

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_url_for_pdffile (int $courseid, int $cmid) {
    global $DB;
    $SQL = 'SELECT *
            FROM {videoplus_pdffile} vp 
            JOIN {videoplus_pdfurl} vpu
            WHERE vp.course = vpu.course AND 
                vp.draftid = vpu.draftid AND
                vp.cmid = vpu.cmid AND 
                vpu.course = '. $courseid . ' AND vpu.cmid = ' .$cmid;

    $message = $DB->get_records_sql($SQL, ['course' => $courseid]);

    return $message;
}

/**
 * @param int $courseid
 * @param int $cmid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_videofile (int $courseid, int $cmid,  stdClass $fromform){
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->cmid = $cmid;
    $record_to_insert->description = '';
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->timemodified = time();

    $DB->insert_record('videoplus_videofile', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param int $cmid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_videofileurl (int $courseid, int $cmid,  stdClass $fromform){
    global $DB;
    $videopath = convert_videofile_to_url($courseid, $fromform);

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->cmid = $cmid;
    $record_to_insert->videourl = $videopath;
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->timemodified = time();

    $DB->insert_record('videoplus_videourl', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param int $cmid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_videofile (int $courseid, int $cmid,  stdClass $fromform, int $messageid){
    global $DB;

    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->cmid = $cmid;
    $object->description = $fromform->description;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->update_record('videoplus_videofile', $object);
}

/**
 * @param int $courseid
 * @param int $cmid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_videofileurl (int $courseid, int $cmid,  stdClass $fromform, int $messageid){
    global $DB;
    $videopath = convert_videofile_to_url($courseid, $fromform);

    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->cmid = $cmid;
    $object->videourl = $videopath;
    $object->draftid = $fromform->draftid;
    $object->timemodified = time();

    $DB->update_record('videoplus_videourl', $object);
}

/**
 * @param int $courseid
 * @param int $cmid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_pdffile (int $courseid, int $cmid,  stdClass $fromform){
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->cmid = $cmid;
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->timemodified = time();

    $DB->insert_record('videoplus_pdffile', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param int $cmid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_pdffileurl (int $courseid, int $cmid,  stdClass $fromform){
    global $DB;
    $pdfpath = convert_pdffile_to_url($courseid, $fromform);

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->cmid = $cmid;
    $record_to_insert->pdfurl = $pdfpath;
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->timemodified = time();

    $DB->insert_record('videoplus_pdfurl', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param int $cmid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_pdffile (int $courseid, int $cmid,  stdClass $fromform, int $messageid){
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->id = $messageid;
    $record_to_insert->course = $courseid;
    $record_to_insert->cmid = $cmid;
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->timemodified = time();

    $DB->update_record('videoplus_pdffile', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param int $cmid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_pdffileurl (int $courseid, int $cmid,  stdClass $fromform, int $messageid){
    global $DB;
    $pdfpath = convert_pdffile_to_url($courseid, $fromform);

    $record_to_insert = new stdClass();
    $record_to_insert->id = $messageid;
    $record_to_insert->course = $courseid;
    $record_to_insert->cmid = $cmid;
    $record_to_insert->pdfurl = $pdfpath;
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->timemodified = time();

    $DB->update_record('videoplus_pdfurl', $record_to_insert, false);
}

/**
 * Function for converting video file from draftid to URL
 */

function convert_videofile_to_url(int $courseid, stdClass $fromform) {
    $videofile = '';
    $context = context_system::instance();
    $filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);

    //adding a new feature
    file_save_draft_area_files( $fromform->draftid, $context->id, 'mod_videoplus', 'attachment', $fromform->draftid, $filemanageropts);

    if ($fromform->draftid) {
        $fs = get_file_storage();

        if ($files = $fs->get_area_files($context->id, 'mod_videoplus', 'attachment',  $fromform->draftid, 'sortorder', false)) {

            // Look through each file beig managed
            foreach ($files as $file) {

                // Build the File URL. Long process! But extremely accurate.
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                // Display the image
                $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . ':' . $fileurl->get_port() . $fileurl->get_path() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();

                $videofile = $download_url;

            }
        }
    }
    return $videofile;
}

/**
 * Function for converting pdf from draftid to URL
 */

function convert_pdffile_to_url (int $courseid, stdClass $fromform) {
    $image = '';
    $context = context_system::instance();

    $filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);
    //adding a new feature
    file_save_draft_area_files( $fromform->draftid, $context->id, 'mod_videoplus', 'attachment',  $fromform->draftid, $filemanageropts);

    if ($fromform->draftid) {
        $fs = get_file_storage();

        if ($files = $fs->get_area_files($context->id, 'mod_videoplus', 'attachment',  $fromform->draftid, 'sortorder', false)) {
            // Look through each file being managed
            foreach ($files as $file) {

                // Build the File URL. Long process! But extremely accurate.
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                // Display the image
                $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . ':' . $fileurl->get_port() . $fileurl->get_path() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();

                $image = $download_url;

            }
        }
    }
    return $image;
}

function videoplus_get_video_and_pdf($courseid, $cmid) {
    global $DB;
    $sql = "SELECT v.name, v.intro, vvu.videourl, vpu.pdfurl
            FROM {videoplus} v
            LEFT JOIN {videoplus_videofile} vv ON vv.course = v.course AND vv.cmid = v.coursemodule
            LEFT JOIN {videoplus_videourl} vvu ON vvu.cmid = v.coursemodule
            LEFT JOIN {videoplus_pdffile} vp ON vv.cmid = vp.cmid
            LEFT JOIN {videoplus_pdfurl} vpu ON vp.cmid = vpu.cmid
            WHERE vv.course = :courseid AND vv.cmid = :cmid";

    $message = $DB->get_records_sql($sql, ['courseid' => $courseid, 'cmid' => $cmid]);

    return $message;

}