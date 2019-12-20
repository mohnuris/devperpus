<?php


/* Biblio Item List */

// key to authenticate
define('INDEX_AUTH', '1');

// main system configuration
require '../../../sysconfig.inc.php';

if (isset($_GET['itemID'])) {
  $_POST['itemID'] = $_GET['itemID'];
}

$_GET['inPopUp'] = true;

ob_start();
require MDLBS . 'bibliography/item.php';
$content = ob_get_clean();

// page title
$page_title = 'Bibliography Items';

// include the page template
require SB . '/admin/' . $sysconf['admin_template']['dir'] . '/notemplate_page_tpl.php';
