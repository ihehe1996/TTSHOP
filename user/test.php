<?php



require_once '../user/globals.php';

if (empty($action)) {


    include View::getUserView('_header');
    require_once(View::getUserView('test'));
    include View::getUserView('_footer');
    View::output();
}

if ($action == 'upload_cover') {
    $Media_Model = new Media_Model();
    $ret = uploadCropImg();
    $Media_Model->addMedia($ret['file_info']);
    Output::ok($ret['file_info']['file_path']);
}