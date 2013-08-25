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
require_once(dirname(__FILE__).'/renderer.php');
require_once("$CFG->libdir/formslib.php");

$id = required_param('id', PARAM_INT); // Course-Module id.

if ($id) {
    if (!$cm = get_coursemodule_from_id('qpractice', $id)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
    $qpractice = $DB->get_records('qpractice_session', array('qpracticeid' => $cm->instance, 'userid' => $USER->id));
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
$PAGE->set_title('Testing');
$PAGE->set_heading('Testing');
$PAGE->set_context($context);
$PAGE->set_url('/mod/qpractice/report.php', array('id' => $cm->id));
$output = $PAGE->get_renderer('mod_qpractice');

echo $OUTPUT->header();

echo $output->report_table($cm->id);

// Finish the page.
echo $OUTPUT->footer();