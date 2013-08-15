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
require_once("$CFG->libdir/formslib.php");

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // qpractice instance ID - it should be named as the first character of the module

if ($id) {
    if (!$cm = get_coursemodule_from_id('qpractice', $id)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
}
else {
    if (!$qpractice = $DB->get_record('qpractice', array('id' => $n))) {
        print_error('invalidpracticeid', 'quiz');
    }
    if (!$course = $DB->get_record('course', array('id' => $qpractice->course))) {
        print_error('invalidcourseid');
    }
    if (!$cm = get_coursemodule_from_instance("qpractice", $qpractice->id, $course->id)) {
        print_error('invalidcoursemodule');
    }
}


require_login($course, true, $cm);
$context = context_module::instance($cm->id);
// require_capability('mod/qpractice:view', $context);
//add_to_log($course->id, 'qpractice', 'view', "view.php?id={$cm->id}", $qpractice->name, $cm->id);
$PAGE->set_url('/mod/qpractice/view.php', array('id' => $cm->id));
// $PAGE->set_title(format_string($qpractice->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);


$createurl = new moodle_url('/mod/qpractice/startattempt.php', array('id' => $cm->id)); 
$createtext = get_string('createurl', 'qpractice');
$reporturl = new moodle_url('../../question/preview.php', array('id' => $cm->id)); 
$reporttext = get_string('reporturl', 'qpractice');

echo $OUTPUT->header();

echo html_writer::link($createurl, $createtext);
echo html_writer::empty_tag('br');
echo html_writer::link($reporturl, $reporttext);

/*if ($qpractice->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('qpractice', $qpractice, $cm->id), 'generalbox mod_introbox', 'qpracticeintro');
}*/

// Finish the page
echo $OUTPUT->footer();
