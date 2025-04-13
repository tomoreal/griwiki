<?php
//表の表示
//error_reporting(0);
include "griddata_dbcon.php";
$conf_mode = $_POST['conf_mode'];
if ($conf_mode == "") $conf_mode = $_GET['conf_mode'];
$no = $_POST['no'];
if ($no == "") $no = $_GET['no'];
$default_yoko = 5;
$default_tate = 10;
$default_page = 5;

if ($conf_mode == "update"){
	$rs_update = pg_query($con, "select * from $gridcategory where no = '$no'");
	$num_rows_update = pg_num_rows($rs_update);
	$row_update = pg_fetch_array($rs_update);
	$category = $row_update['category'];
	$max_yoko = $row_update['max_yoko'];
	$max_tate = $row_update['max_tate'];
	$title = $row_update['title'];
	$max_page = $row_update['max_page'];
	$owner = $row_update['owner'];
	$passwd = $row_update['passwd'];
	$default_yoko = $max_yoko;
	$default_tate = $max_tate;
	$default_page = $max_page;
}
else {
	$conf_mode = "insert";
	$sql_max_category = "select max(category) as max_category from $gridcategory";
	$rs_max_category = pg_query($con, $sql_max_category);
	$max_category = pg_fetch_result($rs_max_category, 0, "max_category");
	$category = $max_category +1;
}

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

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title>表:管理</title>
</head>

<?php
print "<body onLoad=\"document.f_edit.title.focus()\">";
?>

<p>表定義</p>
<form method="post" action="grid_admin_reg.php" name="f_edit">
<input type="hidden" name="no" value="<?= $no ?>">
category：<input type="text" name="category" value="<?= $category ?>"><br>
横:<input name="max_yoko" type="text" value="<?= $default_yoko ?>" size="5">×
縦:<input name="max_tate" type="text" value="<?= $default_tate ?>" size="5"><br>
Title:<input name="title" type="text" value="<?= $title ?>" size="50"><br>
ページ数上限(option):<input name="max_page" type="text" value="<?= $default_page ?>" size="5"><br>
owner:<input name="owner" type="text" value="<?= $owner ?>" size="5">　
passwd:<input name="passwd" type="text" value="<?= $passwd ?>" size="5"><br>
<input type="submit" name="conf_mode" value="<?= $conf_mode ?>">
</form>
<hr>
<p><a href="grid_admin.php">挿入モード</a></p>
<!--一覧の表示-->
<p>表定義一覧 (<?= date("Y/m/d H:i") ?>)</p>
<TABLE border="1">
<tr bgcolor="#BFC5CA">
<TD>no</td>
<TD>category</td>
<TD>横</td>
<TD>縦</td>
<TD>タイトル</td>
<TD>ページ</td>
<TD>owner</td>
<TD>passwd</td>
<TD>削除</td>
<TD>更新</td>
<TD>表を開く</td>
</tr>
<?php
$rs_category = pg_query($con, "select * from $gridcategory order by no desc");
$num_rows_category = pg_num_rows($rs_category);

while ($row_category = pg_fetch_array($rs_category)) {
	$no = $row_category['no'];
	$category = $row_category['category'];
	$max_yoko = $row_category['max_yoko'];
	$max_tate = $row_category['max_tate'];
	$titlte = $row_category['title'];
	$max_page = $row_category['max_page'];
	$owner = $row_category['owner'];
	$passwd = $row_category['passwd'];
	if($max_category < $category) $max_category = $category;
?>
<tr>
<TD bgcolor="#BFC5CA"><div align="center"><?=$no?></div></td>
<TD><?=$category?></td>
<TD><?=$max_yoko?></td>
<TD><?=$max_tate?></td>
<TD id="title"><?=$titlte?></td>
<TD><?=$max_page?></td>
<TD><?=$owner?></td>
<TD><?=$passwd?></td>
<TD><a href="grid_admin_reg.php?no=<?= $no?>&conf_mode=delete">削除</a></td>
<TD><a href="grid_admin.php?no=<?= $no?>&conf_mode=update">更新</a></td>
<TD><a href="griddata_list2.php?id=<?=$owner?>&category=<?=$category?>">開く</a></td>
</tr>
<?php
}
?>
</TABLE>
<?php
//}
pg_close($con);
?>

</body>
</html>
