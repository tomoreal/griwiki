<?php
// ���դ����
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

// ��˽�������Ƥ���
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

// HTTP/1.1
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);

// HTTP/1.0
header("Pragma: no-cache");
?>
<?php
//ɽ��ɽ��
//error_reporting(0);
$category = $_GET['category'];
if (!$category) $page=$_POST['category'];
if (!$category) error("���ƥ��꡼�����ꤵ��Ƥ��ޤ���");
$id = $_GET['id'];
if (!$id) $id=$_POST['id'];
if (!$id) error("ID�����ꤵ��Ƥ��ޤ���");

include "griddata_dbcon.php";
// �ե������ʸ������������
function get_form($str) {
	$str = ereg_replace("<br>", "\n", $str);
	$str = htmlspecialchars($str);
	$str = ereg_replace("\n|\r|\r\n", "<br>", $str);
	return $str;
}

// ���顼ɽ�����ƽ�λ
function error($msg) {
	print "<p><font color='red'>$msg</font></p>\n";
	exit();
}

//ɽ������ɤ߹���
$rs_category = pg_query($con, "select * from $gridcategory where owner = '$id' and category='$category'");
$num_rows_category = pg_num_rows($rs_category);
if ($num_rows_category != 1) error("ɽ���������Ƥ��ޤ���");

//�ޥ��ܤΥǡ�����������ɤ߹���
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
<title>�ǡ����������/�ִ���<?= $title ?></title>
</head>
<body onLoad="document.f_rep.input_data.focus()">
<p>�ǡ����򥻥�˰���������뤤���ִ����ޤ�<br>
�ǡ�����̵���Ǿ�񤭤���ޤ��Τǡ����դ��Ʋ�������<br>
�ڡ����������֡��İ��֡��ǡ����ν�ǻ��ꤷ�ޤ������ڤ�ϥ��֤�����ޡ�,�פǤ���<br>
�ǡ��������ˤ���ȡ��ǡ����������ޤ���<br>
������ǲ��Ԥ���Ȥ���\n������Ƥ���������<br>
���ˤϤ�ɤ��ʤ��Τǡ�������<a href="griddata_out.php?id=<?=$id?>&category=<?=$category?>" class="style1">�ǡ����񤭽Ф�</a>�ǥǡ�������¸���Ƥ������Ȥ򤪴��ᤷ�ޤ�</p>

<form METHOD=post action="griddata_rep_reg.php" name="f_rep" target="_top">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="category" value="<?= $category ?>">
<textarea name="input_data" cols="100" rows="30" id="input_data" onkeydown="HandleKeyDown(this);">
</textarea>
<br>
<input type="submit" name="conf_mode" value="submit">
<input type="button" value="����åץܡ������Ƥ�����" onclick="transferClipboard()">
</form>
<?php
//}
pg_close($con);
?>
</body>
</html>