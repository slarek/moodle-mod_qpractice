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
 * The main qpractice configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_qpractice
 * @copyright  2013 Jayesh Anandani
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");


class mod_qpractice_startattempt_form extends moodleform {

    /**
     * Defines forms elements
     */
    public function definition() {

        $mform = $this->_form;

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $options = array(
                        '0' => 'Topic-1',
                        '1' => 'Topic-2',
                        '2' => 'Topic-3'      
                    );
        $select = $mform->addElement('select', 'categories', get_string('category'), $options);
        $select->setSelected('0');

        $mform->addElement('header', 'qpracticebehaviour', get_string('qpracticebehaviour', 'qpractice'));
        $options = array(
                        '0' => 'Adaptive Mode',
                        '1' => 'Adaptive Mode with no Penalties',
                        '2' => 'Interactive Mode'      
                    );
        $select = $mform->addElement('select', 'categories', get_string('category'), $options);
        $select->setSelected('0');


        $mform->addElement('header', 'qpracticeset', get_string('qpracticeset', 'qpractice'));

        $mform->addElement('radio', 'optiontype', '', 'Normal Practice', 1);

        $mform->addElement('radio', 'optiontype', '', 'Time Achiever', 2);
        $mform->addElement('duration', 'timelimit', 'Time Duration');
        $mform->disabledIf('timelimit', 'optiontype', 'neq', 2);

        $mform->addElement('radio', 'optiontype', '', 'Goal Achiever', 3);
        $mform->addElement('text', 'name1', 'Enter Goal percentage');
        $mform->setType('name1', PARAM_TEXT);
        $mform->addElement('text', 'name2', 'Enter number of questions');
        $mform->setType('name2', PARAM_TEXT);
        $mform->disabledIf('name1', 'optiontype', 'neq', 3);
        $mform->disabledIf('name2', 'optiontype', 'neq', 3);

        $mform->setDefault('optiontype', 1);

        // add standard buttons, common to all modules
        $this->add_action_buttons();

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'returnurl', 0);
        $mform->setType('returnurl', PARAM_LOCALURL);
        $mform->addElement('hidden', 'nexturl', 0);
        $mform->setType('nexturl', PARAM_LOCALURL);
    }
}


