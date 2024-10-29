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
 * @package mod_coursefeedback
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

const COURSEFEEDBACK_TABLE_NAME = 'coursefeedback';
/**
 * List of features supported in Page module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function coursefeedback_supports($feature) {
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
        case FEATURE_MOD_PURPOSE:             return MOD_PURPOSE_CONTENT;

        default: return null;
    }
}

/**
 * This function is used by the reset_course_userdata function in moodlelib.
 * @param $data the data submitted from the reset course.
 * @return array status array
 */
function coursefeedback_reset_userdata($data) {

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
function coursefeedback_get_view_actions() {
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
function coursefeedback_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add coursefeedback instance.
 * @param stdClass $data
 * @param mod_coursefeedback_mod_form $mform
 * @return int new coursefeedback instance id
 */
function coursefeedback_add_instance($data, $mform = null) {
    global $CFG, $DB;


    $data->timemodified = time();

    $data->id = $DB->insert_record(COURSEFEEDBACK_TABLE_NAME, $data);

    return $data->id;
}

/**
 * Update coursefeedback instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function coursefeedback_update_instance($data, $mform) {
    global $CFG, $DB;

    $data->timemodified = time();
    $data->id           = $data->instance;

    $DB->update_record(COURSEFEEDBACK_TABLE_NAME, $data);

    return true;
}

/**
 * Delete coursefeedback instance.
 * @param int $id
 * @return bool true
 */
function coursefeedback_delete_instance($id) {
    global $DB;

    if (!$coursefeedback = $DB->get_record(COURSEFEEDBACK_TABLE_NAME, array('id'=>$id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance(COURSEFEEDBACK_TABLE_NAME, $id);
    \core_completion\api::update_completion_date_event($cm->id, COURSEFEEDBACK_TABLE_NAME, $id, null);

    // note: all context files are deleted automatically

    $DB->delete_records(COURSEFEEDBACK_TABLE_NAME, array('id'=>$coursefeedback->id));

    return true;
}



/**
 * File browsing support for coursefeedback module content area.
 *
 * @package  mod_coursefeedback
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
function coursefeedback_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
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
        if (!$storedfile = $fs->get_file($context->id, 'mod_coursefeedback', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_coursefeedback', 'content', 0);
            } else {
                // not found
                return null;
            }
        }
        require_once("$CFG->dirroot/mod/coursefeedback/locallib.php");
        return new coursefeedback_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true, false);
    }

    // note: coursefeedback_intro handled in file_browser automatically

    return null;
}

/**
 * Serves the coursefeedback files.
 *
 * @package  mod_coursefeedback
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
function coursefeedback_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);
    if (!has_capability('mod/coursefeedback:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        // intro is handled automatically in pluginfile.php
        return false;
    }

    // $arg could be revision number or index.html
    $arg = array_shift($args);
    if ($arg == 'index.html' || $arg == 'index.htm') {
        // serve coursefeedback content
        $filename = $arg;

        if (!$coursefeedback = $DB->get_record('coursefeedback', array('id'=>$cm->instance), '*', MUST_EXIST)) {
            return false;
        }

        // We need to rewrite the pluginfile URLs so the media filters can work.
        $content = file_rewrite_pluginfile_urls($coursefeedback->content, 'webservice/pluginfile.php', $context->id, 'mod_coursefeedback', 'content',
                                                $coursefeedback->revision);
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        $formatoptions->context = $context;
        $content = format_text($content, $coursefeedback->contentformat, $formatoptions);

        // Remove @@PLUGINFILE@@/.
        $options = array('reverse' => true);
        $content = file_rewrite_pluginfile_urls($content, 'webservice/pluginfile.php', $context->id, 'mod_coursefeedback', 'content',
                                                $coursefeedback->revision, $options);
        $content = str_replace('@@PLUGINFILE@@/', '', $content);

        send_file($content, $filename, 0, 0, true, true);
    } else {
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_coursefeedback/$filearea/0/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            $coursefeedback = $DB->get_record('coursefeedback', array('id'=>$cm->instance), 'id, legacyfiles', MUST_EXIST);
            if ($coursefeedback->legacyfiles != RESOURCELIB_LEGACYFILES_ACTIVE) {
                return false;
            }
            if (!$file = resourcelib_try_file_migration('/'.$relativepath, $cm->id, $cm->course, 'mod_coursefeedback', 'content', 0)) {
                return false;
            }
            //file migrate - update flag
            $coursefeedback->legacyfileslast = time();
            $DB->update_record('coursefeedback', $coursefeedback);
        }

        // finally send the file
        send_stored_file($file, null, 0, $forcedownload, $options);
    }
}

/**
 * Return a list of coursefeedback types
 * @param string $coursefeedbacktype current coursefeedback type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function coursefeedback_coursefeedback_type_list($coursefeedbacktype, $parentcontext, $currentcontext) {
    $module_coursefeedbacktype = array('mod-coursefeedback-*'=>get_string('coursefeedback-mod-coursefeedback-x', 'coursefeedback'));
    return $module_coursefeedbacktype;
}

/**
 * Export coursefeedback resource contents
 *
 * @return array of file content
 */
function coursefeedback_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = array();
    $context = context_module::instance($cm->id);

    $coursefeedback = $DB->get_record('coursefeedback', array('id'=>$cm->instance), '*', MUST_EXIST);

    // coursefeedback contents
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_coursefeedback', 'content', 0, 'sortorder DESC, id ASC', false);
    foreach ($files as $fileinfo) {
        $file = array();
        $file['type']         = 'file';
        $file['filename']     = $fileinfo->get_filename();
        $file['filepath']     = $fileinfo->get_filepath();
        $file['filesize']     = $fileinfo->get_filesize();
        $file['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/mod_coursefeedback/content/'.$coursefeedback->revision.$fileinfo->get_filepath().$fileinfo->get_filename(), true);
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

    // coursefeedback html conent
    $filename = 'index.html';
    $coursefeedbackfile = array();
    $coursefeedbackfile['type']         = 'file';
    $coursefeedbackfile['filename']     = $filename;
    $coursefeedbackfile['filepath']     = '/';
    $coursefeedbackfile['filesize']     = 0;
    $coursefeedbackfile['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/mod_coursefeedback/content/' . $filename, true);
    $coursefeedbackfile['timecreated']  = null;
    $coursefeedbackfile['timemodified'] = $coursefeedback->timemodified;
    // make this file as main file
    $coursefeedbackfile['sortorder']    = 1;
    $coursefeedbackfile['userid']       = null;
    $coursefeedbackfile['author']       = null;
    $coursefeedbackfile['license']      = null;
    $contents[] = $coursefeedbackfile;

    return $contents;
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
function coursefeedback_dndupload_register() {
    return array('types' => array(
                     array('identifier' => 'text/html', 'message' => get_string('createcoursefeedback', 'coursefeedback')),
                     array('identifier' => 'text', 'message' => get_string('createcoursefeedback', 'coursefeedback'))
                 ));
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function coursefeedback_dndupload_handle($uploadinfo) {
    // Gather the required info.
    $data = new stdClass();
    $data->course = $uploadinfo->course->id;
    $data->name = $uploadinfo->displayname;
    $data->intro = '<p>'.$uploadinfo->displayname.'</p>';
    $data->introformat = FORMAT_HTML;
    if ($uploadinfo->type == 'text/html') {
        $data->contentformat = FORMAT_HTML;
        $data->content = clean_param($uploadinfo->content, PARAM_CLEANHTML);
    } else {
        $data->contentformat = FORMAT_PLAIN;
        $data->content = clean_param($uploadinfo->content, PARAM_TEXT);
    }
    $data->coursemodule = $uploadinfo->coursemodule;

    // Set the display options to the site defaults.
    $config = get_config('coursefeedback');
    $data->display = $config->display;
    $data->popupheight = $config->popupheight;
    $data->popupwidth = $config->popupwidth;
    $data->printintro = $config->printintro;
    $data->printlastmodified = $config->printlastmodified;

    return coursefeedback_add_instance($data, null);
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $coursefeedback       coursefeedback object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function coursefeedback_view($coursefeedback, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $coursefeedback->id
    );

    $event = \mod_coursefeedback\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('coursefeedback', $coursefeedback);
    $event->trigger();

    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Check if the module has any update that affects the current user since a given time.
 *
 * @param  cm_info $cm course module data
 * @param  int $from the time to check updates from
 * @param  array $filter  if we need to check only specific updates
 * @return stdClass an object with the different type of areas indicating if they were updated or not
 * @since Moodle 3.2
 */
function coursefeedback_check_updates_since(cm_info $cm, $from, $filter = array()) {
    $updates = course_check_module_updates_since($cm, $from, array('content'), $filter);
    return $updates;
}

/**
 * This function receives a calendar event and returns the action associated with it, or null if there is none.
 *
 * This is used by block_myoverview in order to display the event appropriately. If null is returned then the event
 * is not displayed on the block.
 *
 * @param calendar_event $event
 * @param \core_calendar\action_factory $factory
 * @return \core_calendar\local\event\entities\action_interface|null
 */
function mod_coursefeedback_core_calendar_provide_event_action(calendar_event $event,
                                                      \core_calendar\action_factory $factory, $userid = 0) {
    global $USER;

    if (empty($userid)) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['coursefeedback'][$event->instance];

    $completion = new \completion_info($cm->get_course());

    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/coursefeedback/view.php', ['id' => $cm->id]),
        1,
        true
    );
}

/**
 * Given an array with a file path, it returns the itemid and the filepath for the defined filearea.
 *
 * @param  string $filearea The filearea.
 * @param  array  $args The path (the part after the filearea and before the filename).
 * @return array The itemid and the filepath inside the $args path, for the defined filearea.
 */
function mod_coursefeedback_get_path_from_pluginfile(string $filearea, array $args) : array {
    // Page never has an itemid (the number represents the revision but it's not stored in database).
    array_shift($args);

    // Get the filepath.
    if (empty($args)) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    return [
        'itemid' => 0,
        'filepath' => $filepath,
    ];
}

function coursefeedback_insert_question($data, $feedbackid, $cmid, $courseid) {
    global $DB, $USER;

    $record = new stdClass();

    $record->feedbackid = $feedbackid;
    $record->cmid = $cmid;
    $record->courseid = $courseid;
    $record->question = $data->question;
    $record->type = $data->type;
    $record->createdby = $USER->id;
    $record->timecreated = time();
    $record->timemodified = time();

    return $DB->insert_record('coursefeedback_questions', $record);


}

function coursefeedback_update_question($data, $feedbackid, $cmid, $courseid, $questionid) {
    global $DB, $USER;

    $record = new stdClass();
    $record = $DB->get_record('coursefeedback_questions', array('id' => $questionid));
    $record->id = $data->id;
    $record->feedbackid = $feedbackid;
    $record->cmid = $cmid;
    $record->courseid = $courseid;
    $record->question = $data->question;
    $record->type = $data->type;
    $record->createdby = $USER->id;
    $record->timemodified = time();

    $DB->update_record('coursefeedback_questions', $record);
    return $record->id;

}

function coursefeedback_get_questions($cmid, $feedbackid) {
    global $DB;
    $records = $DB->get_records('coursefeedback_questions', array('cmid' => $cmid, 'feedbackid' => $feedbackid));
    return $records;
}

function coursefeedback_get_responses($cmid, $feedbackid) {
    global $DB;
    $sql = "SELECT cr.id, cq.id as questionid, cq.feedbackid, cq.courseid, cq.cmid, cq.question, cq.type, cr.userid, u.username, u.firstname, u.lastname, cr.response
                FROM {coursefeedback_response} cr 
                LEFT JOIN {coursefeedback_questions} cq ON cq.id = cr.questionid
                LEFT JOIN {user} u ON cr.userid = u.id
                WHERE  feedbackid=:feedbackid AND cmid= :cmid ORDER BY u.id";
        
    $params = [
        'feedbackid' => $feedbackid,
        'cmid' => $cmid
    ];

    $responses = $DB->get_records_sql($sql, $params);
    return $responses;
}