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

$sessionid = required_param('id', PARAM_INT); // Sessionid.
$session = $DB->get_record('qpractice_session', array('id' => $sessionid));
$cm = get_coursemodule_from_instance('qpractice', $session->qpracticeid);
$course = $DB->get_record('course', array('id' => $cm->course));

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
$PAGE->set_title('Testing');
$PAGE->set_heading('Testing');
$PAGE->set_context($context);
$PAGE->set_url('/mod/qpractice/summary.php', array('id' => $sessionid));
$output = $PAGE->get_renderer('mod_qpractice');

echo $OUTPUT->header();

echo $output->summary_table();

echo $output->summary_form();

// Finish the page.
echo $OUTPUT->footer();
