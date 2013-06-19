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

// Get and validate question id.
$cmid = required_param('id', PARAM_INT);
$categoryid='8';
$subcategories='false';
$excludedqtypes=null;

        
        if ($subcategories) {
            $categoryids = question_categorylist($categoryid);
        } else {
            $categoryids = array($categoryid);
        }

        $questionids = question_bank::get_finder()->get_questions_from_categories(
                $categoryids, $excludedqtypes);
        
		print_object($questionids);
     

$question = question_bank::load_question($cmid);
//print_object($question);

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
print_object($options);
    $quba = question_engine::make_questions_usage_by_activity(
            'core_question_preview', context_user::instance($USER->id));
    $quba->set_preferred_behaviour($options->behaviour);
    $slot = $quba->add_question($question, $options->maxmark);
	echo $slot;
	$timenow = time();
	 if (true) {
        $variantoffset = rand(1, 100);
    } else {
        $variantoffset = '1';
    }
    $quba->start_all_questions(
            new question_variant_pseudorandom_no_repeats_strategy($variantoffset), $timenow);
	

    $transaction = $DB->start_delegated_transaction();
    question_engine::save_questions_usage_by_activity($quba);
    $transaction->allow_commit();

$options->behaviour = $quba->get_preferred_behaviour();
$options->maxmark = $quba->get_question_max_mark($slot);


// Process any actions from the buttons at the bottom of the form.
if (data_submitted() && confirm_sesskey()) {
       if (optional_param('finish', null, PARAM_BOOL)) {
            $quba->process_all_actions();
            $quba->finish_all_questions();

            $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            $transaction->allow_commit();
            redirect($actionurl);

        } else {
            $quba->process_all_actions();

            $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            $transaction->allow_commit();

            $scrollpos = optional_param('scrollpos', '', PARAM_RAW);
            if ($scrollpos !== '') {
                $actionurl->param('scrollpos', (int) $scrollpos);
            }
            redirect($actionurl);
        }
}

if ($question->length) {
    $displaynumber = '2';
} else {
    $displaynumber = 'ii';
}

// Prepare technical info to be output.
$qa = $quba->get_question_attempt($slot);
//print_object($qa);

// Start output.
$title = get_string('practicesession', 'qpractice', format_string($question->name));
$headtags = question_engine::initialise_js() . $quba->render_question_head_html($slot);
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();


// Output the question.
echo $quba->render_question($slot, $options, $displaynumber);

// Finish the question form.
echo html_writer::start_tag('div', array('id' => 'previewcontrols', 'class' => 'controls'));
echo html_writer::empty_tag('input',array('type' => 'submit',
        'name' => 'next', 'value' => get_string('nextquestion', 'qpractice')));
echo html_writer::empty_tag('input', array('type' => 'submit',
        'name' => 'finish',  'value' => get_string('stoppractice', 'qpractice')));
echo html_writer::end_tag('div');
echo html_writer::end_tag('form');

// Display the settings form.

echo $OUTPUT->footer();

