<?php


/*
A Handler script for AJAX Lookup
Database
Arie Nugraha 2007
*/

// key to authenticate
define('INDEX_AUTH', '1');

require_once '../sysconfig.inc.php';
// session checking
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';

// list limit
$limit = 20;

$table_name = $dbs->escape_string(trim($_POST['tableName']));
$table_fields = trim($_POST['tableFields']);

if (isset($_POST['keywords']) and !empty($_POST['keywords'])) {
	$keywords = $dbs->escape_string(urldecode(ltrim($_POST['keywords'])));
} else {
	$keywords = '';
}

// explode table fields data
$fields = str_replace(':', ', ', $table_fields);
// set where criteria
$criteria = '';
foreach (explode(':', $table_fields) as $field) {
	$criteria .= " $field LIKE '%$keywords%' OR";
}
// remove the last OR
$criteria = substr_replace($criteria, '', -2);

$sql_string = "SELECT $fields ";

// append table name
$sql_string .= " FROM $table_name ";
if ($criteria) {
	$sql_string .= " WHERE $criteria LIMIT $limit";
}

// send query to database
$query = $dbs->query($sql_string);
$error = $dbs->error;
$data = array();

if (isset($_GET['format'])) {
	if ($_GET['format'] == 'json') {
		if ($error) {
			echo json_encode(array('id' => 0, 'text' => $error));
		}
		if ($query->num_rows > 0) {
			while ($row = $query->fetch_row()) {
				$data[] = array('id' => $row[0], 'text' => $row[1] . (isset($row[2]) ? ' - ' . $row[2] : '') . (isset($row[3]) ? ' - ' . $row[3] : ''));
			}
		} else {
			if (isset($_GET['allowNew'])) {
				$data[] = array('id' => 'NEW:' . $keywords, 'text' => $keywords . ' &lt;' . __('Add New') . '&gt;');
			} else {
				$data[] = array('id' => 'NONE', 'text' => 'NO DATA FOUND');
			}
		}
		echo json_encode($data);
	}
	exit();
} else {
	if ($error) {
		echo '<option value="0">' . $error . '</option>';
	}
	if ($query->num_rows < 1) {
		// output the SQL string
		// echo '<option value="0">'.$sql_string.'</option>';
		echo '<option value="0">NO DATA FOUND</option>' . "\n";
	} else {
		while ($row = $query->fetch_row()) {
			echo '<option value="' . $row[0] . '">' . $row[1] . (isset($row[2]) ? ' - ' . $row[2] : '') . (isset($row[3]) ? ' - ' . $row[3] : '') . '</option>' . "\n";
		}
	}
	exit();
}
