<?php
//error_reporting(0);
include "griddata_dbcon.php";
$mode = $_POST['mode'];
$conf_mode = $_POST['conf_mode'];
if ($conf_mode == "") $conf_mode = $_GET['conf_mode'];
if ($conf_mode == "delete") {
	$no = $_POST['no'];
	if ($no == "") $no = $_GET['no'];
	if (!$no) error("No.が指定されていません");
	$rs_action = pg_query($con, "delete from $gridcategory where no=$no");
	if (pg_affected_rows($rs_action) == 0) error("データ削除に失敗しました<br>すでに削除されていた可能性があります。");
}
else {
	$category = $_POST['category'];
	if ($category == "") $category = $_GET['category'];
	if (!$category) error("カテゴリーが指定されていません");
	$max_tate = $_POST['max_tate'];
	if (!$max_tate) error("縦が指定されていません");
	$max_yoko = $_POST['max_yoko'];
	if (!$max_yoko) error("横が指定されていません");
	$max_page = $_POST['max_page'];
	$title = $_POST['title'];
	$owner = $_POST['owner'];
	$passwd = $_POST['passwd'];
	$time = $_POST['time'];
	$midashi = $_POST['midashi'];
}
$rs = pg_query($con, "select * from $gridcategory where no='$no'");
$num_rows = pg_num_rows($rs);

if($conf_mode == "insert" and $num_rows == 0) {
	$title = pg_escape_string($title);
	$owner = pg_escape_string($owner);
	$passwd = pg_escape_string($passwd);
	$rs_action = pg_query($con, "insert into $gridcategory (category,max_yoko,max_tate,max_page,title,owner,passwd,time,midashi) values('$category','$max_yoko','$max_tate','$max_page','$title','$owner','$passwd','now','$midashi')");
	if (pg_affected_rows($rs_action) == 0) error("データ追加に失敗しました。");
}
elseif ($conf_mode == "update") {
	$no = $_POST['no'];
	if ($no == "") $no = $_GET['no'];
	if (!$no) error("No.が指定されていません");
	$rs_action = pg_query($con, "update $gridcategory set category = '$category', max_yoko = '$max_yoko',max_tate = '$max_tate',max_page = '$max_page',title = '$title',owner = '$owner',passwd = '$passwd',time = 'now', midashi = '$midashi' where no=$no");
	if (pg_affected_rows($rs_action) == 0) error("データ更新に失敗しました。");
}

// フォームの文字列を取得する
function get_form($str) {
	$str = ereg_replace("<br>", "\n", $str);
	$str = pg_escape_string(htmlspecialchars($str));
	$str = ereg_replace("\n|\r|\r\n", "<br>", $str);
	return $str;
}

// エラー表示して終了
function error($msg) {
	$kakunin = 1;
	print "<p><font color='red'>$msg</font></p>\n";
	exit();
}
?>

<?PHP
if ($kakunin != 1) {
	//相対参照
	header("Location: http://".$_SERVER['HTTP_HOST']
                      .dirname($_SERVER['PHP_SELF'])
                      ."/grid_admin.php");
	//相対参照終わり
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php print "<!-- \xfd\xfe(MOJIBAKE TAISAKU)-->\n"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title>表定義確認</title>
</head>
<body>
<p>定義を編集しました。</p>
<a href="grid_admin.php">表定義に戻る</a>
</body>
</html>
<?PHP
}
?>
