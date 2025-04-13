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
$rs = pg_query($con, "select * from $griddata where owner = '$id' and category='$category'");
$num_rows = pg_num_rows($rs);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php print "<!-- \xfd\xfe(MOJIBAKE TAISAKU)-->\n"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<SCRIPT src="griwiki.js"></SCRIPT>
<SCRIPT LANGUAGE="JScript">
<!--
function transferClipboard() {
	myClip = clipboardData.getData("Text");
    document.f_rep.input_data.value = myClip;
}
//-->
</SCRIPT>
<title>データ一括挿入/置換：<?= $title ?></title>
</head>
<body onLoad="document.f_rep.input_data.focus()">
<p>データをセルに一括挿入あるいは置換します<br>
データは無条件で上書きされますので、注意して下さい。<br>
ページ、横位置、縦位置、データの順で指定します。区切りはタブかカンマ「,」です。<br>
データを空白にすると、データを削除します。<br>
セル内で改行するときは\nを入れてください。<br>
元にはもどせないので、事前に<a href="griddata_out.php?id=<?=$id?>&category=<?=$category?>" class="style1">データ書き出し</a>でデータを保存しておくことをお勧めします</p>

<form METHOD=post action="griddata_rep_reg.php" name="f_rep" target="_top">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="category" value="<?= $category ?>">
<textarea name="input_data" cols="100" rows="30" id="input_data" onkeydown="HandleKeyDown(this);">
</textarea>
<br>
<input type="submit" name="conf_mode" value="submit">
<input type="button" value="クリップボード内容を入力" onclick="transferClipboard()">
</form>
<?php
//}
pg_close($con);
?>
</body>
</html>
