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
 * This script deals with starting a new attempt for a qpractice.
 *
 * It will end up redirecting to attempt.php.
 *
 * @package    mod_qpractice
 * @copyright  2013 Jayesh Anandani
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once(dirname(__FILE__) . '/startattempt_form.php');
require_once($CFG->libdir . '/questionlib.php');

$id = required_param('id', PARAM_INT); // Course_module ID.


$PAGE->set_url('/mod/qpractice/startattempt.php', array('id' => $id));
$DB->set_field('qpractice_session', 'status', 'finished', null);

if ($id) {
    if (!$cm = get_coursemodule_from_id('qpractice', $id)) {
        print_error('invalidcoursemodule');
    }
    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }
    $qpractice = $DB->get_record('qpractice', array('id' => $cm->instance));
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$behaviours = get_options_behaviour($cm);

$categories = $DB->get_records_menu('question_categories', array('contextid' => $context->id, 'parent' => 0), 'name', 'id, name');
$categories = remove_empty($context, $categories);

$data = array();
$data['categories'] = $categories;
$data['behaviours'] = $behaviours;
$data['instanceid'] = $cm->instance;

$mform = new mod_qpractice_startattempt_form(null, $data);

if ($mform->is_cancelled()) {
    $returnurl = new moodle_url('/mod/qpractice/view.php', array('id' => $cm->id));
    redirect($returnurl);
} else if ($fromform = $mform->get_data()) {

    $sessionid = qpractice_session_create($fromform, $context);
    $nexturl = new moodle_url('/mod/qpractice/attempt.php', array('id' => $sessionid));
    redirect($nexturl);
}

$mform->set_data(array(
    'id' => $cm->id,
));

// Print the page header.
$PAGE->set_title(format_string($qpractice->name));
$PAGE->set_heading(format_string($qpractice->name));
$PAGE->set_context($context);

// Output starts here.
echo $OUTPUT->header();

$mform->display();

// Finish the page.
echo $OUTPUT->footer();

function remove_empty($context, $categories) {
    foreach ($categories as $key => $category) {
        $subcategories = getSubCategories($context, $key);
        /*in case there are questions in the root category */
        $subcategories[]=$key;
        if (!(contains_questions($subcategories))) {
            unset($categories[$key]);
        }
    }
    return $categories;
}

function getOneLevel($context, $categoryid) {
    global $DB;
    $categories = $DB->get_records_sql("select id  from {question_categories} where contextid=? and parent=?", array($context->id, $categoryid));
    return(array_keys($categories));
}

function getSubCategories($context, $categoryid, $categories = array()) {
    $tree = array();
    $tree = getOneLevel($context, $categoryid);
    if (count($tree) > 0 && is_array($tree)) {
        $categories = array_merge($categories, $tree);
    }
    foreach ($tree as $key => $val) {
        getSubCategories($context, $val);
    }
    return $categories;
}

function contains_questions($categories) {
    global $DB;
    foreach ($categories as $category) {
        $questions = $DB->get_records_sql("select id from {question} where category=?", array($category));
        if (count($questions) > 0) {
            return true;
        }
    }
    return false;
}
