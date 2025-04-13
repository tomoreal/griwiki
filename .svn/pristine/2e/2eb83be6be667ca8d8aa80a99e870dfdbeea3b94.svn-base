<?php
//error_reporting(0);
include "griddata_dbcon.php";
	$category = $_POST['category'];
	if (!$category) error("カテゴリーが指定されていません");
	$page = $_POST['page'];
	if ($page == "") error("ページが指定されていません");
	$id = $_GET['id'];
	if (!$id) $id=$_POST['id'];
	if (!$id) error("IDが指定されていません");
	/*
	$pwd = $_GET['pwd'];
	if (!$pwd) $pwd=$_POST['pwd'];
	if (!$pwd) error("PWDが指定されていません");
	*/
	$rep_tate = $_POST['rep_tate'];
	if ($rep_tate == "") error("縦の桁が指定されていません");
	$rep_yoko = $_POST['rep_yoko'];
	if ($rep_yoko == "") error("横の桁が指定されていません");
	$time = $_POST['time'];
	$mode = $_POST['mode'];
	$conf_mode = $_POST['conf_mode'];
	$edit_mode = $_POST['edit_mode'];
	$content = $_POST['content'];
if ($content == "") $conf_mode = "delete";
if ($conf_mode == "delete") $content = "";

$rs = pg_query($con, "select * from $griddata where owner = '$id' and category='$category' and page='$page' and yoko = $rep_yoko and tate = $rep_tate");
$num_rows = pg_num_rows($rs);
$row = pg_fetch_array($rs);
$content_org = $row['content'];
$time_org = $row['time'];


if($conf_mode == "insert" and $num_rows == 0) {
	$content = pg_escape_string($content);
	$rs_action = pg_query($con, "insert into $griddata (owner,category,page,yoko,tate,content,time) values('$id','$category','$page','$rep_yoko','$rep_tate','$content','now')");
	if (pg_affected_rows($rs_action) == 0) error("データ追加に失敗しました。");
}
elseif($conf_mode == "delete" and $time == $time_org and $content_org != ""){
	$rs_action = pg_query($con, "delete from $griddata where owner = '$id' and category=$category and page=$page and yoko = $rep_yoko and tate = $rep_tate");
	if (pg_affected_rows($rs_action) == 0) error("データ削除に失敗しました<br>すでに削除されていた可能性があります。");
}
elseif ($time != $time_org and $content != $content_org) {
	$kakunin = 1;
?>
<html>
<head>
<title>表<?= $category ?>-<?= $page ?>:衝突</title>
</head>
<body>
<p>入力中に同じセルのデータが変更されました。<br>確認後ボタンを押して下さい。</p>
<p>≪入力しようとした文字列≫：<b>時刻→<?= $time ?></b></p>
<form method="post" action="griddata_reg.php" name="f_edit">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="category" value="<?= $category ?>">
<input type="hidden" name="page" value="<?= $page ?>">
<input type="hidden" name="rep_tate" value="<?= $rep_tate ?>">
<input type="hidden" name="rep_yoko" value="<?= $rep_yoko ?>">
<input type="hidden" name="time_rep" value="<?= $time ?>">
<input type="hidden" name="time" value="<?= $time_org ?>">
<textarea name="content"><?= $content ?></textarea>
<input type="submit" name="conf_mode" value="update">
<input type="submit" name="conf_mode" value="delete">
</form>
<p>≪一足先に入力された文字列≫：<b>時刻→<?= $time_org ?></b></p>
<form method="post" action="griddata_reg.php" name="f_edit">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="category" value="<?= $category ?>">
<input type="hidden" name="page" value="<?= $page ?>">
<input type="hidden" name="rep_tate" value="<?= $rep_tate ?>">
<input type="hidden" name="rep_yoko" value="<?= $rep_yoko ?>">
<input type="hidden" name="time" value="<?= $time_org ?>">
<textarea name="content"><?= $content_org ?></textarea>
<input type="submit" name="conf_mode" value="update">
<input type="submit" name="conf_mode" value="delete">
</form>
<p>実際には衝突していないにもかかわらず、この画面が頻出する場合は、ブラウザのキャッシュが悪さをしています。<br>
IEの場合、「ツール」の「インターネットオプション」から「設定」を押して「ページを表示する毎に確認する」にチェックをいれ「OK」を押して下さい</p>

<a href="griddata_list.php">一覧に戻る</a>
</body>
</html>
<?php		
}
elseif ($content != $content_org) {
		$content = pg_escape_string($content);
		$rs_action = pg_query($con, "update $griddata set content = '$content',time = 'now' where owner='$id' and category=$category and page=$page and yoko = $rep_yoko and tate = $rep_tate");
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
	if ($edit_mode == "yoko") $p_name = "griddata_yoko2.php";
	elseif ($edit_mode == "tate") $p_name = "griddata_tate2.php";
	else $p_name = "griddata_list2.php";
	//相対参照
	header("Location: http://".$_SERVER['HTTP_HOST']
		.dirname($_SERVER['PHP_SELF'])
		."/{$p_name}"
		."?id={$id}&category={$category}&page={$page}&mode=view&edit_yoko={$rep_yoko}&edit_tate={$rep_tate}");
	//相対参照終わり
?>
<html>
<head>
<meta http-equiv="refresh" content="0; url=<?=$referer0?>">
<title>表<?= $category ?>-<?= $page ?>:jump</title>
</head>
<body>
<p>編集しました。</p>
<a href="griddata_list.php">一覧に戻る</a>
</body>
</html>
<?PHP
}
?>
