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
$b = $_GET['b'];
if ($b == 1) $griddata=$gridback;
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
$rs = pg_query($con, "select page,yoko,tate,content,time from $griddata where owner = '$id' and category='$category'");
$num_rows = pg_num_rows($rs);

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php print "<!-- \xfd\xfe(MOJIBAKE TAISAKU)-->\n"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title>�ǡ����񤭤�����<?= $title ?></title>
</head>
<body>
<form name="f_data_out" id="f_data_out">
<textarea name="sourse_data" cols="100" rows="30" id="sourse_data">
<?php
	print "ID��{$id}\t���ƥ��꡼��{$category}\n\n";
	for( $j=0; $j<pg_num_fields($rs) -1; $j++) {//��
		print ereg_replace("\"","",pg_field_name($rs,$j))."\t";
	}
		print ereg_replace("\"","",pg_field_name($rs,$j))."\n";
for( $i=0; $i<pg_num_rows($rs); $i++) {//��
	for( $j=0; $j<pg_num_fields($rs) -1; $j++) {//��
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
<p><button onClick="copyText()">ʸ����򥳥ԡ�����</button>(IE����)</p>
<SCRIPT LANGUAGE="JScript">
<!--
	function copyText() {
		var text = document.getElementById("sourse_data").value;
		clipboardData.setData("Text", text);
		alert("�ǡ����򥯥�åץܡ��ɤ˥��ԡ����ޤ�����");
	}
//-->
</SCRIPT>
<?php
//}
pg_close($con);
?>
</body>
</html>
