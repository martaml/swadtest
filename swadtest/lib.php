<?php

/**
 * Library of interface functions and constants for module swadtest
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the swadtest specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod_swadtest
 * @copyright  2014 Marta Muñoz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('NEWMODULE_ULTIMATE_ANSWER', 42);
                         
////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function swadtest_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:         return true;
        case FEATURE_SHOW_DESCRIPTION:  return true;

        default:                        return null;
    }
}

/**
 * Saves a new instance of the swadtest into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $swadtest An object from the form in mod_form.php
 * @param mod_swadtest_mod_form $mform
 * @return int The id of the newly inserted swadtest record
 */
function swadtest_add_instance(stdClass $swadtest, mod_swadtest_mod_form $mform = null) {

    global $DB;

    $swadtest->timecreated = time();

    # You may have to add extra stuff in here #

    //insert answers
    //$swadtest->id = $DB->insert_record("swadtest", $swadtest);
    /*foreach ($choice->option as $key => $value) {
        $value = trim($value);
        if (isset($value) && $value <> '') {
            $option = new stdClass();
            $option->text = $value;
            $option->choiceid = $choice->id;
            if (isset($choice->limit[$key])) {
                $option->maxanswers = $choice->limit[$key];
            }
            $option->timemodified = time();
            $DB->insert_record("choice_options", $option);
        }
    }
    return $choice->id;*/
    
    
   /* if (!$template = $DB->get_record('swadtest', array("id"=>$swadtest->template))) {
        return 0;
    }*/
  
    return $DB->insert_record('swadtest', $swadtest);
}

/**
 * Updates an instance of the swadtest in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $swadtest An object from the form in mod_form.php
 * @param mod_swadtest_mod_form $mform
 * @return boolean Success/Fail
 */
function swadtest_update_instance(stdClass $swadtest, mod_swadtest_mod_form $mform = null) {
    global $DB;

    if (!$template = $DB->get_record('swadtest', array("id"=>$swadtest->template))) {
        return 0;
    }

    $swadtest->timemodified = time();
    $swadtest->id = $swadtest->instance;

    # You may have to add extra stuff in here #

    return $DB->update_record('swadtest', $swadtest);
}

/**
 * Removes an instance of the swadtest from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function swadtest_delete_instance($id) {
    global $DB;

    if (! $swadtest = $DB->get_record('swadtest', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('swadtest', array('id' => $swadtest->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function swadtest_user_outline($course, $user, $mod, $swadtest) {

    global $DB;

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;

}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $swadtest the module instance record
 * @return void, is supposed to echp directly
 */
function swadtest_user_complete($course, $user, $mod, $swadtest) {
    global $CFG, $DB, $OUTPUT;

    if (swadtest_already_done($swadtest->id, $user->id)) {
        if ($swadtest->template == SWADTEST_CIQ) { // print out answers for critical incidents
            $table = new html_table();
            $table->align = array("left", "left");
	    }
            echo html_writer::table($table);

       /* } else {

            survey_print_graph("id=$mod->id&amp;sid=$user->id&amp;type=student.png");
        }*/

    } /* else {
        print_string("notdone", "survey");
    }*/
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in swadtest activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function swadtest_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link swadtest_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function swadtest_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see swadtest_get_recent_mod_activity()}

 * @return void
 */
function swadtest_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function swadtest_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function swadtest_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of swadtest?
 *
 * This function returns if a scale is being used by one swadtest
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $swadtestid ID of an instance of this module
 * @return bool true if the scale is used by the given swadtest instance
 */
function swadtest_scale_used($swadtestid, $scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('swadtest', array('id' => $swadtestid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of swadtest.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any swadtest instance
 */
function swadtest_scale_used_anywhere($scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('swadtest', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the give swadtest instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $swadtest instance object with extra cmidnumber and modname property
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return void
 */
function swadtest_grade_item_update(stdClass $swadtest, $grades=null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($swadtest->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $swadtest->grade;
    $item['grademin']  = 0;

    grade_update('mod/swadtest', $swadtest->course, 'mod', 'swadtest', $swadtest->id, 0, null, $item);
}

/**
 * Update swadtest grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $swadtest instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function swadtest_update_grades(stdClass $swadtest, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/swadtest', $swadtest->course, 'mod', 'swadtest', $swadtest->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function swadtest_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for swadtest file areas
 *
 * @package mod_swadtest
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function swadtest_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the swadtest file areas
 *
 * @package mod_swadtest
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the swadtest's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function mod_swadtest_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
   global $DB, $CFG;
   
   var_dump($context);
   
    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    //send_file_not_found();

    // Check the contextlevel is as expected - if your plugin is a block, this becomes CONTEXT_BLOCK, etc.
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false; 
    }
 
    // Make sure the filearea is one of those used by the plugin.
    if ($filearea !== 'expectedfilearea' && $filearea !== 'anotherexpectedfilearea') {
        return false;
    }
 
    // Make sure the user is logged in and has access to the module (plugins that are not course modules should leave out the 'cm' part).
    require_login($course, true, $cm);
 
    // Check the relevant capabilities - these may vary depending on the filearea being accessed.
    if (!has_capability('mod/SWADTEST:view', $context)) {
        return false;
    }
 
    // Leave this line out if you set the itemid to null in make_pluginfile_url (set $itemid to 0 instead).
    $itemid = array_shift($args); // The first item in the $args array.


    // Use the itemid to retrieve any relevant data records and perform any security checks to see if the
    // user really does have access to the file in question.
 
    // Extract the filename / filepath from the $args array.
    $filename = array_pop($args); // The last item in the $args array.
    if (!$args) {
        $filepath = '/'; // $args is empty => the path is '/'
    } else {
        $filepath = '/'.implode('/', $args).'/'; // $args contains elements of the filepath
    }
 
    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'user', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }
 
    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering. 
    // From Moodle 2.3, use send_stored_file instead.
    send_stored_file($file);
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding swadtest nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the swadtest module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function swadtest_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
}

/**
 * Extends the settings navigation with the swadtest settings
 *
 * This function is called when the context for the page is a swadtest module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $swadtestnode {@link navigation_node}
 */
function swadtest_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $swadtestnode=null) {
}


///////////////FUNCIONES PROPIASSSSSSSSSSSSSSSSSSSS
function base64url_encode($data) { 
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '='); 
} 

// SUSTITUYE CARACTERES ESPECIALES QUE NO SE REEMPLAZAN CON SWAD
// TODOS LOS CARACTERES ESPECIALES SON: ~ # { } = :
// LOS QUE YO TENGO QUE MANEJAR SON: # Y :
function replace_special_char ($str) {
    //str_replace ( mixed $search , mixed $replace , mixed $subject [, int &$count ] )
    $str = str_replace ('#', '\#', $str);
    $str = str_replace (':', '\:', $str);
    $str = str_replace ('{', '\{', $str);
    $str = str_replace ('}', '\}', $str);
    $str = str_replace ('~', '\~', $str);
    $str = str_replace ('=', '\=', $str);
    return $str;
}
