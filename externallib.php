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
require_once($CFG->libdir . "/externallib.php");

class local_bookingapi_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function bookings_parameters() {
        return new external_function_parameters(
                array('courseid' => new external_value(PARAM_TEXT, 'Course id', VALUE_DEFAULT, '0'))
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function bookings($courseid = '0') {
        global $USER;

        //Parameter validation
        //REQUIRED
        $params = self::validate_parameters(self::bookings_parameters(),
                array('courseid' => $courseid));

        //Context validation
        //OPTIONAL but in most web service it should present
        $context = context_user::instance($USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        if (!has_capability('moodle/user:viewdetails', $context)) {
            throw new moodle_exception('cannotviewprofile');
        }

        return "Vrnem: " . $params['courseid'];
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function bookings_returns() {
        return new external_value(PARAM_TEXT, 'All bokings for course.');
    }



}