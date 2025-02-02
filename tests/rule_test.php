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
 * Unit tests for the quizaccess_proctoring plugin.
 *
 * @package    quizaccess_proctoring
 * @copyright  2020 Brain Station 23
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/accessrule/proctoring/rule.php');
require_once($CFG->dirroot . '/mod/quiz/accessrule/proctoring/lib.php');

/**
 * Unit tests for the quizaccess_proctoring plugin.
 *
 * @copyright  2020 Brain Station 23
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quizaccess_proctoring_testcase extends basic_testcase {

    /**
     * Test case to check the rule basics
     */
    public function test_proctoring_access_rule() {
        $quiz = new stdClass();
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new quizaccess_proctoring($quizobj, 0);
        $attempt = new stdClass();

        $this->assertFalse($rule->prevent_access());
        $this->assertFalse($rule->prevent_new_attempt(0, $attempt));
        $this->assertFalse($rule->is_finished(0, $attempt));
        $this->assertFalse($rule->end_time($attempt));
        $this->assertFalse($rule->time_left_display($attempt, 0));
        $this->assertFalse($rule->attempt_must_be_in_popup());
    }

    /**
     * Test case to check if the proper message is producing form the empty object validation method
     *
     * @throws coding_exception
     */
    public function test_validate_preflight_check() {
        $quiz = new stdClass();
        $cm = new stdClass();
        $cm->id = 0;
        $quizobj = new quiz($quiz, $cm, null);
        $rule = new quizaccess_proctoring($quizobj, 0);
        $data['proctoring'] = '';
        $errors = $rule->validate_preflight_check($data, [], [], 0);
        $string = get_string('youmustagree', 'quizaccess_proctoring');

        $this->assertEquals($errors['proctoring'], $string);
    }

    /**
     * Test case to check if aws api response log is inserted correctly or not
     *
     * @throws coding_exception
     */
    public function test_log_aws_api_call() {
        global $DB;
        $reportid = 0; 
        $apiresponse = "{ test: success }";
        log_aws_api_call($reportid, $apiresponse);
        
        $log = $DB->get_records('aws_api_log', array('reportid' => $reportid));
        $count = count($log);
        $this->assertEquals($count, 1);
    }

    /**
     * Test description array
     *
     * @throws coding_exception
     */
    public function test_description() {
        $description = description();
        $this->assertEquals(gettype ($description), 'array');
    }

    /**
     * Test save settings
     *
     * @throws coding_exception
     */
    public function test_save_settings() {
        global $DB;
        $quiz = new stdClass();
        $quiz->id = 0;
        $quiz->proctoringrequired = 1;
        save_settings($quiz);
        $this->assertEquals($DB->record_exists('quizaccess_proctoring', array('quizid' => 0)), true);
    }

    /**
     * Test save settings
     *
     * @throws coding_exception
     */
    public function test_make_modal_content() {
        $modalhtml = make_modal_content(null, "1", "1");
        $this->assertEquals(gettype ($modalhtml), 'string');
    }


    /**
     * Test is_preflight_check_required
     *
     * @throws coding_exception
     */
    public function test_is_preflight_check_required() {
        $checkflag = is_preflight_check_required(0);
        $this->assertFalse($checkflag);
    }

    /**
     * Test courseid cmid response format
     *
     * @throws coding_exception
     */
    public function test_get_courseid_cmid_from_preflight_form() {
        $response = get_courseid_cmid_from_preflight_form(null);
        $this->assertEquals(gettype ($response), 'array');
    }

    /**
     * Test get_download_config_button
     *
     * @throws coding_exception
     */
    public function test_get_download_config_button() {
        $response = get_download_config_button();
        $this->assertEquals(gettype ($response), 'string');
    }
}
