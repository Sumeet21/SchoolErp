<?php

$field = $_GET['select'];

$find = $_GET['enter'];

header("Content-type: application/x-msexcel");
header("Content-Disposition: attachment; filename=std_'$field'_'$find'.xls");
header("Pragma: no-cache");
header("Expires: 0");

mysql_connect('localhost','thevall7_erp',�thevalleyschool123�);
@mysql_select_db(thevall7_erp_std_master) or die("Unable to select database");

$select = "SELECT * FROM std_2013_14 WHERE upper($field) LIKE'%$find%' ORDER BY class, name";

$export = mysql_query($select);

$count = mysql_num_fields($export);
for ($i = 0; $i < $count; $i++) {

$header .= mysql_field_name($export, $i)."\t";
}
while($row = mysql_fetch_row($export)) {

$line = '';
foreach($row as $value) {
if ((!isset($value)) OR ($value == "")) {

$value = "\t";
} else {

$value = str_replace('"', '""', $value);

$value = '"' . $value . '"' . "\t";

}

$line .= $value;
}

$data .= trim($line)."\n";
}

$data = str_replace("\r", "", $data);
if ($data == "") {

$data = "\n(0) Records Found!\n";
}
print "$header\n$data";
?> 
