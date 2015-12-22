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
 * The qpractice_viewed event.
 *
 * @package    mod_qpractice
 * @copyright  2015 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace qpractice\viewed;
defined('MOODLE_INTERNAL') || die();
/**
 * The qpractice_viewed  event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - PUT INFO HERE
 * }
 *
 * @since     Moodle 2.9
 * @copyright 2015 Marcus Green
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/
namespace mod_qpractice\event;

class qpractice_viewed extends \core\event\base {
    protected function init() {
        //$this->data['crud'] = 'c'; // c(reate), r(ead), u(pdate), d(elete)
        //$this->data['edulevel'] = self::LEVEL_PARTICIPATING;
        $this->data['objecttable'] = 'mod_qpractice';
    }
 
    public static function get_name() {
        return get_string('eventqpractice_viewed', 'mod_qpractice');
    }
 
    public function get_description() {
        //return "The user with id {$this->userid} viewed qpractice id :  {$this->objectid}.";
        return "mavg";
    }
 
    public function get_url() {
        return "mavg";
        //return new \moodle_url('/mod/qpractice/view.php', array('view' => $this->objectid));
    }
 
    public function get_legacy_logdata() {
        return "mavg";
        //return array($course->id, 'qpractice', 'view', "view.php?id={$cm->id}", $qpractice->id, $cm->id);        
    }
 
}