<?php


// key to authenticate
define('INDEX_AUTH', '1');

/* File Viewer */

require '../sysconfig.inc.php';
require SB.'admin/default/session.inc.php';
require SB.'admin/default/session_check.inc.php';

// privileges checking
$can_read = utility::havePrivilege('bibliography', 'r');
if (!$can_read) {
    die('<div class="errorBox">You dont have enough privileges to view this section</div>');
}

// get file ID
$fileID = isset($_GET['fid'])?(integer)$_GET['fid']:0;

// query file to database
$file_q = $dbs->query('SELECT * FROM files WHERE file_id='.$fileID);
$file_d = $file_q->fetch_assoc();

if ($file_q->num_rows > 0) {
    $file_loc = REPOBS.str_ireplace('/', DS, $file_d['file_dir']).DS.$file_d['file_name'];
    if (file_exists($file_loc)) {
        header('Content-Disposition: inline; filename="'.basename($file_loc).'"');
        header('Content-Type: '.$file_d['mime_type']);
        readfile($file_loc);
        exit();
    } else {
        if ($file_d['mime_type'] == 'text/uri-list') {
            header('Location: '.$file_d['file_url']);
            exit();
        }
        die('<div class="errorBox">File Metadata exists in database BUT '.$file_loc.' does\'t exists in repository!</div>');
    }
} else {
  die('<div class="errorBox">File Not Found!</div>');
}
