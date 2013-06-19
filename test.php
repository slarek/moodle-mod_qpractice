<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once($CFG->libdir . '/questionlib.php');

print_object(question_engine::get_behaviour_options());

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/test.php');

$form = new test_radio_form();
echo $OUTPUT->header();
$form->display();
echo $OUTPUT->footer();
