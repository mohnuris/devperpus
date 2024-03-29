<?php

/* Membership module submenu items */
// IP based access limitation
do_checkIP('smc');
do_checkIP('smc-system');

$menu[] = array('Header', __('System'));
// only administrator have privileges for below menus
if ($_SESSION['uid'] == 1) {
    $menu[] = array(__('System Configuration'), MWB . 'system/index.php', __('Configure Global System Preferences'));
    $menu[] = array(__('System Environment'), MWB . 'system/envinfo.php', __('Information about System Environment'));
    $menu[] = array(__('UCS Setting'), MWB . 'system/ucsetting.php', __('Configure UCS Preferences'));
    $menu[] = array(__('Theme'), MWB . 'system/theme.php', __('Configure theme Preferences'));
}
$menu[] = array(__('Content'), MWB . 'system/content.php', __('Content'));
// only administrator have privileges for below menus
if ($_SESSION['uid'] == 1) {
    $menu[] = array(__('Biblio Indexes'), MWB . 'system/biblio_indexes.php', __('Bibliographic Indexes management'));
    $menu[] = array(__('Modules'), MWB . 'system/module.php', __('Configure Application Modules'));
    $menu[] = array(__('Librarian & System Users'), MWB . 'system/app_user.php', __('Manage Application User or Library Staff'));
    $menu[] = array(__('User Group'), MWB . 'system/user_group.php', __('Manage Group of Application User'));
}
$menu[] = array(__('Shortcut Setting'), MWB . 'system/shortcut.php', __('Shortcut Setting'));
$menu[] = array(__('Holiday Setting'), MWB . 'system/holiday.php', __('Configure Holiday Setting'));
$menu[] = array(__('Barcode Generator'), MWB . 'system/barcode_generator.php', __('Barcode Generator'));
$menu[] = array(__('System Log'), MWB . 'system/sys_log.php', __('View Application System Log'));
$menu[] = array(__('Database Backup'), MWB . 'system/backup.php', __('Backup Application Database'));
