<?php

// be sure that this file not accessed directly
if (!defined('INDEX_AUTH')) {
    die("can not access this file directly");
} elseif (INDEX_AUTH != 1) {
    die("can not access this file directly");
}

/* LOCAL DATABASE CONNECTION config */
// database constant
// change below setting according to your database configuration
define('DB_HOST', '_DB_HOST_');
define('DB_PORT', '_DB_PORT_');
define('DB_NAME', '_DB_NAME_');
define('DB_USERNAME', '_DB_USER_');
define('DB_PASSWORD', '_DB_PASSWORD_');

// define any other sysconfig variables below
$sysconf['index']['type'] = 'index';

/**
 * UCS settings
 */
$sysconf['ucs']['enable'] = false;
// auto delete same record on UCS?
$sysconf['ucs']['auto_delete'] = true;
// auto insert new record to UCS?
$sysconf['ucs']['auto_insert'] = true;

// additional e-mail recipients for library administrator
/*
$sysconf['mail']['add_recipients'][] = array('from' => 'senayan.slims@slims.web.id', 'from_name' => 'Librarian 2');
$sysconf['mail']['add_recipients'][] = array('from' => 'wynerst@gmail.com', 'from_name' => 'Librarian 3');
*/
