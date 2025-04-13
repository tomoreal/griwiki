<?php
// 日付が過去
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// 常に修正されている
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");
?>
<?php
//表の表示
//error_reporting(0);
$category = $_GET['category'];
if (!$category) $page=$_POST['category'];
if (!$category) error("カテゴリーが指定されていません");
$id = $_GET['id'];
if (!$id) $id=$_POST['id'];
if (!$id) error("IDが指定されていません");

include "griddata_dbcon.php";
$b = $_GET['b'];
if ($b == 1) $griddata=$gridback;
// フォームの文字列を取得する
function get_form($str) {
	$str = ereg_replace("<br>", "\n", $str);
	$str = htmlspecialchars($str);
	$str = ereg_replace("\n|\r|\r\n", "<br>", $str);
	return $str;
}

// エラー表示して終了
function error($msg) {
	print "<p><font color='red'>$msg</font></p>\n";
	exit();
}

//表定義を読み込む
$rs_category = pg_query($con, "select * from $gridcategory where owner = '$id' and category='$category'");
$num_rows_category = pg_num_rows($rs_category);
if ($num_rows_category != 1) error("表が定義されていません");

//マス目のデータを配列に読み込む
$rs = pg_query($con, "select page,yoko,tate,content,time from $griddata where owner = '$id' and category='$category'");
$num_rows = pg_num_rows($rs);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php print "<!-- \xfd\xfe(MOJIBAKE TAISAKU)-->\n"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title>データ書きだし：<?= $title ?></title>
</head>
<body>
<form name="f_data_out" id="f_data_out">
<textarea name="sourse_data" cols="100" rows="30" id="sourse_data">
<?php
	print "ID：{$id}\tカテゴリー：{$category}\n\n";
	for( $j=0; $j<pg_num_fields($rs) -1; $j++) {//横
		print ereg_replace("\"","",pg_field_name($rs,$j))."\t";
	}
		print ereg_replace("\"","",pg_field_name($rs,$j))."\n";
for( $i=0; $i<pg_num_rows($rs); $i++) {//縦
	for( $j=0; $j<pg_num_fields($rs) -1; $j++) {//横
		$string = pg_fetch_result($rs,$i,$j);
		$string = str_replace("\"","",$string);
		$string = str_replace("\n","\\n",$string);
		$string = str_replace("\n", "", $string);
		$string = str_replace("\r\n", "", $string);
		$string = str_replace("\r", "", $string);
		$string .= "\t";
		print $string;
	}
		$string = pg_fetch_result($rs,$i,$j);
		$string = str_replace("\"","",$string);
		$string = str_replace("\n","\\n",$string);
		$string = str_replace("\n", "", $string);
		$string = str_replace("\r\n", "", $string);
		$string = str_replace("\r", "", $string);
		$string .= "\n";
		print $string;
}
?>
</textarea>
</form>
<p><button onClick="copyText()">文字列をコピーする</button>(IE専用)</p>
<SCRIPT LANGUAGE="JScript">
<!--
	function copyText() {
		var text = document.getElementById("sourse_data").value;
		clipboardData.setData("Text", text);
		alert("データをクリップボードにコピーしました。");
	}
//-->
</SCRIPT>
<?php
//}
pg_close($con);
?>
</body>
</html>
