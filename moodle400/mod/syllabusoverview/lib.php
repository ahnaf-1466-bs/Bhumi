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
 * @package mod_syllabusoverview
 * @copyright 2021 Brain Station 23 LTD.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * List of features supported in syllabusoverview module
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function syllabusoverview_supports($feature) {
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
function syllabusoverview_reset_userdata($data) {

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
function syllabusoverview_get_view_actions() {
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
function syllabusoverview_get_post_actions() {
    return array('update', 'add');
}

/**
 * Add syllabusoverview instance.
 * @param stdClass $data
 * @param mod_syllabusoverview_mod_form $mform
 * @return int new syllabusoverview instance id
 */
function syllabusoverview_add_instance($data, $mform = null) {
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

    $data->id = $DB->insert_record('syllabusoverview', $data);

    // we need to use context now, so we need to make sure all needed info is already in db
    $DB->set_field('course_modules', 'instance', $data->id, array('id'=>$cmid));
    $context = context_module::instance($cmid);

    if ($mform and !empty($data->syllabusoverview['itemid'])) {
        $draftitemid = $data->syllabusoverview['itemid'];
        $data->content = file_save_draft_area_files($draftitemid, $context->id, 'mod_syllabusoverview', 'content', 0, syllabusoverview_get_editor_options($context), $data->content);
        $DB->update_record('syllabusoverview', $data);
    }

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'syllabusoverview', $data->id, $completiontimeexpected);

    return $data->id;
}

/**
 * Update syllabusoverview instance.
 * @param object $data
 * @param object $mform
 * @return bool true
 */
function syllabusoverview_update_instance($data, $mform) {
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

    $DB->update_record('syllabusoverview', $data);

    $context = context_module::instance($cmid);

    $completiontimeexpected = !empty($data->completionexpected) ? $data->completionexpected : null;
    \core_completion\api::update_completion_date_event($cmid, 'syllabusoverview', $data->id, $completiontimeexpected);

    return true;
}

/**
 * Delete syllabusoverview instance.
 * @param int $id
 * @return bool true
 */
function syllabusoverview_delete_instance($id) {
    global $DB;

    if (!$syllabusoverview = $DB->get_record('syllabusoverview', array('id'=>$id))) {
        return false;
    }

    $cm = get_coursemodule_from_instance('syllabusoverview', $id);
    \core_completion\api::update_completion_date_event($cm->id, 'syllabusoverview', $id, null);

    // note: all context files are deleted automatically

    $DB->delete_records('syllabusoverview', array('id'=>$syllabusoverview->id));

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
 * @return cached_cm_info Info to customise main syllabusoverview display
 */
function syllabusoverview_get_coursemodule_info($coursemodule) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if (!$syllabusoverview = $DB->get_record('syllabusoverview', array('id'=>$coursemodule->instance),
        'id, name, display, displayoptions, intro, introformat')) {
        return NULL;
    }

    $info = new cached_cm_info();
    $info->name = $syllabusoverview->name;

    if ($coursemodule->showdescription) {
        // Convert intro to html. Do not filter cached version, filters run at display time.
        $info->content = format_module_intro('syllabusoverview', $syllabusoverview, $coursemodule->id, false);
    }

    if ($syllabusoverview->display != RESOURCELIB_DISPLAY_POPUP) {
        return $info;
    }

    $fullurl = "$CFG->wwwroot/mod/syllabusoverview/view.php?id=$coursemodule->id&amp;inpopup=1";
    $options = empty($syllabusoverview->displayoptions) ? array() : unserialize($syllabusoverview->displayoptions);
    $width  = empty($options['popupwidth'])  ? 620 : $options['popupwidth'];
    $height = empty($options['popupheight']) ? 450 : $options['popupheight'];
    $wh = "width=$width,height=$height,toolbar=no,location=no,menubar=no,copyhistory=no,status=no,directories=no,scrollbars=yes,resizable=yes";
    $info->onclick = "window.open('$fullurl', '', '$wh'); return false;";

    return $info;
}


/**
 * Lists all browsable file areas
 *
 * @package  mod_syllabusoverview
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @return array
 */
function syllabusoverview_get_file_areas($course, $cm, $context) {
    $areas = array();
    $areas['content'] = get_string('content', 'mod_syllabusoverview');
    return $areas;
}

/**
 * File browsing support for syllabusoverview module content area.
 *
 * @package  mod_syllabusoverview
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
function syllabusoverview_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
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
        if (!$storedfile = $fs->get_file($context->id, 'mod_syllabusoverview', 'content', 0, $filepath, $filename)) {
            if ($filepath === '/' and $filename === '.') {
                $storedfile = new virtual_root_file($context->id, 'mod_syllabusoverview', 'content', 0);
            } else {
                // not found
                return null;
            }
        }
        require_once("$CFG->dirroot/mod/syllabusoverview/locallib.php");
        return new syllabusoverview_content_file_info($browser, $context, $storedfile, $urlbase, $areas[$filearea], true, true, true, false);
    }

    // note: syllabusoverview_intro handled in file_browser automatically

    return null;
}

/**
 * Serves the syllabusoverview files.
 *
 * @package  mod_syllabusoverview
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
function syllabusoverview_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG, $DB;
    require_once("$CFG->libdir/resourcelib.php");

    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);
    if (!has_capability('mod/syllabusoverview:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        // intro is handled automatically in pluginfile.php
        return false;
    }

    // $arg could be revision number or index.html
    $arg = array_shift($args);
    if ($arg == 'index.html' || $arg == 'index.htm') {
        // serve syllabusoverview content
        $filename = $arg;

        if (!$syllabusoverview = $DB->get_record('syllabusoverview', array('id'=>$cm->instance), '*', MUST_EXIST)) {
            return false;
        }

        // We need to rewrite the pluginfile URLs so the media filters can work.
        $content = file_rewrite_pluginfile_urls($syllabusoverview->content, 'webservice/pluginfile.php', $context->id, 'mod_syllabusoverview', 'content',
            $syllabusoverview->revision);
        $formatoptions = new stdClass;
        $formatoptions->noclean = true;
        $formatoptions->overflowdiv = true;
        $formatoptions->context = $context;

        // Remove @@PLUGINFILE@@/.
        $options = array('reverse' => true);
        $content = file_rewrite_pluginfile_urls($content, 'webservice/pluginfile.php', $context->id, 'mod_syllabusoverview', 'content',
            $syllabusoverview->revision, $options);
        $content = str_replace('@@PLUGINFILE@@/', '', $content);

        send_file($content, $filename, 0, 0, true, true);
    } else {
        $fs = get_file_storage();
        $relativepath = implode('/', $args);
        $fullpath = "/$context->id/mod_syllabusoverview/$filearea/0/$relativepath";
        if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
            $syllabusoverview = $DB->get_record('syllabusoverview', array('id'=>$cm->instance), 'id, legacyfiles', MUST_EXIST);
            if ($syllabusoverview->legacyfiles != RESOURCELIB_LEGACYFILES_ACTIVE) {
                return false;
            }
            if (!$file = resourcelib_try_file_migration('/'.$relativepath, $cm->id, $cm->course, 'mod_syllabusoverview', 'content', 0)) {
                return false;
            }
            //file migrate - update flag
            $syllabusoverview->legacyfileslast = time();
            $DB->update_record('syllabusoverview', $syllabusoverview);
        }

        // finally send the file
        send_stored_file($file, null, 0, $forcedownload, $options);
    }
}

/**
 * Return a list of syllabusoverview types
 * @param string $syllabusoverviewtype current syllabusoverview type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 */
function syllabusoverview_page_type_list($syllabusoverviewtype, $parentcontext, $currentcontext) {
    $module_syllabusoverviewtype = array('mod-syllabusoverview-*'=>get_string('syllabusoverview-mod-syllabusoverview-x', 'mod_syllabusoverview'));
    return $module_syllabusoverviewtype;
}

/**
 * Export syllabusoverview resource contents
 *
 * @return array of file content
 */
function syllabusoverview_export_contents($cm, $baseurl) {
    global $CFG, $DB;
    $contents = array();
    $context = context_module::instance($cm->id);

    $syllabusoverview = $DB->get_record('syllabusoverview', array('id'=>$cm->instance), '*', MUST_EXIST);

    // syllabusoverview contents
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_syllabusoverview', 'content', 0, 'sortorder DESC, id ASC', false);
    foreach ($files as $fileinfo) {
        $file = array();
        $file['type']         = 'file';
        $file['filename']     = $fileinfo->get_filename();
        $file['filepath']     = $fileinfo->get_filepath();
        $file['filesize']     = $fileinfo->get_filesize();
        $file['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/mod_syllabusoverview/content/'.$syllabusoverview->revision.$fileinfo->get_filepath().$fileinfo->get_filename(), true);
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

    // syllabusoverview html conent
    $filename = 'index.html';
    $syllabusoverviewfile = array();
    $syllabusoverviewfile['type']         = 'file';
    $syllabusoverviewfile['filename']     = $filename;
    $syllabusoverviewfile['filepath']     = '/';
    $syllabusoverviewfile['filesize']     = 0;
    $syllabusoverviewfile['fileurl']      = file_encode_url("$CFG->wwwroot/" . $baseurl, '/'.$context->id.'/mod_syllabusoverview/content/' . $filename, true);
    $syllabusoverviewfile['timecreated']  = null;
    $syllabusoverviewfile['timemodified'] = $syllabusoverview->timemodified;
    // make this file as main file
    $syllabusoverviewfile['sortorder']    = 1;
    $syllabusoverviewfile['userid']       = null;
    $syllabusoverviewfile['author']       = null;
    $syllabusoverviewfile['license']      = null;
    $contents[] = $syllabusoverviewfile;

    return $contents;
}

/**
 * Register the ability to handle drag and drop file uploads
 * @return array containing details of the files / types the mod can handle
 */
function syllabusoverview_dndupload_register() {
    return array('types' => array(
        array('identifier' => 'text/html', 'message' => get_string('createpage', 'mod_syllabusoverview')),
        array('identifier' => 'text', 'message' => get_string('createpage', 'mod_syllabusoverview'))
    ));
}

/**
 * Handle a file that has been uploaded
 * @param object $uploadinfo details of the file / content that has been uploaded
 * @return int instance id of the newly created mod
 */
function syllabusoverview_dndupload_handle($uploadinfo) {
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
    $config = get_config('syllabusoverview');
    $data->display = $config->display;
    $data->popupheight = $config->popupheight;
    $data->popupwidth = $config->popupwidth;
    $data->printheading = $config->printheading;
    $data->printintro = $config->printintro;
    $data->printlastmodified = $config->printlastmodified;

    return syllabusoverview_add_instance($data, null);
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param  stdClass $syllabusoverview       syllabusoverview object
 * @param  stdClass $course     course object
 * @param  stdClass $cm         course module object
 * @param  stdClass $context    context object
 * @since Moodle 3.0
 */
function syllabusoverview_view($syllabusoverview, $course, $cm, $context) {

    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $syllabusoverview->id
    );

    $event = \mod_syllabusoverview\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('syllabusoverview', $syllabusoverview);
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
function syllabusoverview_check_updates_since(cm_info $cm, $from, $filter = array()) {
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
function mod_syllabusoverview_core_calendar_provide_event_action(calendar_event $event,
                                                                 \core_calendar\action_factory $factory, $userid = 0) {
    global $USER;
    if (empty($userid)) {
        $userid = $USER->id;
    }

    $cm = get_fast_modinfo($event->courseid, $userid)->instances['syllabusoverview'][$event->instance];
    $completion = new \completion_info($cm->get_course());
    $completiondata = $completion->get_data($cm, false, $userid);

    if ($completiondata->completionstate != COMPLETION_INCOMPLETE) {
        return null;
    }

    return $factory->create_instance(
        get_string('view'),
        new \moodle_url('/mod/syllabusoverview/view.php', ['id' => $cm->id]),
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
function mod_syllabusoverview_get_path_from_pluginfile(string $filearea, array $args) : array {
    // syllabusoverview never has an itemid (the number represents the revision but it's not stored in database).
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
function mod_syllabusoverview_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    $itemid = array_shift($args);
    $filename = array_pop($args);

    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/' . implode('/', $args) . '/';
    }

    $fs = get_file_storage();

    $file = $fs->get_file($context->id, 'mod_syllabusoverview', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }
    send_stored_file($file, 0, 0, $forcedownload, $options);
}

function mod_syllabusoverview_get_image_url($userid) {
    $context = context_system::instance();

    $fs = get_file_storage();
    if ($files = $fs->get_area_files($context->id, 'mod_syllabusoverview', 'user_photo')) {

        foreach ($files as $file) {
            if ($userid == $file->get_itemid() && $file->get_filename() != '.') {
                // Build the File URL. Long process! But extremely accurate.
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), true);
                // Display the image
                $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . ':' . $fileurl->get_port() . $fileurl->get_path() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();
                return $download_url;
            }
        }
    }
    return false;
}

/**
 * Function for converting picture from draftid to URL
 */

function  mod_convert_image_to_url (int $courseid, stdClass $fromform) {
    $image = '';
    $context = context_system::instance();

    $filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);
    //adding a new feature
    file_save_draft_area_files( $fromform->content_pic, $context->id, 'mod_syllabusoverview', 'attachment',  $fromform->content_pic, $filemanageropts);

    if ($fromform->content_pic) {
        $fs = get_file_storage();

        if ($files = $fs->get_area_files($context->id, 'mod_syllabusoverview', 'attachment',  $fromform->content_pic, 'sortorder', false)) {
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

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function mod_get_picurl_with_content (int $courseid) {
    global $DB;
    $SQL = 'SELECT *
            FROM {mod_syllabusoverview_picurl} sp 
            JOIN {mod_syllabusoverview_feature} sf
            WHERE sp.course = sf.course AND 
                sp.draftid = sf.content_pic AND 
                sf.course = '. $courseid;

    $messages = $DB->get_records_sql($SQL, ['course' => $courseid]);

    return $messages;
}

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_content_benefit (int $courseid) {
    global $DB;
    $SQL = 'SELECT *
            FROM {syllabusoverview_benefiturl} sp 
            JOIN {syllabusoverview_benefitted} sf
            WHERE sp.course = sf.course AND 
                sp.draftid = sf.content_pic AND 
                sf.course = '. $courseid;

    $messages = $DB->get_records_sql($SQL, ['course' => $courseid]);

    return $messages;
}

/**
 * Function for converting pdf from draftid to URL
 */

function convert_pdf_to_url (int $courseid, stdClass $fromform) {
    $image = '';
    $context = context_system::instance();

    $filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);
    //adding a new feature
    file_save_draft_area_files( $fromform->syllabuspdf, $context->id, 'mod_syllabusoverview', 'attachment',  $fromform->syllabuspdf, $filemanageropts);

    if ($fromform->syllabuspdf) {
        $fs = get_file_storage();

        if ($files = $fs->get_area_files($context->id, 'mod_syllabusoverview', 'attachment',  $fromform->syllabuspdf, 'sortorder', false)) {
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

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_picurl_with_syllabuspdf (int $courseid) {
    global $DB;
    $SQL = 'SELECT *
            FROM {syllabusoverview_syll_pdfurl} sp 
            JOIN {syllabusoverview_syllabuspdf} sf
            WHERE sp.course = sf.course AND 
                sp.draftid = sf.syllabuspdf AND 
                sf.course = '. $courseid;

    $messages = $DB->get_records_sql($SQL, ['course' => $courseid]);

    return $messages;
}


/**
 * Function for converting picture from draftid to URL
 */

function convert_programpdf_to_url (int $courseid, stdClass $fromform) {
    $programpdf = '';
    $context = context_system::instance();
    $filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);

    //adding a new feature
    file_save_draft_area_files( $fromform->programpdf, $context->id, 'mod_syllabusoverview', 'attachment',  $fromform->programpdf, $filemanageropts);

    if ($fromform->programpdf) {
        $fs = get_file_storage();

        if ($files = $fs->get_area_files($context->id, 'mod_syllabusoverview', 'attachment',  $fromform->programpdf, 'sortorder', false)) {
            // Look through each file being managed
            foreach ($files as $file) {

                // Build the File URL. Long process! But extremely accurate.
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                // Display the image
                $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . ':' . $fileurl->get_port() . $fileurl->get_path() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();

                $programpdf = $download_url;

            }
        }
    }
    return $programpdf;
}


/**
 * Function for converting picture from draftid to URL
 */

function convert_description_to_url (int $courseid, stdClass $fromform) {
    $programpdf = '';
    $context = context_system::instance();
    $filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);

    //adding a new feature
    file_save_draft_area_files( $fromform->draftid, $context->id, 'mod_syllabusoverview', 'attachment',  $fromform->draftid, $filemanageropts);

    if ($fromform->draftid) {
        $fs = get_file_storage();

        if ($files = $fs->get_area_files($context->id, 'mod_syllabusoverview', 'attachment',  $fromform->draftid, 'sortorder', false)) {
            // Look through each file being managed
            foreach ($files as $file) {

                // Build the File URL. Long process! But extremely accurate.
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                // Display the image
                $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . ':' . $fileurl->get_port() . $fileurl->get_path() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();

                $programpdf = $download_url;

            }
        }
    }
    return $programpdf;
}

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_pdfurl_for_program (int $courseid) {
    global $DB;
    $SQL = 'SELECT *
            FROM {syllabusoverview_prog_pdfurl} sp 
            JOIN {syllabusoverview_programpdf} sf
            WHERE sp.course = sf.course AND 
                sp.draftid = sf.programpdf AND 
                sf.course = '. $courseid;

    $message = $DB->get_record_sql($SQL, ['course' => $courseid]);

    return $message;
}

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_program_title_and_data (int $courseid) {
    global $DB;
    $SQL = 'SELECT spt.id, spt.name, spt.name_bangla, spd.value, spd.value_bangla
            FROM {syllabusoverview_prog_title} spt 
            JOIN {syllabusoverview_prog_data} spd
            WHERE spt.course = spd.course AND 
                spt.id = spd.title_id AND 
                spt.course = '. $courseid;

    $message = $DB->get_records_sql($SQL, ['course' => $courseid]);

    return $message;
}

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_program_name_and_date (int $courseid) {
    global $DB;
    $SQL = 'SELECT sp.id, sp.programdate, spn.name
            FROM {syllabusoverview_program} sp 
            JOIN {syllabusoverview_prog_name} spn
            WHERE sp.course = spn.course AND 
                sp.prog_id = spn.id AND 
                sp.course = '. $courseid;

    $message = $DB->get_records_sql($SQL, ['course' => $courseid]);

    return $message;
}

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_url_for_description (int $courseid) {
    global $DB;
    $SQL = 'SELECT *
            FROM {syllabusoverview_description} sp 
            JOIN {syllabusoverview_descrip_url} sf
            WHERE sp.course = sf.course AND 
                sp.draftid = sf.draftid AND 
                sf.course = '. $courseid;

    $message = $DB->get_records_sql($SQL, ['course' => $courseid]);

    return $message;
}

/**
 * @param int $courseid
 * @return array
 * @throws dml_exception
 * For viewing the image link with content this function is used
 */

function get_url_for_courseimage (int $courseid) {
    global $DB;
    $SQL = 'SELECT *
            FROM {syllabusoverview_courseimg} sc 
            JOIN {syllabusoverview_img_url} siu
            WHERE sc.course = siu.course AND 
                sc.course_image = siu.draftid AND 
                siu.course = '. $courseid;

    $message = $DB->get_records_sql($SQL, ['course' => $courseid]);

    return $message;
}



/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_feature($courseid) {
    global $DB;
    $SQL_feature = 'SELECT sf.id, sf.content as feature_heading, sf.content_bangla as feature_heading_bangla, sp.picurl as feature_pic
                    FROM {mod_syllabusoverview_feature}  sf
                        JOIN {mod_syllabusoverview_picurl} sp
                    WHERE sf.course = sp.course AND sp.draftid=sf.content_pic 
                        AND sf.course = '.$courseid;

    $features = $DB->get_records_sql($SQL_feature, ['course' => $courseid]);
    return $features;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_course_image($courseid) {
    global $DB;

    $SQL_description = 'SELECT sdu.id, sdu.courseimgurl as courseimg
                        FROM {syllabusoverview_courseimg} sd
                        JOIN {syllabusoverview_img_url} sdu
                        WHERE sd.course = '.$courseid.' AND sd.course_image = sdu.draftid AND sd.course = sdu.course';

    $image = $DB->get_records_sql($SQL_description, ['course' => $courseid]);

    return $image;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_benefits($courseid) {
    global $DB;
    $SQL_benefits = 'SELECT sb.id, sb.content as beneficiary_name, sb.content_bangla as beneficiary_name_bangla, sbu.picurl as beneficiary_image
                        FROM {syllabusoverview_benefitted} sb
                            JOIN {syllabusoverview_benefiturl} sbu
                        WHERE sb.course = sbu.course AND sb.content_pic = sbu.draftid 
                            AND sb.course = '.$courseid;

    $benefits = $DB->get_records_sql($SQL_benefits, ['course' => $courseid]);
    return $benefits;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_description($courseid) {
    global $DB;

    $SQL_description = 'SELECT sd.id, sd.description as short_description, sd.description_bangla as short_description_bangla, sd.fileurl as file_url, sdu.descriptionurl as description_url
                        FROM {syllabusoverview_description} sd
                        JOIN {syllabusoverview_descrip_url} sdu
                        WHERE sd.course = '.$courseid.' AND sd.draftid = sdu.draftid AND sd.course = sdu.course';

    $description = $DB->get_records_sql($SQL_description, ['course' => $courseid]);

    return $description;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_learn($courseid) {
    global $DB;

    $SQL_learn = 'SELECT sl.id, sl.learn as learning, sl.learn_bangla as learning_bangla
                      FROM {syllabusoverview_learn} sl
                      WHERE sl.course = ' .$courseid;
    $learn = $DB->get_records_sql($SQL_learn, ['course' => $courseid]);

    return $learn;
}

/**
 * @param $courseid
 * @return false|mixed
 * @throws dml_exception
 */
function get_program_details($courseid) {
    global $DB;
    $SQL_program_details = 'SELECT spp.id, spp.programpdfpath as programpdf, 
                                spp.programstructure, spp.deadline,
                                spp.length, spp.fee
                                FROM {syllabusoverview_prog_pdfurl} spp
                                WHERE spp.course = '. $courseid;
    $program_details = $DB->get_record_sql($SQL_program_details, ['course' => $courseid]);

    return $program_details;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_program_name($courseid) {
    global $DB;
    $result = $DB->get_records('syllabusoverview_prog_name', ['course' => $courseid]);

    return $result;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_program_pdf($courseid) {
    global $DB;
    $result = $DB->get_record('syllabusoverview_prog_pdfurl', ['course' => $courseid]);
    $programname = $result->programpdfpath;

    return $programname;
}


/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_program_dt($courseid) {
    global $DB;
    $SQL_program_dt = 'SELECT msp.id,  msp.programdate, msp.programdate_bangla 
                           FROM {syllabusoverview_program} msp
                           WHERE  msp.course = '.$courseid;
    $program_dt = $DB->get_records_sql($SQL_program_dt, ['course' => $courseid]);

    return $program_dt;
}

/**
 * @param $courseid
 * @return false|mixed
 * @throws dml_exception
 */
function get_syll_url($courseid) {
    global $DB;
    $SQL_syll_url = 'SELECT ssp.id,  ssp.picurl as syllabuspdf
                        FROM {syllabusoverview_syll_pdfurl} ssp
                        WHERE ssp.course = '.$courseid;

    $syll_url = $DB->get_record_sql($SQL_syll_url, ['course' => $courseid]);
    return $syll_url;
}

/**
 * @param $courseid
 * @return array
 * @throws dml_exception
 */
function get_syll_details($courseid) {
    global $DB;
    $SQL_syll_details = 'SELECT  mss.id, mss.heading as syllabusheading, mss.heading_bangla as syllabusheading_bangla, mss.body as syllabusbody, mss.body_bangla as syllabusbody_bangla
                         FROM {syllabusoverview_syllabus} mss
                         WHERE mss.course = '.$courseid;

    $syll_details = $DB->get_records_sql($SQL_syll_details, ['course' => $courseid]);
    return $syll_details;
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_benefiturl (int $courseid, stdClass $fromform, int $messageid) {
    global $DB;
    $image = mod_convert_image_to_url($courseid, $fromform);
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->picurl = $image;
    $object->draftid = $fromform->content_pic;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_benefiturl', $object, ['course' => $courseid, 'id' => $messageid]);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_benefiturl(int $courseid, stdClass $fromform)
{
    global $DB;
    $image = mod_convert_image_to_url($courseid, $fromform);
    $object = new stdClass();
    $object->course = $courseid;
    $object->picurl = $image;
    $object->draftid = $fromform->content_pic;
    $object->timemodified = time();

    $DB->insert_record('syllabusoverview_benefiturl', $object);
}

/**
 * @param $courseid
 * @param $fromform
 * @param $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_benefitted(int $courseid, stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->benefit_id = $messageid;
    $object->content_pic = $fromform->content_pic;
    $object->content = $fromform->content;
    $object->content_bangla = $fromform->content_bangla;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_benefitted', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_benefitted(int $courseid, stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->benefit_id = $fromform->benefit_id;
    $record_to_insert->content_pic = $fromform->content_pic;
    $record_to_insert->content = $fromform->content;
    $record_to_insert->content_bangla = $fromform->content_bangla;
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_benefitted', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_feature(int $courseid, stdClass $fromform, int $messageid) {

    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->feature_id = $messageid;
    $object->content_pic = $fromform->content_pic;
    $object->content = $fromform->content;
    $object->content_bangla = $fromform->content_bangla;
    $object->timemodified = time();
    $DB->update_record('mod_syllabusoverview_feature', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_feature_picurl (int $courseid, stdClass $fromform, int $messageid) {
    global $DB;
    $image =  mod_convert_image_to_url($courseid, $fromform);
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->picurl = $image;
    $object->draftid = $fromform->content_pic;
    $object->timemodified = time();

    $DB->update_record('mod_syllabusoverview_picurl', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_feature(int $courseid, stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->feature_id = $fromform->feature_id;
    $record_to_insert->content_pic = $fromform->content_pic;
    $record_to_insert->content = $fromform->content;
    $record_to_insert->content_bangla = $fromform->content_bangla;
    $record_to_insert->timemodified = time();

    $DB->insert_record('mod_syllabusoverview_feature', $record_to_insert, false);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_feature_picurl(int $courseid, stdClass $fromform) {
    global $DB;
    $image =  mod_convert_image_to_url($courseid, $fromform);
    $object = new stdClass();
    $object->course = $courseid;
    $object->picurl = $image;
    $object->draftid = $fromform->content_pic;
    $object->timemodified = time();

    $DB->insert_record('mod_syllabusoverview_picurl', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_description (int $courseid, stdClass $fromform, int $messageid){
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->description = $fromform->description;
    $object->fileurl = $fromform->fileurl;
    $object->description_bangla = $fromform->description_bangla;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_description', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_description_url (int $courseid, stdClass $fromform, int $messageid){
    global $DB;

    $descriptionpath = convert_description_to_url($courseid, $fromform);

    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->draftid = $fromform->draftid;
    $object->descriptionurl = $descriptionpath;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_descrip_url', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_description (int $courseid, stdClass $fromform){
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->description = $fromform->description;
    $record_to_insert->fileurl = $fromform->fileurl;
    $record_to_insert->description_bangla = $fromform->description_bangla;
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_description', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_description_url (int $courseid, stdClass $fromform){
    global $DB;

    $descriptionpath = convert_description_to_url($courseid, $fromform);

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->draftid = $fromform->draftid;
    $record_to_insert->descriptionurl = $descriptionpath;
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_descrip_url', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_syllabus(int $courseid, stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->heading = $fromform->heading;
    $object->heading_bangla = $fromform->heading_bangla;
    $object->body = $fromform->body['text'];
    $object->body_bangla = $fromform->body_bangla['text'];
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_syllabus', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_syllabus(int $courseid, stdClass $fromform) {
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->heading = $fromform->heading;
    $record_to_insert->heading_bangla = $fromform->heading_bangla;
    $record_to_insert->body = $fromform->body['text'];
    $record_to_insert->body_bangla = $fromform->body_bangla['text'];
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_syllabus', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */

function update_record_syllabuspdf(int $courseid, stdClass $fromform, int $messageid){
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->syllabuspdf = $fromform->syllabuspdf;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_syllabuspdf', $object, ['course' => $courseid, 'id' => $messageid]);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_syllabus_pdfurl(int $courseid, stdClass $fromform, int $messageid) {
    global $DB;
    $image = convert_pdf_to_url($courseid, $fromform);
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->picurl = $image;
    $object->draftid = $fromform->syllabuspdf;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_syll_pdfurl', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_syllabuspdf(int $courseid, stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->syllabuspdf = $fromform->syllabuspdf;
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_syllabuspdf', $record_to_insert, false);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */

function insert_record_syllabus_pdfurl(int $courseid, stdClass $fromform) {
    global $DB;
    $image = convert_pdf_to_url($courseid, $fromform);
    $object = new stdClass();
    $object->course = $courseid;
    $object->picurl = $image;
    $object->draftid = $fromform->syllabuspdf;
    $object->timemodified = time();

    $DB->insert_record('syllabusoverview_syll_pdfurl', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_learn(int $courseid, stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->learn = $fromform->learn['text'];
    $object->learn_bangla = $fromform->learn_bangla['text'];
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_learn', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_learn(int $courseid, stdClass $fromform) {
    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->learn = $fromform->learn['text'];
    $record_to_insert->learn_bangla = $fromform->learn_bangla['text'];
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_learn', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_program(int $courseid, stdClass $fromform, int $messageid, int $dateid) {
    global $DB;
    $object = new stdClass();
    $object->id = $dateid;
    $object->course = $courseid;
    $object->prog_id = $messageid;
    $object->programdate = $fromform->programdate;
    $object->programdate_bangla = $fromform->programdate_bangla;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_program', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_programname(int $courseid, stdClass $fromform, int $messageid) {
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->name = $fromform->name;
    $object->name_bangla = $fromform->name_bangla;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_prog_name', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_program(int $courseid, int $programid, stdClass $fromform) {
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->prog_id = $programid;
    $record_to_insert->programdate = $fromform->programdate;
    $record_to_insert->programdate_bangla = $fromform->programdate_bangla;
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_program', $record_to_insert, false);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_programname(int $courseid, stdClass $fromform) {
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->name = $fromform->name;
    $record_to_insert->name_bangla = $fromform->name_bangla;
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_prog_name', $record_to_insert, false);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_programpdf(int $courseid, stdClass $fromform, int $messageid) {

    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->programpdf = $fromform->programpdf;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_programpdf', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_program_pdfurl(int $courseid, stdClass $fromform, int $messageid) {

    global $DB;
    $programpdfpath = convert_programpdf_to_url($courseid, $fromform);

    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->programpdfpath = $programpdfpath;
    $object->draftid = $fromform->programpdf;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_prog_pdfurl', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_programpdf(int $courseid, stdClass $fromform) {

    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->programpdf = $fromform->programpdf;
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_programpdf', $record_to_insert, false);

}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_prog_title(int $courseid, stdClass $fromform) {

    global $DB;
    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->name = $fromform->name;
    $record_to_insert->name_bangla = $fromform->name_bangla;
    $record_to_insert->timemodified = time();

    $titleid = $DB->insert_record('syllabusoverview_prog_title', $record_to_insert);

    $object = new stdClass();
    $object->course = $courseid;
    $object->title_id = $titleid;
    $object->value = $fromform->value;
    $object->value_bangla = $fromform->value_bangla;
    $object->timemodified = time();

    $DB->insert_record('syllabusoverview_prog_data', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_prog_title(int $courseid, stdClass $fromform, int $messageid) {

    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->name = $fromform->name;
    $object->name_bangla = $fromform->name_bangla;
    $object->timemodified = time();

    $titleid = $DB->update_record('syllabusoverview_prog_title', $object, ['course' => $courseid, 'id' => $messageid]);

    $object1 = new stdClass();
    $object1->id = $messageid;
    $object1->course = $courseid;
    $object1->title_id = $messageid;
    $object1->value = $fromform->value;
    $object1->value_bangla = $fromform->value_bangla;
    $object1->timemodified = time();

    $DB->update_record('syllabusoverview_prog_data', $object1);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_program_pdfurl(int $courseid, stdClass $fromform) {

    global $DB;
    $programpdfpath = convert_programpdf_to_url($courseid, $fromform);

    $object = new stdClass();
    $object->course = $courseid;
    $object->programpdfpath = $programpdfpath;
    $object->draftid = $fromform->programpdf;
    $object->timemodified = time();

    $DB->insert_record('syllabusoverview_prog_pdfurl', $object);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_courseimage (int $courseid, stdClass $fromform){
    global $DB;

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->course_image = $fromform->course_image;
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_courseimg', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @return void
 * @throws dml_exception
 */
function insert_record_courseimage_url (int $courseid, stdClass $fromform){
    global $DB;
    $descriptionpath = mod_syllabusoverview_get_course_image_url($courseid, $fromform);

    $record_to_insert = new stdClass();
    $record_to_insert->course = $courseid;
    $record_to_insert->courseimgurl = $descriptionpath;
    $record_to_insert->draftid = $fromform->course_image;
    $record_to_insert->timemodified = time();

    $DB->insert_record('syllabusoverview_img_url', $record_to_insert, false);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_courseimage (int $courseid, stdClass $fromform, int $messageid){
    global $DB;
    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_courseimg', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * @param int $courseid
 * @param stdClass $fromform
 * @param int $messageid
 * @return void
 * @throws dml_exception
 */
function update_record_courseimage_url (int $courseid, stdClass $fromform, int $messageid){
    global $DB;

    $descriptionpath = mod_syllabusoverview_get_course_image_url($courseid, $fromform);

    $object = new stdClass();
    $object->id = $messageid;
    $object->course = $courseid;
    $object->courseimgurl = $descriptionpath;
    $object->draftid = $fromform->course_image;
    $object->timemodified = time();

    $DB->update_record('syllabusoverview_img_url', $object, ['course' => $courseid, 'id' => $messageid]);
}

/**
 * Function for converting course image from draftid to URL
 */

function mod_syllabusoverview_get_course_image_url(int $courseid, stdClass $fromform) {
    $programpdf = '';
    $context = context_system::instance();
    $filemanageropts = array('subdirs' => 0, 'maxbytes' => '0', 'maxfiles' => 50, 'context' => $context);

    //adding a new feature
    file_save_draft_area_files( $fromform->course_image, $context->id, 'mod_syllabusoverview', 'attachment', $fromform->course_image, $filemanageropts);

    if ($fromform->course_image) {
        $fs = get_file_storage();

        if ($files = $fs->get_area_files($context->id, 'mod_syllabusoverview', 'attachment',  $fromform->course_image, 'sortorder', false)) {
            // Look through each file being managed
            foreach ($files as $file) {

                // Build the File URL. Long process! But extremely accurate.
                $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
                // Display the image
                $download_url = $fileurl->get_port() ? $fileurl->get_scheme() . '://' . $fileurl->get_host() . ':' . $fileurl->get_port() . $fileurl->get_path() : $fileurl->get_scheme() . '://' . $fileurl->get_host() . $fileurl->get_path();

                return $download_url;

            }
        }
    }
    return false;
}

function syllabusoverview_get_details (int $coursid) {
    global $DB;
    $sql = "SELECT *
            FROM {syllabusoverview_prog_name} spn 
            JOIN {syllabusoverview_program} sp
            WHERE spn.course = sp.course AND spn.id = sp.prog_id
            AND spn.course = ". $coursid;
    $message = $DB->get_records_sql($sql);
    return $message;
}