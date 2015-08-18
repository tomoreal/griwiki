<?php
mb_regex_encoding("EUC-JP");
//�إå��ν���
include "set_option.php";
$timediff_second = 600;
$category = $_GET['category'];
$id = $_GET['id'];
$mode = $_GET['mode'];
$pmode  = $_GET["pmode"];
if ( $pmode == "xls" ) {
	header("Cache-Control: public");
	header("Pragma: public");
	$cd_str = "Content-Disposition: attachment; filename=\"griwiki_" . $id . "_" . $category . "_" . date("Ymd") . ".xls\"";
	header("Content-Type: application/vnd.ms-excel");
	header($cd_str);
	$mode = "plane";
}
else {
	// ���դ����
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	// ��˽�������Ƥ���
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	
	// HTTP/1.0
	header("Pragma: no-cache");
}

?>
<?php
//ɽ��ɽ��
//error_reporting(0);
if (!$category) $category=$_POST['category'];
if (!$category) error("���ƥ��꡼�����ꤵ��Ƥ��ޤ���");
if (!$id) $id=$_POST['id'];
if (!$id) error("ID�����ꤵ��Ƥ��ޤ���");
/*
$pwd = $_GET['pwd'];
if (!$pwd) $pwd=$_POST['pwd'];
if (!$pwd) error("PWD�����ꤵ��Ƥ��ޤ���");
*/
$yoko_in = $_GET['yoko'];
if ($yoko_in == "") $yoko_in=$_POST['yoko'];
if ($yoko_in == "") $yoko_in=1;
$edit_yoko = $_GET['edit_yoko'];
$edit_tate = $_GET['edit_tate'];
/*
�Խ��⡼��Ƚ��
if ($edit_tate > 0 and $edit_yoko > 0) {
	$mode = "kobetsu";
}
else if ($edit_tate >0) {
	$emode = "tate";
}
else if ($edit_yoko >0) {
	$mode = "yoko";
}
*/

include "griddata_dbcon.php";
// �ե������ʸ������������
function get_form($str) {
	$str = ereg_replace("<br>", "\n", $str);
	$str = htmlspecialchars($str);
	$str = ereg_replace("\n|\r|\r\n", "<br>", $str);
	$str = ereg_replace(" ", "&nbsp;", $str);
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
$row_category = pg_fetch_array($rs_category);
$max_yoko = $row_category['max_yoko'];
$max_tate = $row_category['max_tate'];
$title = $row_category['title'];
$owner = $row_category['owner'];
$max_page = $row_category['max_page'];
$midashi = $row_category['midashi'];

//ɽ�������ǥե���Ȥκ���ڡ��������ɤ߹���
$sql_max_page = "select max(page) as max_page from $griddata where owner = '$id' and category='$category'";
$rs_max_page = pg_query($con, $sql_max_page);
$max_page = max($page,$max_page,pg_fetch_result($rs_max_page, 0, "max_page"));

$yoko_mae = $yoko_in -1;
$yoko_saki = $yoko_in +1;

//�ޥ��ܤΥǡ�����������ɤ߹���
$rs = pg_query($con, "select *, date_trunc('second',time) as time_s from $griddata where owner = '$id' and category='$category' and (yoko = $yoko_in or yoko=0)");
$num_rows = pg_num_rows($rs);
while ($row = pg_fetch_array($rs)) {
	$page_data = $row['page'];
	$yoko = $row['yoko'];
	if ($max_yoko < $yoko) $max_yoko = $yoko;
	$tate = $row['tate'];
	if ($max_tate < $tate) $max_tate = $tate;
	$time[$yoko][$tate][$page_data] = $row['time'];
	if ($max_time < $time[$yoko][$tate][$page_data]) $max_time = $time[$yoko][$tate][$page_data];
	$timediff = time() - strtotime($row['time_s']);
	if ($timediff <= $timediff_second) {
		$timediff_color[$yoko][$tate][$page_data] = "orange";
	}
	elseif ($timediff <= 60 * 60) {
		$timediff_color[$yoko][$tate][$page_data] = "gold";
	}
	elseif ($timediff <= 12 * 60 * 60) {
		$timediff_color[$yoko][$tate][$page_data] = "peachpuff";
	}
	elseif ($timediff <= 24 * 60 * 60) {
		$timediff_color[$yoko][$tate][$page_data] = "lavenderblush";
	}
	elseif ($timediff <= 7 * 24 * 60 * 60) {
		$timediff_color[$yoko][$tate][$page_data] = "mintcream";
	}
	$content = $row['content'];
	$data[$yoko][$tate][$page_data] = $content;
	$max_cell_len[$page_data] = max(strlen($content),$max_cell_len[$page_data]);
	$max_cell_hight[$tate] = max(substr_count($content,"\n")+1,ceil(strlen($content) / 60),$max_cell_hight[$tate]);
}

//�ڡ��������ȥ�ꥹ�ȥ��å�
$rs_page_title = pg_query($con, "select * from $griddata where owner = '$id' and category='$category' and tate = 0 and yoko = 0");
while ($row_page_title = pg_fetch_array($rs_page_title)) {
	$page_data = $row_page_title['page'];
	$content = $row_page_title['content'];
	$data[0][0][$page_data] = $content;
	$page_title[$page_data] = $content;
}
//���Υ����ȥ�ξ��
if ($data[0][0][0] != "") $title = $data[0][0][0];

//��ʲ�)�����ȥ�ꥹ�ȥ��å�
$rs_yoko_title = pg_query($con, "select * from $griddata where owner = '$id' and category='$category' and tate = 0 and page = 0");
while ($row_yoko_title = pg_fetch_array($rs_yoko_title)) {
	$yoko_data = $row_yoko_title['yoko'];
	$content = $row_yoko_title['content'];
	$data[$yoko_data][0][0] = $content;
	$yoko_title[$yoko_data] = $content;
}
//�ԡʽġ˥����ȥ�ꥹ�ȥ��å�
$rs_tate_title = pg_query($con, "select * from $griddata where owner = '$id' and category='$category' and page = 0 and yoko = 0");
while ($row_tate_title = pg_fetch_array($rs_tate_title)) {
	$tate_data = $row_tate_title['tate'];
	$content = $row_tate_title['content'];
	$data[0][$tate_data][0] = $content;
	$tate_title[$tate_data] = $content;
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php print "<!-- \xfd\xfe(MOJIBAKE TAISAKU)-->\n"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW,NOARCHIVE">
<SCRIPT src="griwiki.js"></SCRIPT>
<title>��<?= $yoko_in ?> <?= $data[$yoko_in][0][0] ?>-<?= $title ?></title>
<style type="text/css">
<!--
a {text-decoration: none;}
td a {text-decoration: none;display:block;width:100%;height:100%;white-space:nowrap;}
.orange {background-color: orange}
.gold {background-color: gold}
.peachpuff {background-color: peachpuff}
.lavenderblush {background-color: lavenderblush}
.mintcream {background-color: mintcream}
.skyblue {background-color: skyblue}
.small_font {font-size: x-small}
.t_center {text-align: center}
.t_left {text-align: left}
.t_right {text-align: right}
.style1 {font-size: small}
-->
</style>
</head>

<?php
//�ɤ��˥ե��������򤢤Ƥ뤫�����
if ($mode == "edit") print "<body onLoad=\"document.f_edit.content.focus()\">";
elseif ($mode == "view" and $edit_yoko != "" and $edit_tate != "") print "<body onLoad=\"document.getElementById('{$edit_yoko}_{$edit_tate}').focus()\">";
if ($mode == "title_edit") print "<body onLoad=\"document.f_title_edit.content.focus()\">";
//elseif ($mode == "plane") print "<body onLoad=\"changeURL()\">";
else print "<body>";


//�ڡ�����ư��󥯤κ���
if ($yoko_in == 0) print "<p><b>�ڡ����ޥ����������̡�</b></p>\n";
?>

<form name="f_yoko_select" method="post" action="griddata_yoko.php?id=<?= $id ?>&category=<?= $category ?>">
<?php
if ($yoko_mae > 0) {
?>
[<a href="griddata_yoko.php?id=<?= $id ?>&category=<?= $category ?>&yoko=<?= $yoko_mae ?>&mode=<?= $mode ?>">(<?= $yoko_mae ?>)����</a>��
<?php
}
else {
?>
[( )������
<?php
}
?>
	<select name="yoko" id="yoko" onChange="document.f_yoko_select.submit()">
<?php
	for ($p = 1; $p <= $max_yoko; $p++) {
?>
		<option value="<?=$p?>"<?php if ($p == $yoko_in) print " selected" ?>><?=$p?>�� <?=$data[$p][0][0]?></option>
<?php
	}
?>
	</select>
<noscript>
<input type="submit" name="conf_page_select" value="OK">
</noscript>
��<a href="griddata_yoko.php?id=<?= $id ?>&category=<?= $category ?>&yoko=<?= $yoko_saki ?>&mode=<?= $mode ?>">����(<?= $yoko_saki ?>)</a>]
�����������ӥ塼��
<a href='javascript:table_trance()'>�Ԥ��������</a>�����ؤ����֤Ǥ��Խ��Բġ�
</form>
<b><a href="griddata_yoko.php?id=<?=$id?>&category=<?=$category-1?>&yoko=<?=$yoko_in?>&mode=view">��</a><?=$title?>
<a href="griddata_yoko.php?id=<?=$id?>&category=<?=$category+1?>&yoko=<?=$yoko_in?>&mode=view">��</a>��
<?php 
if ($data[$yoko_in][0][0] != "") {
	$sub_title = $data[$yoko_in][0][0];
	$conf_mode = "update";
}
else {
	$sub_title = "����";
	$conf_mode = "insert";
}
if ($mode == "title_edit") {
?>
<form method="post" action="griddata_reg.php" name="f_title_edit">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="category" value="<?= $category ?>">
<input type="hidden" name="yoko" value="<?= $yoko_in ?>">
<input type="hidden" name="rep_tate" value="0">
<input type="hidden" name="rep_yoko" value="<?= $yoko_in ?>">
<input type="hidden" name="edit_tate" value="0">
<input type="hidden" name="edit_yoko" value="0">
<input type="hidden" name="time" value="<?= $time[$yoko_in][0][0] ?>">
<input type="hidden" name="mode" value="title_edit">
<input type="hidden" name="edit_mode" value="yoko">
<input type="hidden" name="conf_mode" value="<?= $conf_mode ?>">
<input name="content" type="text" onBlur="document.f_title_edit.submit()" size="30" value="<?= $data[$yoko_in][0][0] ?>">
<br><input type="submit" name="conf_mode" value="<?= $conf_mode ?>">
<input type="reset" name="conf_mode" value="reset">
<?php
if ($conf_mode == "update") {
?>
<input type="submit" name="conf_mode" value="delete" onclick="return delete_check()">
<?php
}
?>
</form></td>
<?php
}
else if($mode == "plane"){
	print "{$sub_title}";
}
else {
	print "<a id=0_0 href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$yoko_in}&mode=title_edit\">{$sub_title}</a>";
}
?>
 ��</b>(���ߡ�<?= date("Y-m-d H:i") ?>�úǽ�������<?= substr($max_time,0,16) ?>)
<?php
include "set_cookie.php";
?>
<TABLE border="1">
<tr bgcolor="#BFC5CA">
<?php
	$start_cell = ($yoko_in != 0) ? 1 : 0; 
	if ($mode == "plane") {
		print "<TD><a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$yoko_in}&mode=view\"><div align=\"center\">" . "��" . "</div></a></td>\n";
	}
	else {
		print "<TD><a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$yoko_in}&mode=plane\"><div align=\"center\">" . "��" . "</div></a></td>\n";
	}

	$cell_data_csv .= "\"" . $sub_title . "\"\t";
	for( $j=$start_cell; $j<=$max_page+1; $j++) {//��
//		print "<TD><a href=\"griddata_list.php?id={$id}&category={$category}&page={$j}\"><div align=\"center\">{$j}��<br>{$page_title[$j]}</div></a></td>\n";
//		$cell_data_csv .= "\"" . $page_title[$j] . "\"\t";
		if ($midashi == "short" and $page_title[$j] <> "") {
			$yoko_midashi =  $page_title[$j];
		}
		else if ($page_title[$j] <> "") {
			$yoko_midashi =  $j . "��<br>" . $page_title[$j];
		}
		else {
			$yoko_midashi =  $j . "��";
		}
		print "<TD><a href=\"griddata_list.php?id={$id}&category={$category}&page={$j}&h={$h}\"><div align=\"center\">{$yoko_midashi}</div></a></td>\n";
		$cell_data_csv .= "\"" . $yoko_midashi . "\"\t";
	}
	print "</tr>\n";
	$cell_data_csv .= "\n";
for( $i=$start_cell; $i<=$max_tate+1; $i++) {//��
//	print "<TD bgcolor=\"#BFC5CA\"><a href=\"griddata_tate.php?id={$id}&category={$category}&tate={$i}\"><div align=\"left\">{$i}�� {$tate_title[$i]}</div></a></td>\n";
//	$cell_data_csv .= "\"" . $tate_title[$i] . "\"\t";
	($midashi == "short" and $tate_title[$i] <> "") ? $tate_midashi =  $tate_title[$i] : $tate_midashi =  $i . "�� " . $tate_title[$i];
	print "<TD bgcolor=\"#BFC5CA\"><a href=\"griddata_tate.php?id={$id}&category={$category}&tate={$i}&h={$h}\"><div align=\"left\">{$tate_midashi}</div></a></td>\n";
	$cell_data_csv .= "\"" . $tate_midashi . "\"\t";
	for( $j=$start_cell; $j<=$max_page+1; $j++) {//��
		//��������η���
		($i >= 20) ? $jump_name = "#content" : $jump_name = "";
		//�ƥ����ȥ��ꥢ�Υ���������
		$t_rows = min(max($max_cell_hight[$i],3),40); 
		$max_cell_len[$j]> 60 ?	$t_cols = 60 : $t_cols = 30;

		$master = 0;
		if ($data[$yoko_in][$i][$j] == "") {
			$conf_mode = "insert";
		}
		else {
			$conf_mode = "update";
		}

		if ($data[$yoko_in][$i][$j] != "") {
			$cell_data =  $data[$yoko_in][$i][$j];
			$cell_data_out =  $data[$yoko_in][$i][$j];
		}
		elseif ($yoko_in != 0 and ($data[$yoko_in][$i][0] != "" or $data[0][$i][$j] != "" or $data[$yoko_in][0][$j] != "")) { //�ǥޥ�����
			$master = 1;
			$cell_data =  $data[$yoko_in][$i][0] . $data[0][$i][$j] . $data[$yoko_in][0][$j];
			$cell_data_out =  $data[$yoko_in][$i][0] . $data[0][$i][$j] . $data[$yoko_in][0][$j];
		}
		else {
			$cell_data =  $data[$yoko_in][$i][$j];
/*
			$cell_data_out =  "^��";
*/
			if($mode == "edit" and $edit_tate == $i) {
				$cell_data_out =  "^��" . str_repeat("<br>��",$t_rows + 2);
			}
			elseif ($max_cell_hight[$i] <= 1) {
				$cell_data_out =  "^��";
			}
			else {
				$cell_data_out =  "^��" . str_repeat("<br>��",$max_cell_hight[$i] - 1);
			}
		}

		$atama = substr($cell_data_out,0,1);
		if ( $atama == "^") $place = "t_center";
			elseif ( $atama == "\'") $place = "t_left";
			elseif ( $atama == "\"") $place = "t_right";
			elseif ( preg_match('/^[0-9,+\-\$\.%��]*$/',$cell_data_out) ) $place = "t_right";
			elseif ( mb_ereg_match("^[�������ߢ��ݢ���������]$",$cell_data_out) ) $place = "t_center";
			else $place ="t_left";
		$cell_data_out =  preg_replace('/^[\'\"^]/',"",$cell_data_out);
		$cell_data_csv .= "\"" . $cell_data . "\"\t";

		if ($mode == "edit" and $edit_tate == $i and $edit_yoko == $j) {
			if ($master == 1) {
?>
<td bgcolor="skyblue">
<?php
			}
			elseif ($timediff_color[$yoko_in][$i][$j] == 1) {
?>
<td bgcolor="yellow">
<?php
			}
			else{
?>
<td>
<?php
			}
print "<div class=\"{$timediff_color[$yoko_in][$i][$j]}\">"
?>
<form method="post" action="griddata_reg.php" name="f_edit" id="f_edit">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="category" value="<?= $category ?>">
<input type="hidden" name="page" value="<?= $j ?>">
<input type="hidden" name="yoko" value="<?= $yoko_in ?>">
<input type="hidden" name="rep_tate" value="<?= $i ?>">
<input type="hidden" name="rep_yoko" value="<?= $yoko_in ?>">
<input type="hidden" name="edit_tate" value="<?= $edit_tate ?>">
<input type="hidden" name="edit_yoko" value="<?= $edit_yoko ?>">
<input type="hidden" name="time" value="<?= $time[$yoko_in][$i][$j] ?>">
<input type="hidden" name="conf_mode" value="<?= $conf_mode ?>">
<input type="hidden" name="edit_mode" value="yoko">
<textarea name="content" cols="<?=$t_cols?>" rows="<?=$t_rows?>" ondblclick="ClickUrl()" id="content">
<?= htmlspecialchars($cell_data) ?>
</textarea><br>
<input type="submit" name="conf_mode" value="<?= $conf_mode ?>">
<?php
if ($conf_mode == "update") {
?>
<input type="submit" name="conf_mode" value="delete" onclick="return delete_check()">
<?php
}
?>
<button onClick="return add_date()">��</button>
<button onClick="return add_time()">��</button>
<button onClick="return add_kigou('��')">��</button>
<button onClick="return add_kigou('��')">��</button>
<button onClick="return textareazoom()">����</button>
<input type="reset" name="conf_mode" value="reset"><br>
<?php if ($time[$yoko_in][$i][$j]) echo substr($time[$yoko_in][$i][$j],0,16) ,"����"; ?>
</form></div></td>
<?php
		}
		elseif ($mode == "plane") {
			$regURL = "(https?://[-_.!��*'()a-zA-Z0-9;/?:@&=+$,%#]+)";
			$regHTML = eregi_replace($regURL,'<a href="\1" target="_blank">\1</a>',get_form($cell_data_out));
			$regHTML = eregi_replace('</a><BR>','</a>',$regHTML);
			print "<td bgcolor=\"{$timediff_color[$yoko_in][$i][$j]}\">" . $regHTML . "</div></td>\n";
//			print "<td bgcolor=\"{$timediff_color[$yoko_in][$i][$j]}\">" . get_form($cell_data_out) . "</div></td>\n";
		}
		elseif ($timediff_color[$yoko_in][$i][$j] != "") {
			print "<td bgcolor=\"{$timediff_color[$yoko_in][$i][$j]}\"><a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$yoko_in}&mode=edit&edit_yoko={$j}&edit_tate={$i}{$jump_name}\" id=\"{$j}_{$i}\"><div class=\"{$place}\">" . get_form($cell_data_out) . "</div></a></td>\n";
		}
		elseif ($master != 1) {
			print "<td><a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$yoko_in}&mode=edit&edit_yoko={$j}&edit_tate={$i}{$jump_name}\" id=\"{$j}_{$i}\"><div class=\"{$place}\">" . get_form($cell_data_out) . "</div></a></td>\n";
		}
		else {
			print "<td bgcolor=\"skyblue\"><a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$yoko_in}&mode=edit&edit_yoko={$j}&edit_tate={$i}{$jump_name}\" id=\"{$j}_{$i}\"><div class=\"{$place}\">" . get_form($cell_data_out) . "</div></a></td>\n";
		}
	}
	print "</tr>\n";
	$cell_data_csv .= "\n";
}
?>
<tr bgcolor="#BFC5CA">
<?php
	$start_cell = ($yoko_in != 0) ? 1 : 0; 
	if ($mode == "plane") {
		print "<TD><a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$yoko_in}&mode=view\"><div align=\"center\">" . "��" . "</div></a></td>\n";
	}
	else {
		print "<TD><a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$yoko_in}&mode=plane\"><div align=\"center\">" . "��" . "</div></a></td>\n";
	}

	for( $j=$start_cell; $j<=$max_page+1; $j++) {//��
		if ($midashi == "short" and $page_title[$j] <> "") {
			$yoko_midashi =  $page_title[$j];
		}
		else if ($page_title[$j] <> "") {
			$yoko_midashi =  $j . "��<br>" . $page_title[$j];
		}
		else {
			$yoko_midashi =  $j . "��";
		}
		print "<TD><a href=\"griddata_list.php?id={$id}&category={$category}&page={$j}&h={$h}\"><div align=\"center\">{$yoko_midashi}</div></a></td>\n";
	}
	print "</tr>\n";
?>
</TABLE>
<?php
//}
pg_close($con);
?>
<p>�ޥ��ܤ򥯥�å�����ȡ��Խ����Ǥ��ޤ����ֶ���פ򥯥�å�������ɲäǤ��ޤ���<br>
�ǲ�ü����ü�˥ǡ��������Ϥ���ȡ����줾�첼�������ΰ����礷�ޤ���<br>
�ƥ����Ȱ��֤ϡ���Ƭ�ˡ�^'"�פ��ղáʡ�^���������'�׺��󤻡���"�ױ��󤻡�</p>

<?php
if ($yoko_in == 0) {
?>
<p>�����ԣ��ǡ����ΤΥ����ȥ롡���ƣ��ǡ��Ը��Ф������ƣ��ԡ��Ǹ��Ф�</p>
<?php
}
?>

<?php
if ($mode == "edit") {
?>
<p><b>�Խ����褦�Ȥ���ʸ�����ƥ����ȥܥå����ˤʤ����ϥ���å���αƶ��Ǥ��Τǡ�<i>���ξ��֤Τޤ�</i>�����ɹ��ܥ���(F5)�򲡤��Ʋ�������</b><br>
���ξ��ϡ������ȥ�μ��ˤ��븽�ߤλ��郎���ֲ��פ�ؤ��Ƥ���Ϥ��Ǥ���</p>
<?php
}
?>

<table>
<tr>
<td>����������</td>
<td>
<div class="orange" align="center"><?= round($timediff_second/60) ?>ʬ����<br>��<?= date("H:i",time() - round($timediff_second/60)*60)?></div>
</td>
<td>
<div class="gold" align="center">60ʬ����<br>��<?= date("H:i",time() - 60*60)?></div>
</td>
<td>
<div class="peachpuff" align="center">12���ְ���<br>��<?= date("m/d H:i",time() - 12*60*60)?></div>
</td>
<td>
<div class="lavenderblush" align="center">24���ְ���<br>��<?= date("m/d H:i",time() - 24*60*60)?></div>
</td>
<td>
<div class="mintcream" align="center">1���ְ���<br>��<?= date("m/d H:i",time() - 7*24*60*60)?></div>
</td>
<td><div class="skyblue" align="center">�ޥ�����<br>(��񤭲�)</div>
</td></tr>
</table>
<a href="griddata_out.php?id=<?=$id?>&category=<?=$category?>" class="style1">�ǡ����񤭽Ф�</a>��
<a href="griddata_rep.php?id=<?=$id?>&category=<?=$category?>" class="style1">�����Ͽ</a>��
<a href="griddata_list.php?id=<?=$id?>&category=<?=$category?>&page=0" class="style1">�ޥ�����(�Ƕ���)</a>��
<a href="griddata_yoko.php?id=<?=$id?>&category=<?=$category?>&yoko=0" class="style1">�ޥ�����(����)</a>��
<a href="griddata_tate.php?id=<?=$id?>&category=<?=$category?>&tate=0" class="style1">�ޥ�����(�Զ���)</a><br>
&copy; �䡡��(Makoto Tomo),2006-2007.
<?php
if ($mode == "plane") {
?>
<form name="f_data_out" id="f_data_out">
<p><textarea name="sourse_data" cols="10" rows="1" id="sourse_data">
<?= htmlspecialchars($cell_data_csv) ?>
</textarea>
<button onClick="copyText()">ʸ����򥳥ԡ�����</button>(IE����)</p>
</form>
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
}
?>
<a href="griddata_imp.php?id=<?=$id?>&category=<?=$category?>&yoko=<?=$yoko?>">ɽ��������ݡ���</a>��
<?php
	print "<a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$yoko_in}&pmode=xls\">Excel�ǳ���</a>\n";
?>
</body>
</html>