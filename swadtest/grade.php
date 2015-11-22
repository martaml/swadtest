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
 * Redirect the user to the appropriate submission related page
 *
 * @package   mod_swadtest
 * @category  grade
 * @copyright 2014 Marta Muñoz
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . "../../config.php");
require_once(dirname(__FILE__) . '/../../config.php');

require_once($CFG->dirroot . '/mod/swadmoodle/locallib.php');

$id = required_param('id', PARAM_INT);          // Course module ID
$itemnumber = optional_param('itemnumber', 0, PARAM_INT); // Item number, may be != 0 for activities that allow more than one grade per user
$userid = optional_param('userid', 0, PARAM_INT); // Graded user ID (optional)

// TODO ESTO AÑADIDO VISTO EN /mod/quiz
$cm = get_coursemodule_from_id('swadtest', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$quiz = $DB->get_record('swadtest', array('id' => $cm->instance), '*', MUST_EXIST);
require_login($course, false, $cm);

//in the simplest case just redirect to the view page
redirect('view.php?id='.$id);
