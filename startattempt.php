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
 * Prints a particular instance of qpractice
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_qpractice
 * @copyright  2013 Jayesh Anandani
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/startattempt_form.php');
require_once("$CFG->libdir/formslib.php");

$id = required_param('id', PARAM_INT); // course_module ID, or
$returnurl = optional_param('returnurl', '', PARAM_LOCALURL);
$nexturl = optional_param('nexturl', '', PARAM_LOCALURL);

$PAGE->set_url('/mod/qpractice/startattempt.php', array('id' => $id));

if ($id) {
    if (!$cm = get_coursemodule_from_id('qpractice', $id)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
} 


require_login($course, true, $cm);
$context = context_module::instance($cm->id);

if ($returnurl) {
    $returnurl = new moodle_url($returnurl);
} else {
    $returnurl = new moodle_url('/mod/qpractice/view.php', array('id' => $cm->id));
}
if ($nexturl) {
    $nexturl = new moodle_url($nexturl);
} else {
    $nexturl = new moodle_url('/mod/qpractice/attempt.php', array('id' => $cm->id));
}

$mform = new mod_qpractice_startattempt_form();

 
if ($mform->is_cancelled()){
    
    redirect($returnurl);
 
} else if ($fromform=$mform->get_data()){
    
    redirect($nexturl);
}

global $DB,$CFG;
$DB->set_debug(true);
$ar=$DB->get_record('qpractice', array('id'=>$cm->instance),'behaviour');
print_object($ar->behaviour);
$ar1=$DB->get_record('question_categories', array('contextid'=>$context->id), 'name');
print_object($ar1->name);
$DB->set_debug(false);

$mform->set_data(array(
    'returnurl' => $returnurl,
    'id' => $cm->id,
));


//add_to_log($course->id, 'qpractice', 'view', "view.php?id={$cm->id}", $qpractice->name, $cm->id);

/// Print the page header

$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here*/

echo $OUTPUT->header();


$mform->display();
 
 // Finish the page
echo $OUTPUT->footer();
