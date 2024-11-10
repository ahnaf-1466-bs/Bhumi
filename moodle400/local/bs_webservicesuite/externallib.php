<?php
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
 * External Web Service Template
 * @package local
 * @subpackage bs_webservicesuite
 * @author     Brain station 23 ltd <brainstation-23.com>
 * @copyright  2023 Brain station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->dirroot . "/course/externallib.php");

use core_completion\progress;

class local_bs_webservicesuite_external extends core_course_external {
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_instructor_details_by_courseid_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course ID')
            )
        );
    }
    /**
     * Get Course completion status
     *
     * @param int $courseid ID of the Course
     * @param int $userid ID of the User
     * @return array of course completion status and warnings
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_instructor_details_by_courseid($courseid) {
        global $CFG, $USER, $DB;
        require_once($CFG->libdir . '/grouplib.php');
        require_once($CFG->dirroot . "/user/lib.php");

        $arrayparams = array(
            'courseid' => $courseid
        );
        $params = self::validate_parameters(self::get_instructor_details_by_courseid_parameters(), $arrayparams);

        // Get all the list of teachers (Role id = 3) of a specific course.
        $sql = "SELECT distinct u.*
                FROM    {course} as c, 
                        {role_assignments} AS ra, 
                        {user} AS u, 
                        {context} AS ct
                WHERE   c.id = ct.instanceid 
                        AND ra.roleid =3  
                        AND ra.userid = u.id 
                        AND ct.id = ra.contextid
                        AND c.id = ". $courseid;
        
        $users = $DB->get_records_sql($sql);

        $context = context_system::instance();
        self::validate_context($context);

        // Finally retrieve each users information (Also inscludes custom profile fields)
        $returnedusers = array();
        foreach ($users as $user) {
            $userdetails = user_get_user_details_courses($user);
            
            $returnedusers[] = $userdetails;

            // Return the user only if the searched field is returned.
            // Otherwise it means that the $USER was not allowed to search the returned user.
            if (!empty($userdetails) and !empty($userdetails[$field])) {
                $returnedusers[] = $userdetails;
            }
        }
        return array(
            'users' => $returnedusers,
            'warnings' => array()
        );

    }
    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_instructor_details_by_courseid_returns() {
        return new external_single_structure(
            array('users' => new external_multiple_structure(
                                self::user_description()
                             ),
                  'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
            )
        );
    }

    /**
     * Create user return value description.
     *
     * @param array $additionalfields some additional field
     * @return single_structure_description
     */
    public static function user_description($additionalfields = array()) {
        $userfields = array(
            'id'    => new external_value(core_user::get_property_type('id'), 'ID of the user'),
            'username'    => new external_value(core_user::get_property_type('username'), 'The username', VALUE_OPTIONAL),
            'firstname'   => new external_value(core_user::get_property_type('firstname'), 'The first name(s) of the user', VALUE_OPTIONAL),
            'lastname'    => new external_value(core_user::get_property_type('lastname'), 'The family name of the user', VALUE_OPTIONAL),
            'email'       => new external_value(core_user::get_property_type('email'), 'An email address - allow email as root@localhost', VALUE_OPTIONAL),
            'address'     => new external_value(core_user::get_property_type('address'), 'Postal address', VALUE_OPTIONAL),
            'phone1'      => new external_value(core_user::get_property_type('phone1'), 'Phone 1', VALUE_OPTIONAL),
            'phone2'      => new external_value(core_user::get_property_type('phone2'), 'Phone 2', VALUE_OPTIONAL),
            'department'  => new external_value(core_user::get_property_type('department'), 'department', VALUE_OPTIONAL),
            'institution' => new external_value(core_user::get_property_type('institution'), 'institution', VALUE_OPTIONAL),
            'idnumber'    => new external_value(core_user::get_property_type('idnumber'), 'An arbitrary ID code number perhaps from the institution', VALUE_OPTIONAL),
            'interests'   => new external_value(PARAM_TEXT, 'user interests (separated by commas)', VALUE_OPTIONAL),
            'firstaccess' => new external_value(core_user::get_property_type('firstaccess'), 'first access to the site (0 if never)', VALUE_OPTIONAL),
            'lastaccess'  => new external_value(core_user::get_property_type('lastaccess'), 'last access to the site (0 if never)', VALUE_OPTIONAL),
            'auth'        => new external_value(core_user::get_property_type('auth'), 'Auth plugins include manual, ldap, etc', VALUE_OPTIONAL),
            'suspended'   => new external_value(core_user::get_property_type('suspended'), 'Suspend user account, either false to enable user login or true to disable it', VALUE_OPTIONAL),
            'confirmed'   => new external_value(core_user::get_property_type('confirmed'), 'Active user: 1 if confirmed, 0 otherwise', VALUE_OPTIONAL),
            'lang'        => new external_value(core_user::get_property_type('lang'), 'Language code such as "en", must exist on server', VALUE_OPTIONAL),
            'calendartype' => new external_value(core_user::get_property_type('calendartype'), 'Calendar type such as "gregorian", must exist on server', VALUE_OPTIONAL),
            'theme'       => new external_value(core_user::get_property_type('theme'), 'Theme name such as "standard", must exist on server', VALUE_OPTIONAL),
            'timezone'    => new external_value(core_user::get_property_type('timezone'), 'Timezone code such as Australia/Perth, or 99 for default', VALUE_OPTIONAL),
            'mailformat'  => new external_value(core_user::get_property_type('mailformat'), 'Mail format code is 0 for plain text, 1 for HTML etc', VALUE_OPTIONAL),
            'description' => new external_value(core_user::get_property_type('description'), 'User profile description', VALUE_OPTIONAL),
            'descriptionformat' => new external_format_value(core_user::get_property_type('descriptionformat'), VALUE_OPTIONAL),
            'city'        => new external_value(core_user::get_property_type('city'), 'Home city of the user', VALUE_OPTIONAL),
            'country'     => new external_value(core_user::get_property_type('country'), 'Home country code of the user, such as AU or CZ', VALUE_OPTIONAL),
            'profileimageurlsmall' => new external_value(PARAM_URL, 'User image profile URL - small version'),
            'profileimageurl' => new external_value(PARAM_URL, 'User image profile URL - big version'),
            'customfields' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'type'  => new external_value(PARAM_ALPHANUMEXT, 'The type of the custom field - text field, checkbox...'),
                        'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                        'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                        'shortname' => new external_value(PARAM_RAW, 'The shortname of the custom field - to be able to build the field class in the code'),
                    )
                ), 'User custom fields (also known as user profile fields)', VALUE_OPTIONAL),
            'preferences' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'name'  => new external_value(PARAM_RAW, 'The name of the preferences'),
                        'value' => new external_value(PARAM_RAW, 'The value of the preference'),
                    )
            ), 'Users preferences', VALUE_OPTIONAL)
        );
        if (!empty($additionalfields)) {
            $userfields = array_merge($userfields, $additionalfields);
        }
        return new external_single_structure($userfields);
    }

    /**
     * Returns description of method get_popular_courses parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_popular_courses_parameters() {
        return new external_function_parameters(
            array()
        );
    }
    /**
     * Get list of popular courses by order of popularity.
     *
     * @return array List of popular courses and warnings.
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_popular_courses() {
        global $DB;

        $arrayparams = array();
        $params = self::validate_parameters(self::get_popular_courses_parameters(), $arrayparams);

        $sql = "SELECT c.id, c.fullname, c.summary, c.timemodified
                FROM {course} AS c
                JOIN {enrol} AS en ON en.courseid = c.id
                JOIN {user_enrolments} AS ue ON ue.enrolid = en.id
                GROUP BY c.id
                ORDER BY COUNT(ue.id) DESC";
        
        $courserecords = $DB->get_recordset_sql($sql);
        $courses = [];
        foreach($courserecords as $course) {
            $coursecontext = context_course::instance($course->id);
            $courses[$course->id] = self::get_course_public_information(new \core_course_list_element($course), $coursecontext);
            
        }
        
        return array(
            'popular_courses' => $courses,
            'warnings' => array()
        );

    }
    /**
     * Returns description of get_popular_courses value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_popular_courses_returns() {
        // Course structure, including not only public viewable fields.
        
        return new external_single_structure(
            array('popular_courses' => new external_multiple_structure(self::get_course_structure(), 'Course'),
                  'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
            )
        );
    }

    /**
     * Returns description of method get_future_courses parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_future_courses_parameters() {
        return new external_function_parameters(
            array()
        );
    }

    /**
     * Get list of future courses.
     *
     * @return array List of popular courses and warnings.
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_future_courses () {
        global $CFG;
        require_once($CFG->dirroot . '/local/bs_webservicesuite/lib.php');

        $arrayparams = array();
        self::validate_parameters(
            self::get_future_courses_parameters(),
            $arrayparams
        );

        $courserecords = future_course_by_date();
        $courses = [];
        foreach($courserecords as $course) {
            $coursecontext = context_course::instance($course->id);
            $courses[$course->id] = self::get_course_public_information(new \core_course_list_element($course), $coursecontext);
        }

        return array(
            'future_courses' => $courses,
            'warnings' => array()
        );

    }

    /**
     * Returns description of get_future_courses value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_future_courses_returns() {
        // Course structure, including not only public viewable fields.
        return new external_single_structure(
            array(
                'future_courses' => new external_multiple_structure(self::get_course_structure(), 'Course'),
                'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
            )
        );
    }


    /**
     * Returns description of method get_past_courses parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_past_courses_parameters() {
        return new external_function_parameters(
            array()
        );
    }

    /**
     * Get list of past courses by date.
     *
     * @return array List of past courses and warnings.
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_past_courses () {
        global $CFG;
        require_once($CFG->dirroot . '/local/bs_webservicesuite/lib.php');

        $arrayparams = array();
        self::validate_parameters(self::get_past_courses_parameters(), $arrayparams);

        $courserecords = past_course_by_date();

        $courses = [];
        foreach($courserecords as $course) {
            $coursecontext = context_course::instance($course->id);
            $courses[$course->id] = self::get_course_public_information(new \core_course_list_element($course), $coursecontext);
        }

        return array(
            'past_courses' => $courses,
            'warnings' => array()
        );

    }

    /**
     * Returns description of get_past_courses value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_past_courses_returns() {
        // Course structure, including not only public viewable fields.

        return new external_single_structure(
            array(
                'past_courses' => new external_multiple_structure(self::get_course_structure(), 'Course'),
                'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
            )
        );
    }


    /**
     * Returns description of method get_user_past_courses parameters.
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_user_past_courses_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'User ID')
            )
        );
    }

    /**
     * Get list of past courses of users.
     *
     * @return array List of past courses and warnings.
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_user_past_courses ($userid) {
        global $CFG;
        require_once($CFG->dirroot . '/local/bs_webservicesuite/lib.php');
        // Parameter validation.
        self::validate_parameters(
            self::get_user_past_courses_parameters(),
            array(
                'userid' => $userid,
            )
        );
        $courserecords = past_course_of_user($userid);

        $courses = [];
        foreach($courserecords as $course) {
            $coursecontext = context_course::instance($course->id);
            $courses[$course->id] = self::get_course_public_information(new \core_course_list_element($course), $coursecontext);
        }

        return array(
            'user_past_courses' => $courses,
            'warnings' => array()
        );

    }

    /**
     * Returns description of get_past_courses value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_user_past_courses_returns() {
        // Course structure, including not only public viewable fields.

        return new external_single_structure(
            array(
                'user_past_courses' => new external_multiple_structure(self::get_course_structure(), 'Course'),
                'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
            )
        );
    }
    
    
    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 2.9
     */
    public static function get_course_details_with_instructor_parameters()
    {
        return new external_function_parameters(
            array(
                'field' => new external_value(PARAM_ALPHA, 'The field to search can be left empty for all courses or:
                    id: course id
                    ids: comma separated course ids
                    shortname: course short name
                    idnumber: course id number
                    category: category id the course belongs to
                ', VALUE_DEFAULT, ''),
                'value' => new external_value(PARAM_RAW, 'The value to match', VALUE_DEFAULT, '')
            )
        );
    }

    /**
     * @param $field
     * @param $value
     * @return array
     * @throws coding_exception
     * @throws dml_exception
     * @throws invalid_parameter_exception
     * @throws restricted_context_exception
     */
    public static function get_course_details_with_instructor ($field = '', $value = '') {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->libdir . '/filterlib.php');
        require_once($CFG->libdir . '/grouplib.php');
        require_once($CFG->dirroot . "/user/lib.php");

        $params = self::validate_parameters(self::get_course_details_with_instructor_parameters(),
            array(
                'field' => $field,
                'value' => $value,
            )
        );
        $warnings = array();

        if (empty($params['field'])) {
            $courses = $DB->get_records('course', null, 'sortorder ASC');
        } else {
            switch ($params['field']) {
                case 'id':
                case 'category':
                    $value = clean_param($params['value'], PARAM_INT);
                    break;
                case 'ids':
                    $value = clean_param($params['value'], PARAM_SEQUENCE);
                    break;
                case 'shortname':
                    $value = clean_param($params['value'], PARAM_TEXT);
                    break;
                case 'idnumber':
                    $value = clean_param($params['value'], PARAM_RAW);
                    break;
                default:
                    throw new invalid_parameter_exception('Invalid field name');
            }

            if ($params['field'] === 'ids') {
                // Preload categories to avoid loading one at a time.
                $courseids = explode(',', $value);
                list ($listsql, $listparams) = $DB->get_in_or_equal($courseids);
                $categoryids = $DB->get_fieldset_sql("
                        SELECT DISTINCT cc.id
                          FROM {course} c
                          JOIN {course_categories} cc ON cc.id = c.category
                         WHERE c.id $listsql", $listparams);
                core_course_category::get_many($categoryids);

                // Load and validate all courses. This is called because it loads the courses
                // more efficiently.
                list ($courses, $warnings) = external_util::validate_courses($courseids, [],
                    false, true);
            } else {
                $courses = $DB->get_records('course', array($params['field'] => $value), 'id ASC');
            }
        }

        $coursesdata = array();

        foreach ($courses as $course) {
            $returnedusers = array();
            $context = context_course::instance($course->id);
            $canupdatecourse = has_capability('moodle/course:update', $context);
            $canviewhiddencourses = has_capability('moodle/course:viewhiddencourses', $context);

            // Check if the course is visible in the site for the user.
            if (!$course->visible and !$canviewhiddencourses and !$canupdatecourse) {
                continue;
            }
            // Get the public course information, even if we are not enrolled.
            $courseinlist = new core_course_list_element($course);

            // Now, check if we have access to the course, unless it was already checked.
            try {
                if (empty($course->contextvalidated)) {
                    self::validate_context($context);
                }
            } catch (Exception $e) {
                // User can not access the course, check if they can see the public information about the course and return it.
                if (core_course_category::can_view_course_info($course)) {
                    $coursesdata[$course->id] = self::get_course_public_information($courseinlist, $context);
                }
                continue;
            }

            $coursesdata[$course->id] = self::get_course_public_information($courseinlist, $context);

            // Return information for any user that can access the course.
            $coursefields = array('format', 'showgrades', 'newsitems', 'startdate', 'enddate', 'maxbytes', 'showreports', 'visible',
                'groupmode', 'groupmodeforce', 'defaultgroupingid', 'enablecompletion', 'completionnotify', 'lang', 'theme',
                'marker');

            // Course filters.
            $coursesdata[$course->id]['filters'] = filter_get_available_in_context($context);

            // Information for managers only.
            if ($canupdatecourse) {
                $managerfields = array('idnumber', 'legacyfiles', 'calendartype', 'timecreated', 'timemodified', 'requested',
                    'cacherev');
                $coursefields = array_merge($coursefields, $managerfields);
            }

            // Populate fields.
            foreach ($coursefields as $field) {
                $coursesdata[$course->id][$field] = $course->{$field};
            }

            // Clean lang and auth fields for external functions (it may content uninstalled themes or language packs).
            if (isset($coursesdata[$course->id]['theme'])) {
                $coursesdata[$course->id]['theme'] = clean_param($coursesdata[$course->id]['theme'], PARAM_THEME);
            }
            if (isset($coursesdata[$course->id]['lang'])) {
                $coursesdata[$course->id]['lang'] = clean_param($coursesdata[$course->id]['lang'], PARAM_LANG);
            }

            $courseformatoptions = course_get_format($course)->get_config_for_external();
            foreach ($courseformatoptions as $key => $value) {
                $coursesdata[$course->id]['courseformatoptions'][] = array(
                    'name' => $key,
                    'value' => $value
                );
            }

            // Instructor of the course.
            $courseid = intval($course->id);
            $sql = "SELECT u.id, u.username, u.firstname, u.lastname, u.email 
                    FROM {course} as c, 
                        {role_assignments} AS ra, 
                        {user} AS u, 
                        {context} AS ct
                    WHERE c.id = ct.instanceid 
                        AND ra.roleid = 3  
                        AND ra.userid = u.id 
                        AND ct.id = ra.contextid
                        AND c.id = ". $courseid;

            $users = $DB->get_records_sql($sql);

            // Finally retrieve each users information (Also includes custom profile fields)
            foreach ($users as $user) {
                array_push($returnedusers,
                    [
                        'userid' => $user->id,
                        'username' => $user->username,
                        'firstname' => $user->firstname,
                        'lastname' => $user->lastname,
                        'email' => $user->email
                    ]
                );
            }

            $coursesdata[$course->id]['users'] = $returnedusers;
        }

        return array(
            'courses' => $coursesdata,
            'warnings' => $warnings
        );
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_course_details_with_instructor_returns() {
        return new external_single_structure(
            array(
                'courses' => new external_multiple_structure(self::get_course_structures(false), 'Course'),
                'warnings' => new external_warnings()
            )
        );
    }


    /**
     * Returns a course structure definition
     *
     * @param boolean $onlypublicdata set to true, to retrieve only fields viewable by anyone when the course is visible
     * @return array the course structure
     * @since  Moodle 3.2
     */
    protected static function get_course_structures($onlypublicdata = true) {
        $coursestructure = array(
            'id' => new external_value(PARAM_INT, 'course id'),
            'fullname' => new external_value(PARAM_RAW, 'course full name'),
            'displayname' => new external_value(PARAM_RAW, 'course display name'),
            'shortname' => new external_value(PARAM_RAW, 'course short name'),
            'categoryid' => new external_value(PARAM_INT, 'category id'),
            'categoryname' => new external_value(PARAM_RAW, 'category name'),
            'sortorder' => new external_value(PARAM_INT, 'Sort order in the category', VALUE_OPTIONAL),
            'summary' => new external_value(PARAM_RAW, 'summary'),
            'summaryformat' => new external_format_value('summary'),
            'summaryfiles' => new external_files('summary files in the summary field', VALUE_OPTIONAL),
            'overviewfiles' => new external_files('additional overview files attached to this course'),
            'showactivitydates' => new external_value(PARAM_BOOL, 'Whether the activity dates are shown or not'),
            'showcompletionconditions' => new external_value(PARAM_BOOL,
                'Whether the activity completion conditions are shown or not'),
            'contacts' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'contact user id'),
                        'fullname' => new external_value(PARAM_NOTAGS, 'contact user fullname'),
                    )
                ),
                'contact users'
            ),

            'users' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'userid' => new external_value(PARAM_RAW, 'Course format option name.'),
                        'username' => new external_value(PARAM_RAW, 'Course format option name.'),
                        'firstname' => new external_value(PARAM_RAW, 'Course format option name.'),
                        'lastname' => new external_value(PARAM_RAW, 'Course format option name.'),
                        'email' => new external_value(PARAM_RAW, 'Course format option name.')                    )
                ),
                'contact users'
            ),

            'enrollmentmethods' => new external_multiple_structure(
                new external_value(PARAM_PLUGIN, 'enrollment method'),
                'enrollment methods list'
            ),
            'customfields' => new external_multiple_structure(
                new external_single_structure(
                    array(
                        'name' => new external_value(PARAM_RAW, 'The name of the custom field'),
                        'shortname' => new external_value(PARAM_RAW,
                            'The shortname of the custom field - to be able to build the field class in the code'),
                        'type' => new external_value(PARAM_ALPHANUMEXT,
                            'The type of the custom field - text field, checkbox...'),
                        'valueraw' => new external_value(PARAM_RAW, 'The raw value of the custom field'),
                        'value' => new external_value(PARAM_RAW, 'The value of the custom field'),
                    )
                ), 'Custom fields', VALUE_OPTIONAL),
        );

        if (!$onlypublicdata) {
            $extra = array(
                'idnumber' => new external_value(PARAM_RAW, 'Id number', VALUE_OPTIONAL),
                'format' => new external_value(PARAM_PLUGIN, 'Course format: weeks, topics, social, site,..', VALUE_OPTIONAL),
                'showgrades' => new external_value(PARAM_INT, '1 if grades are shown, otherwise 0', VALUE_OPTIONAL),
                'newsitems' => new external_value(PARAM_INT, 'Number of recent items appearing on the course page', VALUE_OPTIONAL),
                'startdate' => new external_value(PARAM_INT, 'Timestamp when the course start', VALUE_OPTIONAL),
                'enddate' => new external_value(PARAM_INT, 'Timestamp when the course end', VALUE_OPTIONAL),
                'maxbytes' => new external_value(PARAM_INT, 'Largest size of file that can be uploaded into', VALUE_OPTIONAL),
                'showreports' => new external_value(PARAM_INT, 'Are activity report shown (yes = 1, no =0)', VALUE_OPTIONAL),
                'visible' => new external_value(PARAM_INT, '1: available to student, 0:not available', VALUE_OPTIONAL),
                'groupmode' => new external_value(PARAM_INT, 'no group, separate, visible', VALUE_OPTIONAL),
                'groupmodeforce' => new external_value(PARAM_INT, '1: yes, 0: no', VALUE_OPTIONAL),
                'defaultgroupingid' => new external_value(PARAM_INT, 'default grouping id', VALUE_OPTIONAL),
                'enablecompletion' => new external_value(PARAM_INT, 'Completion enabled? 1: yes 0: no', VALUE_OPTIONAL),
                'completionnotify' => new external_value(PARAM_INT, '1: yes 0: no', VALUE_OPTIONAL),
                'lang' => new external_value(PARAM_SAFEDIR, 'Forced course language', VALUE_OPTIONAL),
                'theme' => new external_value(PARAM_PLUGIN, 'Fame of the forced theme', VALUE_OPTIONAL),
                'marker' => new external_value(PARAM_INT, 'Current course marker', VALUE_OPTIONAL),
                'legacyfiles' => new external_value(PARAM_INT, 'If legacy files are enabled', VALUE_OPTIONAL),
                'calendartype' => new external_value(PARAM_PLUGIN, 'Calendar type', VALUE_OPTIONAL),
                'timecreated' => new external_value(PARAM_INT, 'Time when the course was created', VALUE_OPTIONAL),
                'timemodified' => new external_value(PARAM_INT, 'Last time  the course was updated', VALUE_OPTIONAL),
                'requested' => new external_value(PARAM_INT, 'If is a requested course', VALUE_OPTIONAL),
                'cacherev' => new external_value(PARAM_INT, 'Cache revision number', VALUE_OPTIONAL),
                'filters' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'filter' => new external_value(PARAM_PLUGIN, 'Filter plugin name'),
                            'localstate' => new external_value(PARAM_INT, 'Filter state: 1 for on, -1 for off, 0 if inherit'),
                            'inheritedstate' => new external_value(PARAM_INT, '1 or 0 to use when localstate is set to inherit'),
                        )
                    ),
                    'Course filters', VALUE_OPTIONAL
                ),
                'courseformatoptions' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'name' => new external_value(PARAM_RAW, 'Course format option name.'),
                            'value' => new external_value(PARAM_RAW, 'Course format option value.'),
                        )
                    ),
                    'Additional options for particular course format.', VALUE_OPTIONAL
                ),
            );
            $coursestructure = array_merge($coursestructure, $extra);
        }
        return new external_single_structure($coursestructure);
    }
    /**
     * Returns description of method get_enrolment_info_by_userid parameters
     *
     * @return external_function_parameters
     */
    public static function get_enrolment_info_by_userid_parameters() {
        return new external_function_parameters(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course ID'),
                'userid' => new external_value(PARAM_INT, 'User id')
            )
        );
    }
    /**
     * Get enrolment info a course and returns whether the user is enrolled in that course.
     * 
     * This API will be used for discount countdown.
     *
     * @param int $courseid ID of the Course
     * @param int $userid ID of the User
     * @return array 
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_enrolment_info_by_userid($courseid, $userid) {
        global $CFG, $USER, $DB;
        require_once($CFG->libdir . '/grouplib.php');
        require_once($CFG->dirroot . "/user/lib.php");

        $arrayparams = array(
            'courseid' => $courseid,
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_enrolment_info_by_userid_parameters(), $arrayparams);
        $enrolinfo = $DB->get_record('enrol', array('enrol' => 'fee', 'courseid' => $courseid));
        $courseinfo = $DB->get_record('course', array('id' => $courseid));
        
        $cost = $enrolinfo->cost;
        $component = 'enrol_fee';
        $paymentarea = 'fee';
        $itemid = $enrolinfo->id; 
        $description = 'Enrolment in ' . $courseinfo->shortname;

        
        return array(
            'courseid' => $courseid,
            'userid' => $userid,
            'component' => $component,
            'paymentarea' => $paymentarea,
            'itemid' => $itemid,
            'description' => $description,
            'cost' => $cost,
            'warnings' => array()
        );

    }
    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_enrolment_info_by_userid_returns() {
        return new external_single_structure(
            array(
                'courseid' => new external_value(PARAM_INT, 'Course id'),
                'userid' => new external_value(PARAM_INT, 'User id'),
                'component' => new external_value(PARAM_RAW, 'Course id'),
                'paymentarea' => new external_value(PARAM_RAW, 'Course id'),
                'itemid' => new external_value(PARAM_INT, 'Course id'),
                'description' => new external_value(PARAM_RAW, 'Course id'),
                'cost' => new external_value(PARAM_FLOAT, 'Course id'),
                'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
            )
        );
    }

    /**
     * Returns description of method get_recommended_courses parameters
     *
     * @return external_function_parameters
     */
    public static function get_recommended_courses_parameters() {
        return new external_function_parameters(
            array(
                'userid' => new external_value(PARAM_INT, 'User id')
            )
        );
    }
    /**
     * Get list of courses recommneded for a specific user based on tags of course.
     * 
     *
     * @param int $userid ID of the User
     * @return array 
     * @since Moodle 2.9
     * @throws moodle_exception
     */
    public static function get_recommended_courses($userid) {
        global $CFG, $DB;
        require_once($CFG->libdir . '/grouplib.php');
        require_once($CFG->dirroot . "/user/lib.php");

        $arrayparams = array(
            'userid' => $userid,
        );
        $params = self::validate_parameters(self::get_recommended_courses_parameters(), $arrayparams);
        
        // Get enrolled courses.
        $enrolledcourses = enrol_get_users_courses($userid, true, '*');
        $enrolledcourseids = array();

        foreach($enrolledcourses as $key => $value) {
            array_push($enrolledcourseids, $key);
        }
        $coursestring = implode(",", $enrolledcourseids);
        
        // Get tags of enrolled courses.
        $tags_sql = "SELECT ti.tagid
                FROM {tag_instance} ti
                LEFT JOIN {tag} t ON ti.tagid = t.id
                WHERE ti.itemid IN ($coursestring)
                GROUP BY ti.tagid";

        $tags = $DB->get_records_sql($tags_sql);
        $tagsids = array();
        foreach($tags as $key => $value) {
            array_push($tagsids, $key);
        }

        $tagstring = implode(",", $tagsids);

        // Get recommended courses.
        $recommend_sql = "SELECT ti.tagid, t.name, ti.itemid, c.*
                            FROM mdl_tag_instance ti
                       LEFT JOIN mdl_course c ON ti.itemid = c.id
                       LEFT JOIN mdl_tag t ON ti.tagid = t.id
                           WHERE ti.tagid IN ($tagstring) AND ti.itemid NOT IN ($coursestring)";

        $recommended_data = $DB->get_records_sql($recommend_sql);
        $recommended_courses = array();

        foreach($recommended_data as $key=>$value) {
            
            $temp = new stdClass();
            $temp->id = $value->id;
            $temp->fullname = $value->fullname;
            $temp->shortname = $value->shortname;
            $temp->summary = $value->summary;
            $temp->startdate = $value->startdate;
            $temp->enddate = $value->enddate;

            array_push($recommended_courses, $temp);
        }
        
        $courses = [];

        foreach($recommended_courses as $course) {
            // If the course's end date is past the current time, the we don't need to recommend that course.
            if($course->enddate  > time()) {
                $coursecontext = context_course::instance($course->id);
                $courses[$course->id] = self::get_course_public_information(new \core_course_list_element($course), $coursecontext);
            }
            
        }
        return array(
            'userid' => $userid,
            'recommended_courses' => $courses,
            'warnings' => array()
        );

    }
    /**
     * Returns description of method result value.
     *
     * @return external_description
     * @since Moodle 2.9
     */
    public static function get_recommended_courses_returns() {
        return new external_single_structure(
            array(
                'userid' => new external_value(PARAM_INT, 'User id'),
                'recommended_courses' => new external_multiple_structure(self::get_course_structure(), 'Course list'),
                'warnings' => new external_warnings('always set to \'key\'', 'faulty key name')
            )
        );
    }

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     * @since Moodle 3.0
     */
    public static function search_courses_by_lang_parameters() {
        return new external_function_parameters(
            array(
                'criterianame'  => new external_value(PARAM_ALPHA, 'criteria name
                                                        (search, modulelist (only admins), blocklist (only admins), tagid)'),
                'criteriavalue' => new external_value(PARAM_RAW, 'criteria value'),
                'page'          => new external_value(PARAM_INT, 'page number (0 based)', VALUE_DEFAULT, 0),
                'perpage'       => new external_value(PARAM_INT, 'items per page', VALUE_DEFAULT, 0),
                'requiredcapabilities' => new external_multiple_structure(
                    new external_value(PARAM_CAPABILITY, 'Capability string used to filter courses by permission'),
                    'Optional list of required capabilities (used to filter the list)', VALUE_DEFAULT, array()
                ),
                'limittoenrolled' => new external_value(PARAM_BOOL, 'limit to enrolled courses', VALUE_DEFAULT, 0),
                'onlywithcompletion' => new external_value(PARAM_BOOL, 'limit to courses where completion is enabled',
                    VALUE_DEFAULT, 0),
                'lang' => new external_value(PARAM_RAW, 'Language', VALUE_DEFAULT, 'en'),
                'cname' => new external_value(PARAM_RAW, 'Course name (customfield)', VALUE_DEFAULT, 'cname'),
            )
        );
    }

    /**
     * Search courses following the specified criteria.
     *
     * @param string $criterianame  Criteria name (search, modulelist (only admins), blocklist (only admins), tagid)
     * @param string $criteriavalue Criteria value
     * @param int $page             Page number (for pagination)
     * @param int $perpage          Items per page
     * @param array $requiredcapabilities Optional list of required capabilities (used to filter the list).
     * @param int $limittoenrolled  Limit to only enrolled courses
     * @param int $lang Language string: EN or BN
     * @param int $cname Course name in bangla (customfield)
     * @param int onlywithcompletion Limit to only courses where completion is enabled
     * @return array of course objects and warnings
     * @since Moodle 3.0
     * @throws moodle_exception
     */
    public static function search_courses_by_lang($criterianame,
                                          $criteriavalue,
                                          $page=0,
                                          $perpage=0,
                                          $requiredcapabilities=array(),
                                          $limittoenrolled=0,
                                          $onlywithcompletion=0,
                                          $lang='en',
                                          $cname='cname'
    ) {
        $warnings = array();
        $parameters = array(
            'criterianame'               => $criterianame,
            'criteriavalue'              => $criteriavalue,
            'page'                       => $page,
            'perpage'                    => $perpage,
            'requiredcapabilities'       => $requiredcapabilities,
            'limittoenrolled'            => $limittoenrolled,
            'onlywithcompletion'         => $onlywithcompletion,
            'lang'                       => $lang,
            'cname'                      => $cname,
        );
        $params = self::validate_parameters(self::search_courses_by_lang_parameters(), $parameters);
        self::validate_context(context_system::instance());

        $allowedcriterianames = array('search', 'modulelist', 'blocklist', 'tagid');
        if (!in_array($params['criterianame'], $allowedcriterianames)) {
            throw new invalid_parameter_exception('Invalid value for criterianame parameter (value: '.$params['criterianame'].'),' .
                'allowed values are: '.implode(',', $allowedcriterianames));
        }

        if ($params['criterianame'] == 'modulelist' or $params['criterianame'] == 'blocklist') {
            require_capability('moodle/site:config', context_system::instance());
        }

        $paramtype = array(
            'search' => PARAM_RAW,
            'modulelist' => PARAM_PLUGIN,
            'blocklist' => PARAM_INT,
            'tagid' => PARAM_INT
        );
        $params['criteriavalue'] = clean_param($params['criteriavalue'], $paramtype[$params['criterianame']]);

        // Prepare the search API options.
        $searchcriteria = array();
        $searchcriteria[$params['criterianame']] = $params['criteriavalue'];
        if ($params['onlywithcompletion']) {
            $searchcriteria['onlywithcompletion'] = true;
        }

        $options = array();
        if ($params['perpage'] != 0) {
            $offset = $params['page'] * $params['perpage'];
            $options = array('offset' => $offset, 'limit' => $params['perpage']);
        }

        // Search the courses.
        $courses = core_course_category::search_courses($searchcriteria, $options, $params['requiredcapabilities']);
        $totalcount = core_course_category::search_courses_count($searchcriteria, $options, $params['requiredcapabilities']);

        if (!empty($limittoenrolled)) {
            // Get the courses where the current user has access.
            $enrolled = enrol_get_my_courses(array('id', 'cacherev'));
        }

        $finalcourses = array();
        $categoriescache = array();

        foreach ($courses as $course) {
            if (!empty($limittoenrolled)) {
                // Filter out not enrolled courses.
                if (!isset($enrolled[$course->id])) {
                    $totalcount--;
                    continue;
                }
            }

            $coursecontext = context_course::instance($course->id);
            $course_info = self::get_course_public_information($course, $coursecontext);
            $course_info['visible'] = $course->visible; // Add 'visible' property
            $finalcourses[] = $course_info;
        }
        $searchresult = [];
        foreach ($finalcourses as $finalcourse) {
            $match = false; // Flag to indicate if the course matches the search criteria
            foreach ($finalcourse['customfields'] as $customfield) {
                // Check if the course matches the language
                if ($customfield['shortname'] == 'language' && $customfield['value'] == $lang) {
                    $match = true;
                    break;
                }
                // Check if the course matches the cname (if $cname is not empty)
                if (!empty($params['cname']) && $customfield['shortname'] == 'cname' && stripos($customfield['value'], $params['cname']) !== false) {
                    $match = true;
                    break; // No need to check further custom fields if a match is found
                }
            }
            // If $cname is empty, include all courses
            if (empty($params['cname']) || $match) {
                $searchresult[] = $finalcourse;
            }
        }

        return array(
            'total' => count($searchresult),
            'courses' => $searchresult,
            'warnings' => $warnings
        );
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     * @since Moodle 3.0
     */
    public static function search_courses_by_lang_returns() {
        return new external_single_structure(
            array(
                'total' => new external_value(PARAM_INT, 'total course count'),
                'courses' => new external_multiple_structure(self::get_course_structure(false), 'course'),
                'warnings' => new external_warnings()
            )
        );
    }
}



