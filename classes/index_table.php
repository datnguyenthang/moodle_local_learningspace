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
 * The Wunderbyte table class is an extension of the tablelib table_sql class.
 *
 * @package local_wunderbyte_table
 * @copyright 2023 Wunderbyte Gmbh <info@wunderbyte.at>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// phpcs:ignoreFile

namespace local_learningspace;

defined('MOODLE_INTERNAL') || die();

use local_wunderbyte_table\output\table;
use local_wunderbyte_table\wunderbyte_table;
use stdClass;

/**
 * Wunderbyte table demo class.
 */
class index_table extends wunderbyte_table {

    public function col_cohort_ids($values) {
        global $DB;
    
        // Convert string to array.
        $cohort_ids = $this->convert_str_to_arr($values->cohort_ids);
    
        // Fetch cohort records.
        list($inSql, $params) = $DB->get_in_or_equal($cohort_ids, SQL_PARAMS_NAMED);
        $cohorts = $DB->get_records_select('cohort', "id $inSql", $params, '', 'id, name');
        
        $html = '';
        foreach ($cohorts as $cohort) {
            // Display each cohort name as a tag.
            $html .= '<a target="_blank" href="/cohort/assign.php?id='.$cohort->id.'">
                        <span class="badge badge-info mb-2 mr-1 d-inline-flex align-items-center">' . htmlspecialchars($cohort->name) . '</span>
                    </a>';
        }
        return $html;
    }
    
    public function col_user_ids($values) {
        global $DB;
    
        // Convert string to array.
        $user_ids = $this->convert_str_to_arr($values->user_ids);
    
        // Fetch user records.
        list($inSql, $params) = $DB->get_in_or_equal($user_ids, SQL_PARAMS_NAMED);
        $users = $DB->get_records_select('user', "id $inSql", $params, '', 'id, firstname, lastname');
    
        $html = '';
        foreach ($users as $user) {
            // Combine first name and last name.
            $fullname = $user->firstname . ' ' . $user->lastname;
            
            // Display each user full name as a tag.
            $html .= '<a target="_blank" href="/user/profile.php?id='.$user->id.'">
                        <span class="badge badge-info mb-2 mr-1 d-inline-flex align-items-center">' . htmlspecialchars($fullname) . '</span>
                    </a>';
        }
        return $html;
    }
    
    public function col_owner_ids($values) {
        global $DB;
    
        // Convert string to array.
        $owner_ids = $this->convert_str_to_arr($values->owner_ids);
    
        // Fetch owner records.
        list($inSql, $params) = $DB->get_in_or_equal($owner_ids, SQL_PARAMS_NAMED);
        $owners = $DB->get_records_select('user', "id $inSql", $params, '', 'id, firstname, lastname');
    
        $html = '';
        foreach ($owners as $owner) {
            // Combine first name and last name.
            $fullname = $owner->firstname . ' ' . $owner->lastname;
            
            // Display each owner full name as a tag.
            $html .= '<a target="_blank" href="/user/profile.php?id='.$owner->id.'">
                        <span class="badge badge-info mb-2 mr-1 d-inline-flex align-items-center">' . htmlspecialchars($fullname) . '</span>
                      </a>';
        }
        return $html;
    }

    /**
     * Decodes the Unix Timestamp
     *
     * @param stdClass $values
     * @return void
     */
    public function col_timecreated($values) {
        return date('d/m/Y', strtotime($values->timecreated));
    }

    /**
     * Decodes the Unix Timestamp
     *
     * @param stdClass $values
     * @return void
     */
    public function col_enddate($values) {
        return date('d/m/Y', strtotime($values->enddate));
    }

    /**
     * Replace the value of the column with a string.
     *
     * @param stdClass $values
     * @return void
     */
    public function col_published($values) {
        global $OUTPUT;
        $data[] = [
            'label' => '',
            'class' => 'custom-control-input',
            'href' => '#',
            'iclass' => 'fa fa-edit',
            'id' => $values->id.'-'.$this->uniqueid,
            'name' => $this->uniqueid.'-'.$values->id,
            'methodname' => 'togglepublished',
            'ischeckbox' => true,
            'checked' => $values->published ? true : false,
            'disabled' => false,
            'nomodal' => true,
            'data' => [
                'id' => $values->id,
                'state' => $values->published ? 0 : 1,
                'value' => $values->published ? 0 : 1,
            ]
        ];
        table::transform_actionbuttons_array($data);

        return $OUTPUT->render_from_template('local_wunderbyte_table/component_actiontoggle', ['showactionbuttons' => $data]);
    }

    public function col_is_default($values) { 
        return $values->is_default == 1 ? get_string('yes') : get_string('no');
    }

    /**
     * This handles the action column with buttons, icons, checkboxes.
     *
     * @param stdClass $values
     * @return void
     */
    public function col_action($values) {
        global $OUTPUT;

        $data[] = [
            'label' => get_string('edit'), // Name of your action button.
            'class' => '',
            'href' => '/local/learningspace/learningspace.php?id='.$values->id, // You can either use the link, or JS, or both.
            'iclass' => 'fa fa-edit', // Add an icon before the label.
            'id' => $values->id.'-'.$this->uniqueid,
            'name' => $this->uniqueid.'-'.$values->id,
            //'methodname' => 'togglecheckbox', // The method needs to be added to your child of wunderbyte_table class.
            'ischeckbox' => false,
            'data' => [ // Will be added eg as data-id = $values->id, so values can be transmitted to the method above.
                'id' => $values->id,
                'labelcolumn' => 'username',
            ]
        ];

        // This transforms the array to make it easier to use in mustache template.
        table::transform_actionbuttons_array($data);

        return $OUTPUT->render_from_template('local_wunderbyte_table/component_actionbutton', ['showactionbuttons' => $data]);
    }

    /**
     * Toggle Checkbox
     *
     * @param int $id
     * @param string $data
     * @return array
     */
    /**
     * Toggle Checkbox
     *
     * @param int $id
     * @param string $data
     * @return array
     */
    public function action_togglepublished(int $id, string $data):array {
        global $DB;
        $dataobject = json_decode($data);
        $learingspace = $DB->get_record('local_learningspace', ['id' => $id]);
        $learingspace->published = $dataobject->value == '0' ? 0 : 1;
        $DB->update_record('local_learningspace', $learingspace);
        return [
            'success' => 1,
            'message' => $learingspace->published ? 'This learing space is now published!' : 'This learing space is no longer published!',
        ];
    }

    /**
     * Convert a comma-separated string to an array.
     *
     * @param string $str The input string.
     * @return array The resulting array.
     */
    public function convert_str_to_arr($str) { 
        // Check if the string contains a comma. 
        if (strpos($str, ',') !== false) { 
            // Split the string into an array using explode. 
            $arr = explode(',', $str); 
        } else { 
            // If there's no comma, treat the string as a single element array. 
            $arr = [$str]; 
        } 
        return $arr; 
    }
}
