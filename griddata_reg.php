<?php
//error_reporting(0);
include "griddata_dbcon.php";
	$category = $_POST['category'];
	if (!$category) error("���ƥ��꡼�����ꤵ��Ƥ��ޤ���");
	$page = $_POST['page'];
	if ($page == "") $page=0;
	$tate = $_POST['tate'];
	if ($tate == "") $tate=0;
	$yoko = $_POST['yoko'];
	if ($yoko == "") $yoko=0;
	$id = $_GET['id'];
	if (!$id) $id=$_POST['id'];
	if (!$id) error("ID�����ꤵ��Ƥ��ޤ���");
	/*
	$pwd = $_GET['pwd'];
	if (!$pwd) $pwd=$_POST['pwd'];
	if (!$pwd) error("PWD�����ꤵ��Ƥ��ޤ���");
	*/
	$rep_tate = $_POST['rep_tate'];
	if ($rep_tate == "") error("�Ĥη夬���ꤵ��Ƥ��ޤ���");
	$rep_yoko = $_POST['rep_yoko'];
	if ($rep_yoko == "") error("���η夬���ꤵ��Ƥ��ޤ���");
	$edit_tate = $_POST['edit_tate'];
	$edit_yoko = $_POST['edit_yoko'];
	$time = $_POST['time'];
	$mode = $_POST['mode'];
	$conf_mode = $_POST['conf_mode'];
	$edit_mode = $_POST['edit_mode'];
	$content = rtrim($_POST['content']);
	if ( preg_match("/^[0-9,\-\+%��]+/",trim($content))){
		$content = trim($content);;
	}
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
	if (pg_affected_rows($rs_action) == 0) error("�ǡ����ɲä˼��Ԥ��ޤ�����");
//�Хå����å���
	$rs_action = pg_query($con, "insert into $gridback (owner,category,page,yoko,tate,content,time) values('$id','$category','$page','$rep_yoko','$rep_tate','$content','now')");
}
elseif($conf_mode == "delete" and $time == $time_org and $content_org != ""){
	$rs_action = pg_query($con, "delete from $griddata where owner = '$id' and category=$category and page=$page and yoko = $rep_yoko and tate = $rep_tate");
	if (pg_affected_rows($rs_action) == 0) error("�ǡ�������˼��Ԥ��ޤ���<br>���Ǥ˺������Ƥ�����ǽ��������ޤ���");
}
elseif ($time != $time_org and $content != $content_org) {
	$kakunin = 1;
?>
<html>
<head>
<?php print "<!-- \xfd\xfe(MOJIBAKE TAISAKU)-->\n"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title>ɽ<?= $category ?>-<?= $page ?>:����</title>
</head>
<body>
<p>�������Ʊ������Υǡ������ѹ�����ޤ�����<br>��ǧ��ܥ���򲡤��Ʋ�������</p>
<p>�����Ϥ��褦�Ȥ���ʸ����䡧<b>���<?= $time ?></b></p>
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
<input type="submit" name="conf_mode" value="delete" onclick="return delete_check()">
</form>
<p>���­������Ϥ��줿ʸ����䡧<b>���<?= $time_org ?></b></p>
<form method="post" action="griddata_reg.php" name="f_edit">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="category" value="<?= $category ?>">
<input type="hidden" name="page" value="<?= $page ?>">
<input type="hidden" name="rep_tate" value="<?= $rep_tate ?>">
<input type="hidden" name="rep_yoko" value="<?= $rep_yoko ?>">
<input type="hidden" name="time" value="<?= $time_org ?>">
<textarea name="content"><?= $content_org ?></textarea>
<input type="submit" name="conf_mode" value="update">
<input type="submit" name="conf_mode" value="delete" onclick="return delete_check()">
</form>

<p>�ºݤˤϾ��ͤ��Ƥ��ʤ��ˤ⤫����餺�����β��̤��ѽФ�����ϡ��֥饦���Υ���å��夬�����򤷤Ƥ��ޤ���<br>
IE�ξ�硢�֥ġ���פΡ֥��󥿡��ͥåȥ��ץ����פ��������פ򲡤��ơ֥ڡ�����ɽ��������˳�ǧ����פ˥����å��򤤤��OK�פ򲡤��Ʋ�����</p>
<a href="griddata_list.php">���������</a>
</body>
</html>
<?php		
}
elseif (strcmp($content,$content_org) != 0) {
		$content = pg_escape_string($content);
		$rs_action = pg_query($con, "update $griddata set content = '$content',time = 'now' where owner='$id' and category=$category and page=$page and yoko = $rep_yoko and tate = $rep_tate");
		if (pg_affected_rows($rs_action) == 0) error("�ǡ��������˼��Ԥ��ޤ�����");
//�Хå����å���
		$rs_action = pg_query($con, "insert into $gridback (owner,category,page,yoko,tate,content,time) values('$id','$category','$page','$rep_yoko','$rep_tate','$content','now')");
}

// �ե������ʸ������������
function get_form($str) {
	$str = ereg_replace("<br>", "\n", $str);
	$str = pg_escape_string(htmlspecialchars($str));
	$str = ereg_replace("\n|\r|\r\n", "<br>", $str);
	return $str;
}

// ���顼ɽ�����ƽ�λ
function error($msg) {
	$kakunin = 1;
	print "<p><font color='red'>$msg</font></p>\n";
	exit();
}
?>

<?PHP
if ($kakunin != 1) {
	$mydirname = dirname($_SERVER['PHP_SELF']);
	if ($mydirname != "/") $mydirname = $mydirname . "/";
	if ($edit_mode == "yoko"){
		$p_name = "griddata_yoko.php";
		//���л���
		header("Location: http://".$_SERVER['HTTP_HOST']
			."{$mydirname}"
			."{$p_name}"
			."?id={$id}&category={$category}&yoko={$yoko}&mode=view&edit_yoko={$edit_yoko}&edit_tate={$edit_tate}");
		//���л��Ƚ����
	}
	elseif ($edit_mode == "tate") {
		$p_name = "griddata_tate.php";
		//���л���
		header("Location: http://".$_SERVER['HTTP_HOST']
			."{$mydirname}"
			."{$p_name}"
			."?id={$id}&category={$category}&tate={$tate}&mode=view&edit_yoko={$edit_yoko}&edit_tate={$edit_tate}");
		//���л��Ƚ����
	}
	else {
		$p_name = "griddata_list.php";
		//���л���
		if ($edit_tate >=25) {
			$jump_saki = "#{$edit_yoko}_{$edit_tate}";
		}
		else {
			$jump_saki = "";
		}
		header("Location: http://".$_SERVER['HTTP_HOST']
			."{$mydirname}"
			."{$p_name}"
			."?id={$id}&category={$category}&page={$page}&mode=view&edit_yoko={$edit_yoko}&edit_tate={$edit_tate}{$jump_saki}");
		//���л��Ƚ����
	}
?>
<html>
<head>
<meta http-equiv="refresh" content="0; url=<?=$referer0?>">
<title>ɽ<?= $category ?>-<?= $page ?>:jump</title>
</head>
<body>
<p>�Խ����ޤ�����</p>
<a href="griddata_list.php">���������</a>
</body>
</html>
<?PHP
}
?>
