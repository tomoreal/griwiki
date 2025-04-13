<?php
//error_reporting(0);
include "griddata_dbcon.php";

/*
$sql0_1 = "select count(relname) from pg_class where relname = " . $gridcategory;
$sql0_2 = "select count(relname) from pg_class where relname = " . $griddata;
$sql0_3 = "select count(relname) from pg_class where relname = " . $gridback;
*/

$sql1 = "CREATE TABLE {$gridcategory} (
	\"no\" serial NOT NULL,
    category integer NOT NULL,
    max_yoko integer NOT NULL,
    max_tate integer NOT NULL,
    max_page integer,
    title text,
    \"owner\" text,
    \"time\" timestamp without time zone,
    passwd text,
    midashi text);
";

$sql2 = "CREATE TABLE {$griddata} (
    \"no\" serial NOT NULL,
    category integer NOT NULL,
    page integer NOT NULL,
    yoko integer NOT NULL,
    tate integer NOT NULL,
    \"owner\" text,
    content text,
    \"time\" timestamp without time zone);
";

$sql3 = "CREATE TABLE {$gridback} (
    \"no\" serial NOT NULL,
    category integer NOT NULL,
    page integer NOT NULL,
    yoko integer NOT NULL,
    tate integer NOT NULL,
    \"owner\" text,
    content text,
    \"time\" timestamp without time zone);
";

/*
$rs0_1 = pg_query($con, $sql0_1);
$rs0_2 = pg_query($con, $sql0_2);
$rs0_3 = pg_query($con, $sql0_3);

if (pg_fetch_result($rs0_1,1) > 0) {
	$err_msg = $gridcategory . "を作成できません。<br>";
	$errcode = 1;
}
if (pg_fetch_result($rs0_2,1) > 0) {
	$err_msg .= $griddata  . "を作成できません。<br>";
	$errcode = 1;
}
if (pg_fetch_result($rs0_3,1) > 0) {
	$err_msg .= $gridback  . "を作成できません。<br>";
	$errcode = 1;
}

if ($errcode == 1 ) {
	print "<html><body><p>" . $err_msg . "</html></body></p>" ;
	exit;
}
else {
*/
	$rs1 = pg_query($con, $sql1);
	$rs2 = pg_query($con, $sql2);
	$rs3 = pg_query($con, $sql3);

if ($rs1 == false) {
	$err_msg = $gridcategory . "を作成できません。<br>";
	$errcode = 1;
}
if ($rs2 == false) {
	$err_msg .= $griddata  . "を作成できません。<br>";
	$errcode = 1;
}
if ($rs3 == false) {
	$err_msg .= $gridback  . "を作成できません。<br>";
	$errcode = 1;
}

if ($errcode == 1 ) {
	print "<html><body><p>" . $err_msg . "</html></body></p>" ;
	exit;
}
else {
?>
<html>
<head>
<title>インストール</title>
</head>
<body>
<p>データベースを正常に作成しました。
<br>
<a href="grid_admin.php">表定義</a></p>
</body>
</html>
<?PHP
}
?>
