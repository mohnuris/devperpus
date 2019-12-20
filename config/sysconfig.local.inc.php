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
define('DB_HOST', 'localhost');
define('DB_NAME', 'perpus_unuja');
define('DB_USERNAME', 'admin');
define('DB_PASSWORD', '123');

// define any other sysconfig variables below
$sysconf['index']['type'] = 'default';

// additional e-mail recipients for library administrator
/*
$sysconf['mail']['add_recipients'][] = array('from' => 'senayan.slims@slims.web.id', 'from_name' => 'Librarian 2');
$sysconf['mail']['add_recipients'][] = array('from' => 'wynerst@gmail.com', 'from_name' => 'Librarian 3');
*/
