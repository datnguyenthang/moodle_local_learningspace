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
 * This plugin serves as a database and plan for all learning activities in the organization,
 * where such activities are organized for a more structured learning space.
 * @package    local_learningspace
 * @copyright  dat.nguyen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @author     Azmat Ullah <dat.nguyen@gmail.com>
 */

defined('MOODLE_INTERNAL') || die();
require_once("{$CFG->libdir}/formslib.php");
/**
 * Defines the learning path form.
 */
class learningspace_form extends moodleform {

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $DB;
        $mform = $this->_form;

        // Add hidden element for ID to handle edit.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('text', 'name', get_string('name', 'local_learningspace'), ['size' => 100]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('requiredelement', 'form'), 'required', null, 'client');
        
        $mform->addElement('textarea', 'description', get_string('description', 'local_learningspace'));
        $mform->setType('description', PARAM_TEXT);

        // Autocomplete for cohort IDs.
        $cohorts = $DB->get_records('cohort', null, 'name ASC', 'id, name');
        $cohortoptions = [];
        foreach ($cohorts as $cohort) {
            $cohortoptions[$cohort->id] = $cohort->name;
        }
        $mform->addElement('autocomplete', 'cohort_ids', get_string('cohort_ids', 'local_learningspace'), $cohortoptions, [
            'multiple' => true,
            'noselectionstring' => get_string('noselection', 'form')
        ]);
        $mform->setType('cohort_ids', PARAM_SEQUENCE); 


        // Autocomplete for user IDs.
        $users = $DB->get_records('user', ['deleted' => 0], 'lastname ASC', 'id, CONCAT(firstname, " ", lastname) AS fullname');
        $useroptions = [];
        foreach ($users as $user) {
            $useroptions[$user->id] = $user->fullname;
        }
        $mform->addElement('autocomplete', 'user_ids', get_string('user_ids', 'local_learningspace'), $useroptions, [
            'multiple' => true,
            'noselectionstring' => get_string('noselection', 'form')
        ]);
        $mform->setType('user_ids', PARAM_SEQUENCE);


        // Autocomplete for owner IDs.
        $mform->addElement('autocomplete', 'owner_ids', get_string('owner_ids', 'local_learningspace'), $useroptions, [
            'multiple' => true,
            'noselectionstring' => get_string('noselection', 'form')
        ]);
        $mform->setType('owner_ids', PARAM_SEQUENCE);              

        // Published field. 
        $mform->addElement('advcheckbox', 'published', get_string('published', 'local_learningspace')); 
        $mform->setType('published', PARAM_BOOL);

        $mform->addElement('selectyesno', 'is_default', get_string('is_default', 'local_learningspace'));

        $this->add_action_buttons();
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        return $errors;
    }
}
