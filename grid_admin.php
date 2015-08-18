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
$category = 1;

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
	$midashi = $row_update['midashi'];
}
else {
	$conf_mode = "insert";
	$sql_max_category = "select max(category) as max_category from $gridcategory";
	$rs_max_category = pg_query($con, $sql_max_category);
	$max_category = pg_fetch_result($rs_max_category, 0, "max_category");
	$category = $max_category +1;
}

//データの最大日付を読み込む
$rs_date = pg_query($con, "select owner,category, date_trunc('second',max(time)) as time_s from $griddata group by owner,category");
while ($row_rs_date = pg_fetch_array($rs_date)) {
	$owner_c = $row_rs_date['owner'];
	$category_c = $row_rs_date['category'];
	$time_s = $row_rs_date['time_s'];
	$max_date[$owner_c][$category_c] = $time_s;
	$max_table = max($max_table,$time_s);
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
<?php print "<!-- \xfd\xfe(MOJIBAKE TAISAKU)-->\n"; ?>
<meta http-equiv="Content-Type" content="text/html; charset=euc-jp">
<META NAME="WWWC" CONTENT="<?=$max_table?>">
<script type="text/javascript">
<!--
function del_con(tgt_no) {
	var conf_str;
	conf_str = "番号" + tgt_no + "を削除しますか";
	del_href = "grid_admin_reg.php?conf_mode=delete&no=" + tgt_no;
	if (confirm(conf_str)) { location.href=del_href }
}
function delete_check(){
	conf_msg = "削除して宜しいですか？"
	return(confirm(conf_msg));
}
// -->
</SCRIPT>
<title>表:管理</title>
</head>

<?php
//print "<body onLoad=\"document.f_edit.owner.focus()\">";
?>
<body>
<p>表定義（<a href="grid_admin.php">挿入モード</a>）<br>
<form method="post" action="grid_admin_reg.php" name="f_edit">
<input type="hidden" name="no" value="<?= $no ?>">
owner:<input name="owner" type="text" value="<?= $owner ?>" size="5">　
category：<input type="text" name="category" value="<?= $category ?>"  size="5"><br>
Title:<input name="title" type="text" value="<?= $title ?>" size="50"><br>
横:<input name="max_yoko" type="text" value="<?= $default_yoko ?>" size="5">×
縦:<input name="max_tate" type="text" value="<?= $default_tate ?>" size="5">
 ページ数(option):<input name="max_page" type="text" value="<?= $default_page ?>" size="5">
passwd:<input name="passwd" type="text" value="<?= $passwd ?>" size="5"><br>
見出し短縮<input name="midashi" type="checkbox" value="short" <?php if ($midashi == "short") print "checked" ;?>><br>
<input type="submit" name="conf_mode" value="<?= $conf_mode ?>">
</form></p>
<hr>
<!--一覧の表示-->
<p>表定義一覧 (<?= date("Y/m/d H:i") ?>)
<TABLE border="1">
<tr bgcolor="#BFC5CA">
<TH>no</TH>
<TH>owner</TH>
<TH>passwd</TH>
<TH>category</TH>
<TH>タイトル</TH>
<TH>横</TH>
<TH>縦</TH>
<TH>ページ</TH>
<TH>見出し短縮</TH>
<TH>削除</TH>
<TH>更新</TH>
<TH>最終更新日</TH>
<TH>出力</TH>
<TH>出力B</TH>
</tr>
<?php
$rs_category = pg_query($con, "select * from $gridcategory order by owner, category desc, no desc");
$num_rows_category = pg_num_rows($rs_category);

while ($row_category = pg_fetch_array($rs_category)) {
	$no = $row_category['no'];
	$owner = $row_category['owner'];
	$category = $row_category['category'];
	$titlte = $row_category['title'];
	$max_yoko = $row_category['max_yoko'];
	$max_tate = $row_category['max_tate'];
	$max_page = $row_category['max_page'];
	$passwd = $row_category['passwd'];
	$midashi = $row_category['midashi'];
	if($max_category < $category) $max_category = $category;
?>
<tr>
<TD bgcolor="#BFC5CA"><div align="center"><a href="griddata_list.php?id=<?=$owner?>&category=<?=$category?>"><?=$no?></a></div></td>
<TD><?=$owner?></td>
<TD><?=$passwd?></td>
<TD align="right"><?=$category?></td>
<TD id="title"><a href="griddata_list.php?id=<?=$owner?>&category=<?=$category?>"><?=$titlte?></a></td>
<TD align="right"><?=$max_yoko?></td>
<TD align="right"><?=$max_tate?></td>
<TD align="right"><?=$max_page?></td>
<TD align="right"><?=$midashi?></td>
<TD><a href="javascript:del_con(<?= $no ?>)">削除</a></td>
<TD><a href="grid_admin.php?no=<?= $no?>&conf_mode=update">更新</a></td>
<TD><?=$max_date[$owner][$category]?></td>
<td><a href="griddata_out.php?id=<?=$owner?>&category=<?=$category?>" class="style1">現在</a></td>
<td><a href="griddata_out.php?id=<?=$owner?>&category=<?=$category?>&b=1" class="style1">backup</a></td>
</tr>
<?php
}
?>
</TABLE></p>
<?php
//}
pg_close($con);
?>
<!--
ソートのブックマークレットは下記から転載させて頂きました。すばらしいスクリプトに感謝、感謝。
http://bookmarklet.daa.jp/
<p><a href="javascript:function toArray (c){var a, k;a=new Array;for (k=0; k<c.length; ++k)a[k]=c[k];return a;}function insAtTop(par,child){if(par.childNodes.length) par.insertBefore(child, par.childNodes[0]);else par.appendChild(child);}function countCols(tab){var nCols, i;nCols=0;for(i=0;i<tab.rows.length;++i)if(tab.rows[i].cells.length>nCols)nCols=tab.rows[i].cells.length;return nCols;}function makeHeaderLink(tableNo, colNo, ord){var link;link=document.createElement('a');link.href='javascript:sortTable('+tableNo+','+colNo+','+ord+');';link.appendChild(document.createTextNode((ord>0)?'昇順':'降順'));return link;}function makeHeader(tableNo,nCols){var header, headerCell, i;header=document.createElement('tr');for(i=0;i<nCols;++i){headerCell=document.createElement('td');headerCell.appendChild(makeHeaderLink(tableNo,i,1));headerCell.appendChild(document.createTextNode('/'));headerCell.appendChild(makeHeaderLink(tableNo,i,-1));header.appendChild(headerCell);}return header;}g_tables=toArray(document.getElementsByTagName('table'));if(!g_tables.length) alert('このページにはテーブルが含まれていません.');(function(){var j, thead;for(j=0;j<g_tables.length;++j){thead=g_tables[j].createTHead();insAtTop(thead, makeHeader(j,countCols(g_tables[j])))}}) ();function compareRows(a,b){if(a.sortKey==b.sortKey)return 0;return (a.sortKey < b.sortKey) ? g_order : -g_order;}function sortTable(tableNo, colNo, ord){var table, rows, nR, bs, i, j, temp;g_order=ord;g_colNo=colNo;table=g_tables[tableNo];rows=new Array();nR=0;bs=table.tBodies;for(i=0; i<bs.length; ++i)for(j=0; j<bs[i].rows.length; ++j){rows[nR]=bs[i].rows[j];temp=rows[nR].cells[g_colNo];if(temp) rows[nR].sortKey=temp.innerHTML;else rows[nR].sortKey='';++nR;}rows.sort(compareRows);for (i=0; i < rows.length; ++i)insAtTop(table.tBodies[0], rows[i]);}">ファイルをソートモードにする</a></p>
-->
<script type="text/javascript">
<!--
javascript:function toArray (c){var a, k;a=new Array;for (k=0; k<c.length; ++k)a[k]=c[k];return a;}function insAtTop(par,child){if(par.childNodes.length) par.insertBefore(child, par.childNodes[0]);else par.appendChild(child);}function countCols(tab){var nCols, i;nCols=0;for(i=0;i<tab.rows.length;++i)if(tab.rows[i].cells.length>nCols)nCols=tab.rows[i].cells.length;return nCols;}function makeHeaderLink(tableNo, colNo, ord){var link;link=document.createElement('a');link.href='javascript:sortTable('+tableNo+','+colNo+','+ord+');';link.appendChild(document.createTextNode((ord>0)?'A':'D'));return link;}function makeHeader(tableNo,nCols){var header, headerCell, i;header=document.createElement('tr');for(i=0;i<nCols;++i){headerCell=document.createElement('td');headerCell.appendChild(makeHeaderLink(tableNo,i,1));headerCell.appendChild(document.createTextNode('/'));headerCell.appendChild(makeHeaderLink(tableNo,i,-1));header.appendChild(headerCell);}return header;}g_tables=toArray(document.getElementsByTagName('table'));if(!g_tables.length) alert('このページにはテーブルが含まれていません.');(function(){var j, thead;for(j=0;j<g_tables.length;++j){thead=g_tables[j].createTHead();insAtTop(thead, makeHeader(j,countCols(g_tables[j])))}}) ();function compareRows(a,b){if(a.sortKey==b.sortKey)return 0;return (a.sortKey < b.sortKey) ? g_order : -g_order;}function sortTable(tableNo, colNo, ord){var table, rows, nR, bs, i, j, temp;g_order=ord;g_colNo=colNo;table=g_tables[tableNo];rows=new Array();nR=0;bs=table.tBodies;for(i=0; i<bs.length; ++i)for(j=0; j<bs[i].rows.length; ++j){rows[nR]=bs[i].rows[j];temp=rows[nR].cells[g_colNo];if(temp) rows[nR].sortKey=temp.innerHTML;else rows[nR].sortKey='';++nR;}rows.sort(compareRows);for (i=0; i < rows.length; ++i)insAtTop(table.tBodies[0], rows[i]);}
-->
</script>
</body>
</html>
