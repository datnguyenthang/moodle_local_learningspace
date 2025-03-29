<?php
require_once('../../config.php');

require_login();
if (!is_siteadmin()) {
    redirect(new moodle_url('/'), get_string('accessdenied', 'error'), null, \core\output\notification::NOTIFY_ERROR);
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url('/local/learningspace/admin.php');
$PAGE->set_title(get_string('managelearningspace', 'local_learningspace'));

$learningspace = new \local_learningspace\output\index();

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('managelearningspace', 'local_learningspace'));

echo $OUTPUT->render_from_template('local_learningspace/index', $learningspace->render_table());

echo $OUTPUT->footer();