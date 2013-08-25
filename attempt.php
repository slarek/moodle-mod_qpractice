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
require_once($CFG->libdir . '/filelib.php');

$sessionid = required_param('id', PARAM_INT);
$scrollpos = optional_param('scrollpos', '1', PARAM_INT);
$session = $DB->get_record('qpractice_session', array('id' => $sessionid));
$resultid = optional_param ('resultid', '', PARAM_INT);
$categoryid = $session->categoryid;

require_login();

$cm = get_coursemodule_from_instance('qpractice', $session->qpracticeid);
$course = $DB->get_record('course', array('id' => $cm->course));

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$quba = question_engine::load_questions_usage_by_activity($session->questionusageid);

$results = $DB->get_records_menu('question_attempts', array('questionusageid'=>$session->questionusageid), 'id', 'id, questionid');

if ($scrollpos=='1') {
       $questionid=choose_other_question($categoryid, $results);

    if ($questionid==null) {
          $viewurl = new moodle_url('/mod/qpractice/summary.php', array('id'=>$sessionid));
          redirect($viewurl, 'Sorry.No more questions to display.Try different category');
    } else {
           $resultid = $questionid->id;
    }
}

$question = question_bank::load_question($resultid, false);

$options = new question_display_options();

$slot = $quba->add_question($question);

$quba->start_question($slot);

$updatesql2 = "UPDATE {qpractice_session}
                          SET totalnoofquestions = ?
                        WHERE id=?";
            $return = $DB->execute($updatesql2, array($slot, $sessionid));

$actionurl = new moodle_url('/mod/qpractice/attempt.php', array('id' => $sessionid));
$stopurl = new moodle_url('/mod/qpractice/summary.php', array('id' => $sessionid));

if (data_submitted()) {
    if (optional_param('next', null, PARAM_BOOL)) {
            // $transaction = $DB->start_delegated_transaction();
            question_engine::save_questions_usage_by_activity($quba);
            // $transaction->allow_commit();
            redirect($actionurl);

    } else if (optional_param('finish', null, PARAM_BOOL)) {
            $DB->set_field('qpractice_session', 'status', 'finished', array('id' => $sessionid));
            question_engine::save_questions_usage_by_activity($quba);
            redirect($stopurl);
    } else {
            $quba->process_all_actions();
            $fraction = $quba->get_question_fraction($slot);
            $maxmarks = $quba->get_question_max_mark($slot);
            $obtainedmarks = $fraction*$maxmarks;
            $updatesql = "UPDATE {qpractice_session}
                          SET marksobtained = marksobtained + ?, totalmarks = totalmarks + ?
                        WHERE id=?";
            $return = $DB->execute($updatesql, array($obtainedmarks, $maxmarks, $sessionid));

            if ($fraction>0) {
                $updatesql1 = "UPDATE {qpractice_session}
                          SET totalnoofquestionsright = totalnoofquestionsright + '1'
                        WHERE id=?";
                $return = $DB->execute($updatesql1, array($sessionid));
            }
            $scrollpos = '';
    }
}


// Start output.
$PAGE->set_url('/mod/qpractice/attempt.php', array('id' => $sessionid));
$title = get_string('practicesession', 'qpractice', format_string($question->name));
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_context($context);
echo $OUTPUT->header();

// Start the question form.
echo html_writer::start_tag('form', array('method' => 'post', 'action' => $actionurl,
        'enctype' => 'multipart/form-data', 'id' => 'responseform'));
echo html_writer::start_tag('div');
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'slots', 'value' => $slot));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'resultid', 'value' => $resultid));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'scrollpos', 'value' => '0', 'id' => 'scrollpos'));
echo html_writer::end_tag('div');

// Output the question.
echo $quba->render_question($slot, $options, $slot);

// Finish the question form.
echo html_writer::start_tag('div');
echo html_writer::empty_tag('input', array('type' => 'submit',
        'name' => 'next', 'value' => get_string('nextquestion', 'qpractice')));
echo html_writer::empty_tag('input', array('type' => 'submit',
        'name' => 'finish',  'value' => get_string('stoppractice', 'qpractice')));
echo html_writer::end_tag('div');
echo html_writer::end_tag('form');


// Display the settings form.

echo $OUTPUT->footer();

