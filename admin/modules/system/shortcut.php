<?php


/* Global application configuration */

// key to authenticate
if (!defined('INDEX_AUTH')) {
  define('INDEX_AUTH', '1');
}

// key to get full database access
define('DB_ACCESS', 'fa');

if (!defined('SB')) {
  // main system configuration
  require '../../../sysconfig.inc.php';
  // start the session
  require SB . 'admin/default/session.inc.php';
}
// IP based access limitation
require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-system');

// only administrator have privileges to change global settings
if ($_SESSION['uid'] != 1) {
  header('Location: ' . MWB . 'system/content.php');
  die();
}

require SB . 'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_FILE/simbio_directory.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_DB/simbio_dbop.inc.php';
require LIB . 'module.inc.php';

?>
<fieldset class="menuBox">
  <div class="menuBoxInner systemIcon">
    <div class="per_title">
      <h2><?php echo __('Shortcut Settings'); ?></h2>
    </div>
    <div class="infoBox">
      <?php echo __('Add or remove application shortcuts'); ?>
    </div>
    <?php
    if (isset($_POST['selectedShortcuts']) && count($_POST['selectedShortcuts'])) {
      $shortcuts = $dbs->escape_string(serialize($_POST['selectedShortcuts']));
      $dbs->query('REPLACE INTO setting (setting_name, setting_value) VALUES
        (\'shortcuts_' . $_SESSION['uid'] . '\', \'' . $shortcuts . '\')');
      echo '<div class="infoBox">' . __('Shortcut setting saved') . '</div>';
      // write log
      utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['realname'] . ' change application shortcuts');
    }
    ?>
  </div>
</fieldset>
<?php
/* main content */
ob_start();
?>
<form name="shortcut-form" class="shortcut-form submitViaAJAX" id="mainForm" method="post" action="<?php echo MWB . 'system/shortcut.php' ?>">
  <div class="row">
    <div class="col-md-5">
      <select class="form-control shortcuts-list" name="shortcutsOptions" id="shortcuts-options" multiple="multiple" size="10">
        <?php
        $modules = new module();
        $modules->setModulesDir(MDLBS);
        $menus = $modules->getModuleMainMenu($dbs, true);
        foreach ($menus as $main_menu) {
          $_formated_module_name = ucwords(str_replace('_', ' ', $main_menu['name']));
          echo '<optgroup label="' . strtoupper(__($_formated_module_name)) . '">';
          if (isset($main_menu['childs']) && $main_menu['childs']) {
            foreach ($main_menu['childs'] as $id => $main_menu_child) {
              if ($main_menu_child[0] == 'Header') {
                echo '<option disabled="disabled" class="option-disabled">' . $main_menu_child[1] . '</option>';
              } else {
                echo '<option value="' . $main_menu_child[0] . '|' . str_ireplace(MWB, '/', $main_menu_child[1]) . '" id="submenu_' . $id . '">&nbsp;&nbsp; ' . $main_menu_child[0] . '</option>';
              }
            }
          }
          echo '</optgroup>';
        }
        ?>
      </select>
    </div>
    <div class="col-md-2">
      <button type="button" class="btn btn-default btn-full btn-select-shortcuts"><?php echo __('Select') ?> <i class="glyphicon glyphicon-fast-forward"></i></button>
      <button type="button" class="btn btn-default btn-full btn-remove-shortcuts"><i class="glyphicon glyphicon-fast-backward"></i> <?php echo __('Move back') ?></button>
    </div>
    <div class="col-md-5">
      <select class="form-control shortcuts-list" name="selectedShortcuts[]" id="selected-shortcuts" multiple="multiple" size="10">
        <?php
        // current selected shortcuts
        $shortcuts_q = $dbs->query('SELECT * FROM setting WHERE setting_name LIKE \'shortcuts_' . $_SESSION['uid'] . '\'');
        $shortcuts_d = $shortcuts_q->fetch_assoc();
        if ($shortcuts_q->num_rows > 0) {
          $shortcuts = unserialize($shortcuts_d['setting_value']);
          foreach ($shortcuts as $shortcut) {
            echo '<option value="' . $shortcut . '">' . preg_replace('@\|.+$@i', '', $shortcut) . '</option>';
          }
        }
        ?>
      </select>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12"><input type="submit" class="btn btn-primary btn-full save-shortcuts" name="updateData" value="<?php echo __('Save') ?>" /></div>
  </div>
</form>
<script type="text/javascript">
  $('.btn-select-shortcuts').bind('click', function(e) {
    $('#shortcuts-options').find('option:selected').clone().appendTo('#selected-shortcuts');
    $('#selected-shortcuts').find('option').prop('selected', true);
  });

  $('.btn-remove-shortcuts').bind('click', function(e) {
    $('#selected-shortcuts').find('option:selected').remove();
    $('#selected-shortcuts').find('option').prop('selected', true);
  });
</script>
<?php
$form = ob_get_clean();
// print out the object
echo $form;
/* main content end */
