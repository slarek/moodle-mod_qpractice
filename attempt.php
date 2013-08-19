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
 * This page displays an attempt of practice module
 * @package    mod_qpractice
 * @copyright  2013 Jayesh Anandani
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir . '/questionlib.php');
require_once(dirname(__FILE__) . '/attemptlib.php');
require_once(dirname(__FILE__) . '/locallib.php');

// Get and validate question id.
$sessionid = required_param('id', PARAM_INT);
$displaynumber = optional_param('displaynumber', false, PARAM_INT);
$session = $DB->get_record('qpractice_session', array('id' => $sessionid));
$categoryid = $session->categoryid;
$excludedqtypes =null;

$quba = question_engine::load_questions_usage_by_activity($session->questionusageid);

$results = $DB->get_records_menu('question_attempts', array('questionusageid'=>$session->questionusageid), 'id', 'id, questionid');

$idss=choose_other_question($categoryid, $results);
$results = $idss->id;

$question = question_bank::load_question($idss->id);

if ($displaynumber) {
     $displaynumber = $displaynumber+1;
} else {
     $displaynumber = '1';
}

require_login();
$category = $DB->get_record('question_categories',
            array('id' => $question->category), '*', MUST_EXIST);
$context = context::instance_by_id($category->contextid);
$PAGE->set_context($context);
// Note that in the other cases, require_login will set the correct page context.
question_require_capability_on($question, 'use');
$PAGE->set_pagelayout('popup');
// Get and validate display options.
$options = new question_preview_options($question);
$slot = $quba->add_question($question);

$timenow = time();
if (true) {
    $variantoffset = rand(1, 100);
} else {
     $variantoffset = '1';
}

$quba->start_all_questions(
            new question_variant_pseudorandom_no_repeats_strategy($variantoffset), $timenow);

$actionurl = new moodle_url('/mod/qpractice/attempt.php', array('id' => $sessionid, 'displaynumber' => $displaynumber));
$stopurl = new moodle_url('/mod/qpractice/summary.php', array('id' => $sessionid));

// Process any actions from the buttons at the bottom of the form.
if (data_submitted()) {
    if (optional_param('next', null, PARAM_BOOL)) {
            $quba->process_all_actions();
            $quba->finish_all_questions();
            $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            $transaction->allow_commit();
            redirect($actionurl);

    } if (optional_param('finish', null, PARAM_BOOL)) {
            $quba->process_all_actions();
            $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            $transaction->allow_commit();
            redirect($stopurl);
    }
}


// Start output.
$title = get_string('practicesession', 'qpractice', format_string($question->name));
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

// Start the question form.
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $actionurl,
        'enctype' => 'multipart/form-data', 'id' => 'responseform'));


// Output the question.
echo $quba->render_question($slot, $options, $displaynumber);

// Finish the question form.
echo html_writer::start_tag('div', array('id' => 'previewcontrols', 'class' => 'controls'));
echo html_writer::empty_tag('input', array('type' => 'submit',
        'name' => 'next', 'value' => get_string('nextquestion', 'qpractice')));
echo html_writer::empty_tag('input', array('type' => 'submit',
        'name' => 'finish',  'value' => get_string('stoppractice', 'qpractice')));
echo html_writer::end_tag('div');
echo html_writer::end_tag('form');


// Display the settings form.

echo $OUTPUT->footer();

