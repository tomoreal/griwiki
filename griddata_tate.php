<?php
mb_regex_encoding("EUC-JP");
//ヘッダの出力
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
	// 日付が過去
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	
	// 常に修正されている
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	
	// HTTP/1.1
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	
	// HTTP/1.0
	header("Pragma: no-cache");
}

?>
<?php
//表の表示
//error_reporting(0);
if (!$category) $category=$_POST['category'];
if (!$category) error("カテゴリーが指定されていません");
if (!$id) $id=$_POST['id'];
if (!$id) error("IDが指定されていません");
/*
$pwd = $_GET['pwd'];
if (!$pwd) $pwd=$_POST['pwd'];
if (!$pwd) error("PWDが指定されていません");
*/
$tate_in = $_GET['tate'];
if ($tate_in == "") $tate_in=$_POST['tate'];
if ($tate_in == "") $tate_in=1;
$edit_tate = $_GET['edit_tate'];
$edit_yoko = $_GET['edit_yoko'];
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
	$str = ereg_replace(" ", "&nbsp;", $str);
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
$midashi = $row_category['midashi'];

//表定義からデフォルトの最大ページ数を読み込む
$sql_max_page = "select max(page) as max_page from $griddata where owner = '$id' and category='$category'";
$rs_max_page = pg_query($con, $sql_max_page);
$max_page = max($page,$max_page,pg_fetch_result($rs_max_page, 0, "max_page"));

$tate_mae = $tate_in -1;
$tate_saki = $tate_in +1;

//マス目のデータを配列に読み込む
$rs = pg_query($con, "select *, date_trunc('second',time) as time_s from $griddata where owner = '$id' and category='$category' and (tate = '$tate_in' or tate=0)");
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
	$max_cell_len[$yoko] = max(strlen($content),$max_cell_len[$yoko]);
	$max_cell_hight[$page_data] = max(substr_count($content,"\n")+1,ceil(strlen($content) / 60),$max_cell_hight[$page_data]);
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
<?php print "<!-- \xfd\xfe(MOJIBAKE TAISAKU)-->\n"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<META NAME="ROBOTS" CONTENT="NOINDEX,NOFOLLOW,NOARCHIVE">
<SCRIPT src="griwiki.js"></SCRIPT>
<title>行：<?= $tate_in ?> <?= $data[0][$tate_in][0] ?>-<?= $title ?></title>
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
//どこにフォーカスをあてるかを決める
if ($mode == "edit") print "<body onLoad=\"document.f_edit.content.focus()\">";
elseif ($mode == "view" and $edit_yoko != "" and $edit_tate != "") print "<body onLoad=\"document.getElementById('{$edit_yoko}_{$edit_tate}').focus()\">";
if ($mode == "title_edit") print "<body onLoad=\"document.f_title_edit.content.focus()\">";
//elseif ($mode == "plane") print "<body onLoad=\"changeURL()\">";
else print "<body>";


//ページ移動リンクの作成
if ($tate_in == 0) print "<p><b>ページマスター（行共通）</b></p>\n";
?>

<form name="f_tate_select" method="post" action="griddata_tate.php?id=<?= $id ?>&category=<?= $category ?>">
<?php
if ($tate_mae > 0) {
?>
[<a href="griddata_tate.php?id=<?= $id ?>&category=<?= $category ?>&tate=<?= $tate_mae ?>&mode=<?= $mode ?>">(<?= $tate_mae ?>)≪前</a>｜
<?php
}
else {
?>
[( )≪前｜
<?php
}
?>
	<select name="tate" id="tate" onChange="document.f_tate_select.submit()">
<?php
	for ($p = 1; $p <= $max_tate; $p++) {
?>
		<option value="<?=$p?>"<?php if ($p == $tate_in) print " selected" ?>><?=$p?>行 <?=$data[0][$p][0]?></option>
<?php
	}
?>
	</select>
<noscript>
<input type="submit" name="conf_page_select" value="OK">
</noscript>
｜<a href="griddata_tate.php?id=<?= $id ?>&category=<?= $category ?>&tate=<?= $tate_saki ?>&mode=<?= $mode ?>">次≫(<?= $tate_saki ?>)</a>]
　‖縦方向ビュー‖
<a href='javascript:table_trance()'>行と列を入替</a>（入替た状態では編集不可）
</form>
<b><a href="griddata_tate.php?id=<?=$id?>&category=<?=$category-1?>&tate=<?=$tate_in?>&mode=view">【</a><?=$title?>
<a href="griddata_tate.php?id=<?=$id?>&category=<?=$category+1?>&tate=<?=$tate_in?>&mode=view">】</a>《
<?php 
if ($data[0][$tate_in][0] != "") {
	$sub_title = $data[0][$tate_in][0];
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
<input type="hidden" name="tate" value="<?= $tate_in ?>">
<input type="hidden" name="rep_tate" value="<?= $tate_in ?>">
<input type="hidden" name="rep_yoko" value="0">
<input type="hidden" name="edit_tate" value="0">
<input type="hidden" name="edit_yoko" value="0">
<input type="hidden" name="time" value="<?= $time[0][$tate_in][0] ?>">
<input type="hidden" name="mode" value="title_edit">
<input type="hidden" name="edit_mode" value="tate">
<input type="hidden" name="conf_mode" value="<?= $conf_mode ?>">
<input name="content" type="text" onBlur="document.f_title_edit.submit()" size="30" value="<?= $data[0][$tate_in][0] ?>">
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
	print "<a id=0_0 href=\"griddata_tate.php?id={$id}&category={$category}&tate={$tate_in}&mode=title_edit\">{$sub_title}</a>";
}
?>
 》</b>(現在：<?= date("Y-m-d H:i") ?>｜最終更新：<?= substr($max_time,0,16) ?>)
<?php
include "set_cookie.php";
?>
<TABLE border="1" id="table1">
<tr bgcolor="#BFC5CA">
<?php
	$start_cell = ($tate_in != 0) ? 1 : 0;
	if ($mode == "plane") {
		print "<TD><a href=\"griddata_tate.php?id={$id}&category={$category}&tate={$tate_in}&mode=view\"><div align=\"center\">" . "編" . "</div></a></td>\n";
	}
	else {
		print "<TD><a href=\"griddata_tate.php?id={$id}&category={$category}&tate={$tate_in}&mode=plane\"><div align=\"center\">" . "閲" . "</div></a></td>\n";
	}
	
	$cell_data_csv .= "\"" . $sub_title . "\"\t";
	for( $j=$start_cell; $j<=$max_yoko+1; $j++) {//横
//		print "<TD><a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$j}\"><div align=\"center\">{$j}列<br>{$yoko_title[$j]}</div></a></td>\n";
//		$cell_data_csv .= "\"" . $yoko_title[$j] . "\"\t";
		if ($midashi == "short" and $yoko_title[$j] <> "") {
			$yoko_midashi =  $yoko_title[$j];
		}
		else if ($yoko_title[$j] <> "") {
			$yoko_midashi =  $j . "列<br>" . $yoko_title[$j];
		}
		else {
			$yoko_midashi =  $j . "列";
		}
		print "<TD><a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$j}&h={$h}\"><div align=\"center\">{$yoko_midashi}</div></a></td>\n";
		$cell_data_csv .= "\"" . $yoko_midashi . "\"\t";
	}
	print "</tr>\n";
	$cell_data_csv .= "\n";
for( $i=$start_cell; $i<=$max_page+1; $i++) {//縦
//	print "<TD bgcolor=\"#BFC5CA\"><a href=\"griddata_list.php?id={$id}&category={$category}&page={$i}\"><div align=\"left\">{$i}頁 {$page_title[$i]}</div></a></td>\n";
//	$cell_data_csv .= "\"" . $page_title[$i] . "\"\t";
	($midashi == "short" and $page_title[$i] <> "") ? $tate_midashi =  $page_title[$i] : $tate_midashi =  $i . "頁 " . $page_title[$i];
	print "<TD bgcolor=\"#BFC5CA\"><a href=\"griddata_list.php?id={$id}&category={$category}&page={$i}&h={$h}\"><div align=\"left\">{$tate_midashi}</div></a></td>\n";
	$cell_data_csv .= "\"" . $tate_midashi . "\"\t";
	for( $j=$start_cell; $j<=$max_yoko+1; $j++) {//横
		//ジャンプ先の決定
		($i >= 20) ? $jump_name = "#content" : $jump_name = "";
		//テキストエリアのサイズ決定
		$t_rows = min(max($max_cell_hight[$i],3),40); 
		$max_cell_len[$j]> 60 ?	$t_cols = 60 : $t_cols = 30;

		$master = 0;
		if ($data[$j][$tate_in][$i] == "") {
			$conf_mode = "insert";
		}
		else {
			$conf_mode = "update";
		}

		if ($data[$j][$tate_in][$i] != "") {
			$cell_data =  $data[$j][$tate_in][$i];
			$cell_data_out =  $data[$j][$tate_in][$i];
		}
		elseif ($tate_in != 0 and ($data[$j][$tate_in][0] != "" or $data[0][$tate_in][$i] != "" or $data[$j][0][$i] != "")) { //頁マスター
			$master = 1;
			$cell_data = $data[$j][$tate_in][0] . $data[0][$tate_in][$i]. $data[$j][0][$i];
			$cell_data_out = $data[$j][$tate_in][0] . $data[0][$tate_in][$i]. $data[$j][0][$i];
		}
		else {
			$cell_data =  $data[$j][$tate_in][$i];
/*
			$cell_data_out =  "^　";
*/
			if($mode == "edit" and $edit_tate == $i) {
				$cell_data_out =  "^　" . str_repeat("<br>　",$t_rows + 2);
			}
			elseif ($max_cell_hight[$i] <= 1) {
				$cell_data_out =  "^　";
			}
			else {
				$cell_data_out =  "^　" . str_repeat("<br>　",$max_cell_hight[$i] - 1);
			}
		}

		$atama = substr($cell_data_out,0,1);
		if ( $atama == "^") $place = "t_center";
			elseif ( $atama == "\'") $place = "t_left";
			elseif ( $atama == "\"") $place = "t_right";
			elseif ( preg_match('/^[0-9,+\-\$\.%％]*$/',$cell_data_out) ) $place = "t_right";
			elseif ( mb_ereg_match("^[○◎●×△−◆■□◇？]$",$cell_data_out) ) $place = "t_center";
			else $place ="t_left";
		$cell_data_out =  preg_replace('/^[\'\"^]/',"",$cell_data_out);
		$cell_data_csv .= "\"" . $cell_data . "\"\t";

		if ($mode == "edit" and $edit_yoko == $j and $edit_tate == $i) {
			if ($master == 1) {
?>
<td bgcolor="skyblue">
<?php
			}
			elseif ($timediff_color[$j][$tate_in][$i] == 1) {
?>
<td bgcolor="yellow">
<?php
			}
			else{
?>
<td>
<?php
			}
print "<div class=\"{$timediff_color[$j][$tate_in][$i]}\">"
?>
<form method="post" action="griddata_reg.php" name="f_edit" id="f_edit">
<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="category" value="<?= $category ?>">
<input type="hidden" name="page" value="<?= $i ?>">
<input type="hidden" name="tate" value="<?= $tate_in ?>">
<input type="hidden" name="rep_tate" value="<?= $tate_in ?>">
<input type="hidden" name="rep_yoko" value="<?= $j ?>">
<input type="hidden" name="edit_tate" value="<?= $edit_tate ?>">
<input type="hidden" name="edit_yoko" value="<?= $edit_yoko ?>">
<input type="hidden" name="edit_tate" value="<?= $edit_tate ?>">
<input type="hidden" name="edit_yoko" value="<?= $edit_yoko ?>">
<input type="hidden" name="time" value="<?= $time[$j][$tate_in][$i] ?>">
<input type="hidden" name="conf_mode" value="<?= $conf_mode ?>">
<input type="hidden" name="edit_mode" value="tate">
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
<button onClick="return add_date()">日</button>
<button onClick="return add_time()">時</button>
<button onClick="return add_kigou('○')">○</button>
<button onClick="return add_kigou('×')">×</button>
<button onClick="return textareazoom()">拡大</button>
<input type="reset" name="conf_mode" value="reset"><br>
<?php if ($time[$j][$tate_in][$i]) echo substr($time[$j][$tate_in][$i],0,16), "更新"; ?>
</form></div></td>
<?php
		}
		elseif ($mode == "plane") {
			$regURL = "(https?://[-_.!〜*'()a-zA-Z0-9;/?:@&=+$,%#]+)";
			$regHTML = eregi_replace($regURL,'<a href="\1" target="_blank">\1</a>',get_form($cell_data_out));
			$regHTML = eregi_replace('</a><BR>','</a>',$regHTML);
			print "<td bgcolor=\"{$timediff_color[$j][$tate_in][$i]}\">" . $regHTML . "</div></td>\n";
//			print "<td bgcolor=\"{$timediff_color[$j][$tate_in][$i]}\">" . get_form($cell_data_out) . "</div></td>\n";
		}
		elseif ($timediff_color[$j][$tate_in][$i] != "") {
			print "<td bgcolor=\"{$timediff_color[$j][$tate_in][$i]}\"><a href=\"griddata_tate.php?id={$id}&category={$category}&tate={$tate_in}&mode=edit&edit_yoko={$j}&edit_tate={$i}{$jump_name}\" id=\"{$j}_{$i}\"><div class=\"{$place}\">" . get_form($cell_data_out) . "</div></a></td>\n";
		}
		elseif ($master != 1) {
			print "<td><a href=\"griddata_tate.php?id={$id}&category={$category}&tate={$tate_in}&mode=edit&edit_yoko={$j}&edit_tate={$i}{$jump_name}\" id=\"{$j}_{$i}\"><div class=\"{$place}\">" . get_form($cell_data_out) . "</div></a></td>\n";
		}
		else {
			print "<td bgcolor=\"skyblue\"><a href=\"griddata_tate.php?id={$id}&category={$category}&tate={$tate_in}&mode=edit&edit_yoko={$j}&edit_tate={$i}{$jump_name}\" id=\"{$j}_{$i}\"><div class=\"{$place}\">" . get_form($cell_data_out) . "</div></a></td>\n";
		}
	}
	print "</tr>\n";
	$cell_data_csv .= "\n";
}
?>
<tr bgcolor="#BFC5CA">
<?php
	$start_cell = ($tate_in != 0) ? 1 : 0;
	if ($mode == "plane") {
		print "<TD><a href=\"griddata_tate.php?id={$id}&category={$category}&tate={$tate_in}&mode=view\"><div align=\"center\">" . "編" . "</div></a></td>\n";
	}
	else {
		print "<TD><a href=\"griddata_tate.php?id={$id}&category={$category}&tate={$tate_in}&mode=plane\"><div align=\"center\">" . "閲" . "</div></a></td>\n";
	}
	
	for( $j=$start_cell; $j<=$max_yoko+1; $j++) {//横
		if ($midashi == "short" and $yoko_title[$j] <> "") {
			$yoko_midashi =  $yoko_title[$j];
		}
		else if ($yoko_title[$j] <> "") {
			$yoko_midashi =  $j . "列<br>" . $yoko_title[$j];
		}
		else {
			$yoko_midashi =  $j . "列";
		}
		print "<TD><a href=\"griddata_yoko.php?id={$id}&category={$category}&yoko={$j}&h={$h}\"><div align=\"center\">{$yoko_midashi}</div></a></td>\n";
	}
	print "</tr>\n";
?>
</TABLE>
<?php
//}
pg_close($con);
?>
<p>マス目をクリックすると、編集ができます。「空欄」をクリックすると追加できます。<br>
最下端、右端にデータを入力すると、それぞれ下、右に領域を拡大します。<br>
テキスト位置は、先頭に「^'"」を付加（「^」中央、「'」左寄せ、「"」右寄せ）</p>

<?php
if ($tate_in == 0) {
?>
<p>◆０頁０列：全体のタイトル　◆各０列：頁見出し　◆各０頁：列見出し</p>
<?php
}
?>

<?php
if ($mode == "edit") {
?>
<p><b>編集しようとした文字がテキストボックスにない時はキャッシュの影響ですので、<i>その状態のまま</i>、再読込ボタン(F5)を押して下さい。</b><br>
その場合は、タイトルの次にある現在の時刻が、「過去」を指しているはずです。</p>
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
<td><div class="skyblue" align="center">マスター<br>(上書き可)</div>
</td></tr>
</table>
<a href="griddata_out.php?id=<?=$id?>&category=<?=$category?>" class="style1">データ書き出し</a>　
<a href="griddata_rep.php?id=<?=$id?>&category=<?=$category?>" class="style1">一括登録</a>　
<a href="griddata_list.php?id=<?=$id?>&category=<?=$category?>&page=0" class="style1">マスター(頁共通)</a>　
<a href="griddata_yoko.php?id=<?=$id?>&category=<?=$category?>&yoko=0" class="style1">マスター(列共通)</a>　
<a href="griddata_tate.php?id=<?=$id?>&category=<?=$category?>&tate=0" class="style1">マスター(行共通)</a><br>
&copy; 塘　誠(Makoto Tomo),2006.
<?php
if ($mode == "plane") {
?>
<form name="f_data_out" id="f_data_out">
<p><textarea name="sourse_data" cols="10" rows="1" id="sourse_data">
<?= htmlspecialchars($cell_data_csv) ?>
</textarea>
<button onClick="copyText()">文字列をコピーする</button>(IE専用)</p>
</form>
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
}
?>
<a href="griddata_imp.php?id=<?=$id?>&category=<?=$category?>&tate=<?=$tate?>">表形式インポート</a>　
<?php
	print "<a href=\"griddata_tate.php?id={$id}&category={$category}&tate={$tate_in}&pmode=xls\">Excelで開く</a>\n";
?>
</body>
</html>
