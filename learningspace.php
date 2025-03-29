<?php
require_once('../../config.php');
require_once('forms/learningspace_form.php');
require_once('lib.php');

global $DB, $PAGE, $OUTPUT;

// Set up the page.
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('pluginname', 'local_learningspace'));
$PAGE->set_heading(get_string('pluginname', 'local_learningspace'));
$PAGE->set_pagelayout('standard');

$id = optional_param('id', 0, PARAM_INT);
$current_url = new moodle_url('/local/learningspace/learningspace.php', ['id' => $id]);
$PAGE->set_url($current_url);

$exclude_cohort_ids = '';
if ($id) {
    $learningspace = $DB->get_record('local_learningspace', ['id' => $id], '*', MUST_EXIST);
    $exclude_cohort_ids = $learningspace->cohort_ids;

    $learningspace->cohort_ids = explode(',', $learningspace->cohort_ids); 
    $learningspace->user_ids = explode(',', $learningspace->user_ids); 
    $learningspace->owner_ids = explode(',', $learningspace->owner_ids);
} else {
    $learningspace = new stdClass();
    $learningspace->id = 0;
}

$form = new learningspace_form($current_url, [
    'exclude_cohort_ids' => $exclude_cohort_ids
]);

if ($form->is_cancelled()) {
    // Handle form cancel operation, if cancel button is present on form.
    redirect(new moodle_url('/local/learningspace/admin.php'));
} else if ($data = $form->get_data()) {
    $data->cohort_ids = convert_array_to_str($data->cohort_ids); 
    $data->user_ids = convert_array_to_str($data->user_ids);
    $data->owner_ids = convert_array_to_str($data->owner_ids); 

    $data->timecreated = time();
    $data->timemodified = time();

    // Process validated data.
    if ($data->id) {
        $DB->update_record('local_learningspace', $data); // Update existing record.
    } else {
        $data->id = $DB->insert_record('local_learningspace', $data); // Insert new record.
    }
    cache_helper::purge_by_event('changesinwunderbytetable');
    redirect(new moodle_url('/local/learningspace/admin.php'));
} else {
    // Output the page.
    echo $OUTPUT->header();
    $form->set_data($learningspace);
    $form->display();
    echo $OUTPUT->footer();
}