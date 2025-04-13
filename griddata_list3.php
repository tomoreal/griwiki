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
$timediff_second = 600;
$category = $_GET['category'];
if (!$category) $page=$_POST['category'];
if (!$category) error("カテゴリーが指定されていません");
$id = $_GET['id'];
if (!$id) $id=$_POST['id'];
if (!$id) error("IDが指定されていません");
/*
$pwd = $_GET['pwd'];
if (!$pwd) $pwd=$_POST['pwd'];
if (!$pwd) error("PWDが指定されていません");
*/
$page = $_GET['page'];
if ($page == "") $page=$_POST['page'];
if ($page == "") $page=1;
$edit_tate = $_GET['edit_tate'];
$edit_yoko = $_GET['edit_yoko'];
$mode = $_GET['mode'];
/*
編集モード判別
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
$row_category = pg_fetch_array($rs_category);
$max_yoko = $row_category['max_yoko'];
$max_tate = $row_category['max_tate'];
$title = $row_category['title'];
$owner = $row_category['owner'];
$max_page = $row_category['max_page'];

//表定義からデフォルトの最大ページ数を読み込む
$sql_max_page = "select max(page) as max_page from $griddata where owner = '$id' and category='$category'";
$rs_max_page = pg_query($con, $sql_max_page);
$max_page = max($page,$max_page,pg_fetch_result($rs_max_page, 0, "max_page"));

$page_mae = $page -1;
$page_saki = $page +1;

//マス目のデータを配列に読み込む
$rs = pg_query($con, "select *, date_trunc('second',time) as time_s from $griddata where owner = '$id' and category='$category' and (page='$page' or page=0)");
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
}

//ページタイトルリストアップ
$rs_page_title = pg_query($con, "select * from $griddata where owner = '$id' and category='$category' and tate = 0 and yoko = 0");
while ($row_page_title = pg_fetch_array($rs_page_title)) {
	$page_data = $row_page_title['page'];
	$content = $row_page_title['content'];
	$data[0][0][$page_data] = $content;
	$page_title[$page_data] = $content;
}
//全体タイトルの上書き
if ($data[0][0][0] != "") $title = $data[0][0][0];

//列（横)タイトルリストアップ
$rs_yoko_title = pg_query($con, "select * from $griddata where owner = '$id' and category='$category' and tate = 0 and page = 0");
while ($row_yoko_title = pg_fetch_array($rs_yoko_title)) {
	$yoko_data = $row_yoko_title['yoko'];
	$content = $row_yoko_title['content'];
	$data[$yoko_data][0][0] = $content;
	$yoko_title[$yoko_data] = $content;
}
//行（縦）タイトルリストアップ
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
<SCRIPT language="JavaScript">
<!--
function pageBack(){
    history.back();
}
function updateform(upid, oldstr,owner,category,page,tate,yoko,time,conf_mode,title_edit_mode)
{
	nakami = "<form method='post' action='griddata_reg.php' name='f_edit'>";
	nakami += "<input type='hidden' name='id' value='" + owner + "'>";
	nakami += "<input type='hidden' name='category' value='" + category + "'>";
	nakami += "<input type='hidden' name='page' value='" + page + "'>";
	nakami += "<input type='hidden' name='rep_tate' value='" + tate + "'>";
	nakami += "<input type='hidden' name='rep_yoko' value='" + yoko + "'>";
	nakami += "<input type='hidden' name='time' value='" + time + "'>";
	nakami += "<input type='hidden' name='conf_mode' value='" + conf_mode + "'>";
	nakami += "<input type='hidden' name='conf_mode' value='" + conf_mode + "'>";
	nakami += "<input type='hidden' name='mode' value='" + title_edit_mode + "'>";

	nakami2 = "<br><input type='submit' name='conf_mode' value='" + conf_mode + "'>";
	nakami2 += "<input type='reset' name='conf_mode' value='reset'>";
	nakami2 += "</form>";

    document.getElementById(upid).innerHTML = 
	nakami+"<textarea name='content' cols='30' rows='3' onBlur='document.f_edit.submit()'>" + oldstr + "</textarea>"+nakami2;
    //document.f_edit.content.select();
    document.f_edit.content.focus();
}
function delete_check(){
	conf_msg = "削除して宜しいですか？"
	return(confirm(conf_msg));
}
// -->
</SCRIPT>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<title>頁:<?= $page ?> <?= $data[0][0][$page] ?>-<?= $title ?></title>
<style type="text/css">
<!--
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
//どこにフォーカスをあてるかを決める
if ($mode == "edit") print "<body onLoad=\"document.f_edit.content.focus()\">";
elseif ($mode == "view") print "<body onLoad=\"document.getElementById('{$edit_yoko}_{$edit_tate}').focus()\">";
if ($mode == "title_edit") print "<body onLoad=\"document.f_title_edit.content.focus()\">";
else print "<body>";


//ページ移動リンクの作成
if ($page == 0) print "<p><b>ページマスター（頁共通）</b></p>\n";
?>

<?php //		if ($num_rows != 0) { ?>
<form name="f_page_select" method="post" action="griddata_list.php?id=<?= $id ?>&category=<?= $category ?>">
<?php
if ($page_mae > 0) {
?>
[<a href="griddata_list.php?id=<?= $id ?>&category=<?= $category ?>&page=<?= $page_mae ?>">(<?= $page_mae ?>)≪前頁</a>｜
<?php
}
else {
?>
[( )≪前頁｜
<?php
}
?>
	<select name="page" id="page" onChange="document.f_page_select.submit()">
<?php
	for ($p = 1; $p <= $max_page; $p++) {
?>
		<option value="<?=$p?>"<?php if ($p == $page) print " selected" ?>><?=$p?>頁 <?=$data[0][0][$p]?></option>
<?php
	}
?>
	</select>
<noscript>
<input type="submit" name="conf_page_select" value="OK">
</noscript>
｜<a href="griddata_list.php?id=<?= $id ?>&category=<?= $category ?>&page=<?= $page_saki ?>">次頁≫(<?= $page_saki ?>)</a>]
　◆頁ビュー◆
<a href="griddata_yoko.php?id=<?= $id ?>&category=<?= $category ?>">列</a>
<a href="griddata_tate.php?id=<?= $id ?>&category=<?= $category ?>">行</a> 欄のクリックでビューを切替可
</form>
<p><b>【<?= $title ?>】《
<?php 
if ($data[0][0][$page] != "") {
	$sub_title = $data[0][0][$page];
	$conf_mode = "update";
}
else {
	$sub_title = "＿＿";
	$conf_mode = "insert";
}
if ($mode == "title_edit") {
?>
<form method="post" action="griddata_reg.php" name="f_title_edit">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="category" value="<?= $category ?>">
<input type="hidden" name="page" value="<?= $page ?>">
<input type="hidden" name="rep_tate" value="0">
<input type="hidden" name="rep_yoko" value="0">
<input type="hidden" name="time" value="<?= $time[0][0][$page] ?>">
<input type="hidden" name="mode" value="title_edit">
<input type="hidden" name="conf_mode" value="<?= $conf_mode ?>">
<input name="content" type="text" onBlur="document.f_title_edit.submit()" size="30" value="<?= $data[0][0][$page] ?>">
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
else {
?>
<div class="todolist" id="idid"><div class="<?=$timediff_color[0][0][$page]?>">
<span id='ichi[<?=$j?>][<?=$i?>]' style='color:#000;' ondblclick="updateform('ichi[0][0]', '<?=get_form($data[0][0][$page])?>','<?= $id ?>','<?= $category ?>','<?= $page ?>','<?= $i ?>','<?= $j ?>','<?= $time[$j][$i][$page] ?>','<?= $conf_mode ?>')"><?=get_form($data[0][0][$page])?></span>
<noscript>
<?php
	print "<br><a href=\"griddata_list.php?id={$id}&category={$category}&page={$page}&mode=title_edit\">≪編集≫</a>\n";
?>
</noscript>
</div></div>
<?php
}
?>
 》</b>(現在：<?= date("Y-m-d H:i") ?>｜最終更新：<?= substr($max_time,0,16) ?>)</p>
<TABLE border="1">
<tr bgcolor="#BFC5CA">
<?php
	$start_cell = ($page != 0) ? 1 : 0;  
	print "<TD><a href=\"griddata_list.php?id={$id}&category={$category}&page={$page}&mode=view\"><div align=\"center\">" . "■" . "</div></a></td>\n";
	for( $j=$start_cell; $j<=$max_yoko+1; $j++) {//横
		print "<TD><a href=\"griddata_yoko.php?id={$id}&category={$category}&edit_yoko={$j}\"><div align=\"center\">{$j}列<br>{$yoko_title[$j]}</div></a></td>\n";
	}
	print "</tr>\n";
for( $i=$start_cell; $i<=$max_tate+1; $i++) {//縦
	print "<TD bgcolor=\"#BFC5CA\"><a href=\"griddata_tate.php?id={$id}&category={$category}&edit_tate={$i}\"><div align=\"left\">{$i}行 {$tate_title[$i]}</div></a></td>\n";
	for( $j=$start_cell; $j<=$max_yoko+1; $j++) {//横
		$master = 0;
		if ($data[$j][$i][$page] == "") {
			$conf_mode = "insert";
		}
		else {
			$conf_mode = "update";
		}

		if ($data[$j][$i][$page] != "") {
			$cell_data =  $data[$j][$i][$page];
			$cell_data_out =  $data[$j][$i][$page];
		}
		elseif ($page != 0 and ($data[$j][$i][0] != "" or $data[0][$i][$page] != "" or $data[$j][0][$page] != "")) { //頁マスター
			$master = 1;
			$cell_data =  $data[$j][$i][0] . $data[0][$i][$page] . $data[$j][0][$page];
			$cell_data_out =  $data[$j][$i][0] . $data[0][$i][$page] . $data[$j][0][$page];
		}
		else {
			$cell_data =  $data[$j][$i][0];
			$cell_data_out =  "^＿";
		}

		$atama = substr($cell_data_out,0,1);
		if ( $atama == "^") $place = "t_center";
			elseif ( $atama == "\'") $place = "t_left";
			elseif ( $atama == "\"") $place = "t_right";
			elseif ( preg_match('/^[0-9,+\-\$]*$/',$cell_data_out) ) $place = "t_right";
			elseif ( mb_ereg_match("^[○◎●×△−◆■□◇？]$",$cell_data_out) ) $place = "t_center";
			else $place = "t_left";
		$cell_data_out =  preg_replace('/^[\'\"^]/',"",$cell_data_out);

		if ($mode == "edit" and $edit_tate == $i and $edit_yoko == $j) {
			if ($master == 1) {
?>
<td bgcolor="skyblue">
<?php
			}
			elseif ($timediff_color[$j][$i][$page] == 1) {
?>
<td bgcolor="yellow">
<?php
			}
			else{
?>
<td>
<?php
			}
?>
<form method="post" action="griddata_reg.php" name="f_edit">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="category" value="<?= $category ?>">
<input type="hidden" name="page" value="<?= $page ?>">
<input type="hidden" name="rep_tate" value="<?= $i ?>">
<input type="hidden" name="rep_yoko" value="<?= $j ?>">
<input type="hidden" name="time" value="<?= $time[$j][$i][$page] ?>">
<input type="hidden" name="conf_mode" value="<?= $conf_mode ?>">
<textarea name="content" cols="30" rows="3" onBlur="document.f_edit.submit()"><?= $cell_data ?></textarea>
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
		elseif ($timediff_color[$j][$i][$page] != "") {
?>
<td>
<div class="todolist" id="idid"><div class="<?=$timediff_color[$j][$i][$page]?>"><div class="<?=$place?>">
<span id='ichi[<?=$j?>][<?=$i?>]' style='color:#000;' ondblclick="updateform('ichi[<?=$j?>][<?=$i?>]', '<?=get_form($cell_data)?>','<?= $id ?>','<?= $category ?>','<?= $page ?>','<?= $i ?>','<?= $j ?>','<?= $time[$j][$i][$page] ?>','<?= $conf_mode ?>')"><?=get_form($cell_data_out)?></span>
<noscript>
<?php
	print "<br><a href=\"griddata_list.php?id={$id}&category={$category}&page={$page}&mode=edit&edit_yoko={$j}&edit_tate={$i}\" id=\"{$j}_{$i}\">≪編集≫</a>\n";
?>
</noscript>
</div></div></div>
</td>
<?php
		}
		elseif ($master != 1) {
//			print "<td><a href=\"griddata_list.php?id={$id}&category={$category}&page={$page}&mode=edit&edit_yoko={$j}&edit_tate={$i}\" id=\"{$j}_{$i}\"><div class=\"{$place}\">" . get_form($cell_data_out) . "</div></a></td>\n";
?>
<td>
<div class="todolist" id="idid"><div class="<?=$place?>">
<span id='ichi[<?=$j?>][<?=$i?>]' style='color:#000;' ondblclick="updateform('ichi[<?=$j?>][<?=$i?>]', '<?=get_form($cell_data)?>','<?= $id ?>','<?= $category ?>','<?= $page ?>','<?= $i ?>','<?= $j ?>','<?= $time[$j][$i][$page] ?>','<?= $conf_mode ?>')"><?=get_form($cell_data_out)?></span>
<noscript>
<?php
	print "<br><a href=\"griddata_list.php?id={$id}&category={$category}&page={$page}&mode=edit&edit_yoko={$j}&edit_tate={$i}\" id=\"{$j}_{$i}\">≪編集≫</a>\n";
?>
</noscript>
</div></div>
</td>
<?php
		}
		else {
//			print "<td bgcolor=\"skyblue\"><a href=\"griddata_list.php?id={$id}&category={$category}&page={$page}&mode=edit&edit_yoko={$j}&edit_tate={$i}\" id=\"{$j}_{$i}\"><div class=\"{$place}\">" . get_form($cell_data_out) . "</div></a></td>\n";
?>
<td bgcolor="skyblue">
<div class="todolist" id="idid"><div class="<?=$place?>">
<span id='ichi[<?=$j?>][<?=$i?>]' style='color:#000;' ondblclick="updateform('ichi[<?=$j?>][<?=$i?>]', '<?=get_form($cell_data)?>','<?= $id ?>','<?= $category ?>','<?= $page ?>','<?= $i ?>','<?= $j ?>','<?= $time[$j][$i][$page] ?>','<?= $conf_mode ?>')"><?=get_form($cell_data_out)?></span>
<noscript>
<?php
	print "<br><a href=\"griddata_list.php?id={$id}&category={$category}&page={$page}&mode=edit&edit_yoko={$j}&edit_tate={$i}\" id=\"{$j}_{$i}\">≪編集≫</a>\n";
?>
</noscript>
</div></div>
</td>
<?php
		}
	}
	print "</tr>\n";
}
?>
</TABLE>
<?php
//}
pg_close($con);
?>
<p>マス目をクリックすると、編集ができます。＿をクリックすると追加できます。<br>
入力してテキストエリアから他にフォーカスを移すと自動的に更新されます。<br>
最下端、右端にデータを入力すると、それぞれ下、右に領域を拡大します。<br>
テキスト位置は、先頭に「^'"」を付加（「^」中央、「'」左寄せ、「"」右寄せ）</p>

<?php
if ($page == 0) {
?>
<p>◆０行０列：全体のタイトル　◆各０列：行見出し　◆各０行：列見出し</p>
<?php
}
?>

<table>
<tr>
<td>更新色凡例</td>
<td>
<div class="orange" align="center"><?= round($timediff_second/60) ?>分以内<br>〜<?= date("H:i",time() - round($timediff_second/60)*60)?></div>
</td>
<td>
<div class="gold" align="center">60分以内<br>〜<?= date("H:i",time() - 60*60)?></div>
</td>
<td>
<div class="peachpuff" align="center">12時間以内<br>〜<?= date("m/d H:i",time() - 12*60*60)?></div>
</td>
<td>
<div class="lavenderblush" align="center">24時間以内<br>〜<?= date("m/d H:i",time() - 24*60*60)?></div>
</td>
<td>
<div class="mintcream" align="center">1週間以内<br>〜<?= date("m/d H:i",time() - 7*24*60*60)?></div>
</td>
<td><div class="skyblue">マスター(上書き可)</div>
</td></tr>
</table>
<a href="griddata_out.php?id=<?=$id?>&category=<?=$category?>" class="style1">データ書き出し</a>　
<a href="griddata_list.php?id=<?=$id?>&category=<?=$category?>&page=0" class="style1">マスター(頁共通)</a>　
<a href="griddata_yoko.php?id=<?=$id?>&category=<?=$category?>&edit_yoko=0" class="style1">マスター(列共通)</a>　
<a href="griddata_tate.php?id=<?=$id?>&category=<?=$category?>&edit_tate=0" class="style1">マスター(行共通)</a><br>
&copy; 塘　誠(Makoto Tomo),2006.
<!--
<div class="todolist" id="idid">
		<span id='data1' style='color:#000;' ondblclick="updateform('aa', 'data1', '●')">●</span>
</div>
-->
</body>
</html>
