<?php

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
 * External Web Service Template
 *
 * @package    localbookingapi
 * @copyright  2014 Andraž Prinčič (http://www.princic.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once($CFG->libdir . "/externallib.php");
require_once($CFG->libdir . "/filelib.php");
require_once($CFG->libdir . "/datalib.php");

class local_bookingapi_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function bookings_parameters() {
        return new external_function_parameters(
            array(
		'courseid' => new external_value(PARAM_TEXT, 'Course id', VALUE_DEFAULT, '0'),
		'printusers' => new external_value(PARAM_TEXT, 'Print user profiles', VALUE_DEFAULT, '0')
		)
            );
    }
    
    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function bookings($courseid = '0', $printusers = '0') {
        global $DB;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::bookings_parameters(),
            array('courseid' => $courseid, 'printusers' => $printusers));

        $bookings = $DB->get_records("booking", array("course" => $courseid));

        foreach ($bookings as $booking) {
            $records = $DB->get_records('booking_options', array('bookingid' => $booking->id));

            $cm = get_coursemodule_from_instance('booking', $booking->id);
            $context = context_module::instance($cm->id);

		$booking->cm = $cm;
            $booking->intro = file_rewrite_pluginfile_urls($booking->intro, 'pluginfile.php',
                $context->id, 'mod_booking', 'intro', null);

            if ($booking->categoryid != '0') {
                    $categoryies = explode(',', $booking->categoryid);

                    if (count($categoryies) > 0) { 
                        foreach ($categoryies as $category) {
                            $booking->categories->{$category} = new stdClass();
                            $booking->categories->{$category} = $DB->get_field('booking_category', 'name', array('id' => $category));
                        }
                    }
                }

            $booking->booking_options = new stdClass();
            foreach ($records as $record) {
                $booking->booking_options->{$record->id} = new stdClass();
                $booking->booking_options->{$record->id} = $record;

		if ($printusers) {
                $users = $DB->get_records('booking_answers', array('bookingid' => $record->bookingid, 'optionid' => $record->id));
                foreach ($users as $user) {
                    $ruser = $DB->get_record('user', array('id' => $user->userid));
                    $booking->booking_options->{$record->id}->users->{$ruser->id} = new stdClass();
                    $booking->booking_options->{$record->id}->users->{$ruser->id} = $ruser;
                }
		}

                $users = $DB->get_records('booking_teachers', array('bookingid' => $record->bookingid, 'optionid' => $record->id));
                foreach ($users as $user) {
                    $ruser = $DB->get_record('user', array('id' => $user->userid));
                    $booking->booking_options->{$record->id}->teachers->{$ruser->id} = new stdClass();
                    $booking->booking_options->{$record->id}->teachers->{$ruser->id} = $ruser;
                }
            }
        }

        return json_encode($bookings);
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function bookings_returns() {
        return new external_value(PARAM_RAW, 'All bokings for course.');
    }



}
