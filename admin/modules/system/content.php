<?php


/* Dynamic content Management section */

// key to authenticate
define('INDEX_AUTH', '1');
// key to get full database access
define('DB_ACCESS', 'fa');

// main system configuration
require '../../../sysconfig.inc.php';
// IP based access limitation
require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-system');
// start the session
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/template_parser/simbio_template_parser.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO . 'simbio_DB/simbio_dbop.inc.php';

// privileges checking
$can_read = utility::havePrivilege('system', 'r');
$can_write = utility::havePrivilege('system', 'w');

if (!$can_read) {
    die('<div class="errorBox">' . __('You don\'t have enough privileges to view this section') . '</div>');
}

/* RECORD OPERATION */
if (isset($_POST['saveData'])) {
    $contentTitle = trim(strip_tags($_POST['contentTitle']));
    $contentPath = trim(strip_tags($_POST['contentPath']));
    // check form validity
    if (empty($contentTitle) or empty($contentPath)) {
        utility::jsAlert(__('Title or Path can\'t be empty!'));
        exit();
    } else {
        $data['content_title'] = $dbs->escape_string(strip_tags(trim($contentTitle)));
        $data['content_path'] = strtolower($dbs->escape_string(strip_tags(trim($contentPath))));
        if ($_POST['isNews'] && $_POST['isNews'] == '1') {
            $data['is_news'] = 1;
        }
        $data['content_desc'] = $dbs->escape_string(trim($_POST['contentDesc']));
        $data['input_date'] = date('Y-m-d H:i:s');
        $data['last_update'] = date('Y-m-d H:i:s');

        // create sql op object
        $sql_op = new simbio_dbop($dbs);
        if (isset($_POST['updateRecordID'])) {
            /* UPDATE RECORD MODE */
            // remove input date
            unset($data['input_date']);
            // filter update record ID
            $updateRecordID = (int) $_POST['updateRecordID'];
            // update the data
            $update = $sql_op->update('content', $data, 'content_id=' . $updateRecordID);
            if ($update) {
                // write log
                utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['content_title'] . ' update content data (' . $data['content_title'] . ') with contentname (' . $data['contentname'] . ')');
                utility::jsAlert(__('Content data updated'));
                echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(parent.$.ajaxHistory[0].url);</script>';
            } else {
                utility::jsAlert(__('Content data FAILED to update!') . "\nDEBUG : " . $sql_op->error);
            }
            exit();
        } else {
            /* INSERT RECORD MODE */
            // insert the data
            if ($sql_op->insert('content', $data)) {
                // write log
                utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['realname'] . ' add new content (' . $data['content_title'] . ') with contentname (' . $data['contentname'] . ')');
                utility::jsAlert(__('Content data saved'));
                echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '\');</script>';
            } else {
                utility::jsAlert(__('Content data FAILED to save!') . "\n" . $sql_op->error);
            }
            exit();
        }
    }
    exit();
} else if (isset($_POST['itemID']) and !empty($_POST['itemID']) and isset($_POST['itemAction'])) {
    if (!($can_read and $can_write)) {
        die();
    }
    /* DATA DELETION PROCESS */
    $sql_op = new simbio_dbop($dbs);
    $failed_array = array();
    $error_num = 0;
    if (!is_array($_POST['itemID'])) {
        // make an array
        $_POST['itemID'] = array((int) $_POST['itemID']);
    }
    // loop array
    foreach ($_POST['itemID'] as $itemID) {
        $itemID = (int) $itemID;
        // get content data
        $content_q = $dbs->query('SELECT content_title FROM content WHERE content_id=' . $itemID);
        $content_d = $content_q->fetch_row();
        if (!$sql_op->delete('content', "content_id='$itemID'")) {
            $error_num++;
        } else {
            // write log
            utility::writeLogs($dbs, 'staff', $_SESSION['uid'], 'system', $_SESSION['realname'] . ' DELETE content (' . $content_d[0] . ')');
        }
    }

    // error alerting
    if ($error_num == 0) {
        utility::jsAlert(__('All Data Successfully Deleted'));
        echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '?' . $_POST['lastQueryStr'] . '\');</script>';
    } else {
        utility::jsAlert(__('Some or All Data NOT deleted successfully!\nPlease contact system administrator'));
        echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '?' . $_POST['lastQueryStr'] . '\');</script>';
    }
    exit();
}
/* RECORD OPERATION END */

/* search form */
?>
<fieldset class="menuBox">
    <div class="menuBoxInner systemIcon">
        <div class="per_title">
            <h2><?php echo __('Content'); ?></h2>
        </div>
        <div class="sub_section">
            <div class="btn-group">
                <a href="<?php echo MWB; ?>system/content.php" class="btn btn-default"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;<?php echo __('Content List'); ?></a>
                <a href="<?php echo MWB; ?>system/content.php?action=detail" class="btn btn-default"><i class="glyphicon glyphicon-plus"></i>&nbsp;<?php echo __('Add New Content'); ?></a>
            </div>
            <form name="search" action="<?php echo MWB; ?>system/content.php" id="search" method="get" style="display: inline;"><?php echo __('Search'); ?> :
                <input type="text" name="keywords" size="30" />
                <input type="submit" id="doSearch" value="<?php echo __('Search'); ?>" class="btn btn-default" />
            </form>
        </div>
    </div>
</fieldset>
<?php
/* main content */
if (isset($_POST['detail']) or (isset($_GET['action']) and $_GET['action'] == 'detail')) {
    if (!($can_read and $can_write)) {
        die('<div class="errorBox">' . __('You don\'t have enough privileges to view this section') . '</div>');
    }
    /* RECORD FORM */
    // try query
    $itemID = (int) isset($_POST['itemID']) ? $_POST['itemID'] : 0;
    $rec_q = $dbs->query('SELECT * FROM content WHERE content_id=' . $itemID);
    $rec_d = $rec_q->fetch_assoc();

    // create new instance
    $form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'], 'post');
    $form->submit_button_attr = 'name="saveData" value="' . __('Save') . '" class="btn btn-default"';

    // form table attributes
    $form->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $form->table_header_attr = 'class="alterCell" style="font-weight: bold;"';
    $form->table_content_attr = 'class="alterCell2"';

    // edit mode flag set
    if ($rec_q->num_rows > 0) {
        $form->edit_mode = true;
        // record ID for delete process
        // form record id
        $form->record_id = $itemID;
        // form record title
        $form->record_title = $rec_d['content_title'];
        // submit button attribute
        $form->submit_button_attr = 'name="saveData" value="' . __('Update') . '" class="btn btn-default"';
    }

    /* Form Element(s) */
    // content title
    $form->addTextField('text', 'contentTitle', __('Content Title') . '*', $rec_d['content_title'], 'style="width: 100%;"');
    // content news flag
    $news_chbox[0] = array('0', __('No'));
    $news_chbox[1] = array('1', __('Yes'));
    $form->addRadio('isNews', __('This is News'), $news_chbox, $rec_d['is_news']);
    // content path
    $form->addTextField('text', 'contentPath', __('Path (Must be unique)') . '*', $rec_d['content_path'], 'style="width: 50%;"');
    // content description
    $form->addTextField('textarea', 'contentDesc', __('Content Description'), htmlentities($rec_d['content_desc'], ENT_QUOTES), 'class="texteditor" tyle="width: 100%; height: 500px;"');

    // edit mode messagge
    if ($form->edit_mode) {
        echo '<div class="infoBox">' . __('You are going to update Content data'), ' : <b>' . $rec_d['content_title'] . '</b> <br />' . __('Last Updated') . $rec_d['last_update'] . '</div>'; //mfc
    }
    // print out the form object
    echo $form->printOut();
    // texteditor instance
    ?>
    <script type="text/javascript">
        $(document).ready(
                function() {
                    / *
                    $(\'# contentDesc \'). removeAttr (\ '
                        disable\ ');
                        tinymce.init({
                            pemilih: "textarea # contentDesc",
                            tema: "modern",
                            plugin: "media meja cari ganti kode directionality",
                            toolbar: "batalkan redo | styleselect | bold italic | alignleft aligncenter alignrightify | bullist numlist outdent indent | gambar tautan | cetak pratinjau media fullpage | forecolor backcolor emoticon",
                            content_css: "'. (SWB.'admin /'.$ sysconf [' admin_template '] [' css ']).'",
                            tinggi: 300
                        });
                        */
                        CKEDITOR.replace('contentDesc'); $(document).bind('formEnabled', function() {
                            CKEDITOR.instances.contentDesc.setReadOnly(false);
                        });
                    }
                );
    </script>
<?php
} else {
    /* USER LIST */
    // table spec
    $table_spec = 'content AS c';

    // create datagrid
    $datagrid = new simbio_datagrid();
    if ($can_read and $can_write) {
        $datagrid->setSQLColumn(
            'c.content_id',
            'c.content_title AS \'' . __('Content Title') . '\'',
            'c.content_path AS \'' . __('Path (Must be unique)') . '\'',
            'c.last_update AS \'' . __('Last Updated') . '\''
        );
    } else {
        $datagrid->setSQLColumn(
            'c.content_title AS \'' . __('Content Title') . '\'',
            'c.content_path AS \'' . __('Path (Must be unique)') . '\'',
            'c.last_update AS \'' . __('Last Updated') . '\''
        );
    }
    $datagrid->setSQLorder('c.last_update DESC');

    // is there any search
    $criteria = 'c.content_id IS NOT NULL ';
    if (isset($_GET['keywords']) and $_GET['keywords']) {
        $keywords = $dbs->escape_string($_GET['keywords']);
        $criteria .= " AND MATCH(content_title, content_desc) AGAINST('$keywords')";
    }
    $datagrid->setSQLCriteria($criteria);

    // set table and table header attributes
    $datagrid->table_attr = 'align="center" id="dataList" cellpadding="5" cellspacing="0"';
    $datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
    // set delete proccess URL
    $datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];

    // put the result into variables
    $datagrid_result = $datagrid->createDataGrid($dbs, $table_spec, 20, ($can_read and $can_write));
    if (isset($_GET['keywords']) and $_GET['keywords']) {
        $msg = str_replace('{result->num_rows}', $datagrid->num_rows, __('Found <strong>{result->num_rows}</strong> from your keywords')); //mfc
        echo '<div class="infoBox">' . $msg . ' : "' . $_GET['keywords'] . '"</div>';
    }

    echo $datagrid_result;
}
/* main content end */
