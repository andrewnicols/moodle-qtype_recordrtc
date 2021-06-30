<?php
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/questionlib.php');

$context = context_system::instance();

require_login();
require_capability('moodle/site:config', $context);

$PAGE->set_context($context);
$PAGE->set_url('/question/type/recordrtc/test.php');
$PAGE->set_title('Recording test');

$mediatype = 'audio';
$aspectclass = '';
$widthattribute = '';
$maxrecordingduration = 120;
$filename = 'recording.mp3';
$norecordinghideclass = '';
$norecordinglangstring = 'No recording';
$mediaplayerhideclass = 'hide ';
$recordingurl = '';
$state = 'new';
$label = 'Start recording';

echo $OUTPUT->header();

echo html_writer::start_div('', ['id' => 'anid']) . 'Record something: ';
echo '
    <span class="' . $mediatype . '-widget' . $aspectclass . '"' . $widthattribute . ' data-media-type="' . $mediatype .
        '" data-max-recording-duration="' . $maxrecordingduration . '" data-recording-filename="' . $filename . '">
        <span class="' . $norecordinghideclass . 'no-recording-placeholder">' . $norecordinglangstring . '</span>
        <span class="' . $mediaplayerhideclass . 'media-player">
            <' . $mediatype . ' controls>
                <source src="' . $recordingurl . '">
            </' . $mediatype . '>
        </span>
        <span class="record-button">
            <button type="button" class="btn btn-outline-danger osep-smallbutton"
                    data-state="' . $state . '">' . $label . '</button>
        </span>
    </span>';
echo html_writer::end_div();

$PAGE->requires->strings_for_js([
        'gumabort',
        'gumabort_title',
        'gumnotallowed',
        'gumnotallowed_title',
        'gumnotfound',
        'gumnotfound_title',
        'gumnotreadable',
        'gumnotreadable_title',
        'gumnotsupported',
        'gumnotsupported_title',
        'gumoverconstrained',
        'gumoverconstrained_title',
        'gumsecurity',
        'gumsecurity_title',
        'gumtype',
        'gumtype_title',
        'nearingmaxsize',
        'nearingmaxsize_title',
        'recordagain',
        'recordingfailed',
        'recordinginprogress',
        'startcamera',
        'startrecording',
        'uploadaborted',
        'uploadcomplete',
        'uploadfailed',
        'uploadfailed404',
        'uploadpreparing',
        'uploadprogress',
], 'qtype_recordrtc');

$repositories = repository::get_instances(
        ['type' => 'upload', 'currentcontext' => $context]);
if (empty($repositories)) {
    throw new moodle_exception('errornouploadrepo', 'moodle');
}
$uploadrepository = reset($repositories); // Get the first (and only) upload repo.

$draftitemid = 0; // Will be filled in by file_prepare_draft_area.
file_prepare_draft_area($draftitemid, $context->id, 'test',
        'test', 0, null, '');

$PAGE->requires->js_call_amd('qtype_recordrtc/avrecording', 'init',
        ['anid', [
                'audioBitRate' => (int) get_config('qtype_recordrtc', 'audiobitrate'),
                'videoBitRate' => (int) get_config('qtype_recordrtc', 'videobitrate'),
                'videoWidth' => 640,
                'videoHeight' => 480,
                'maxUploadSize' => 1000000000,
                'uploadRepositoryId' => (int) $uploadrepository->id,
                'contextId' => $context->id,
                'draftItemId' => $draftitemid,
        ]]);

echo $OUTPUT->footer();
