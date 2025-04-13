<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"
 "http://www.w3.org/TR/REC-html40/loose.dtd">
<HTML>
<HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=EUC-JP">
<BODY>
<?php
//error_reporting(0);
// エラー表示して終了
function error($msg) {
	print "<p><font color='red'>$msg</font></p>\n";
	exit();
}

include "griddata_dbcon.php";
	$category = $_POST['category'];
	if (!$category) error("カテゴリーが指定されていません");
	$id = $_GET['id'];
	if (!$id) $id=$_POST['id'];
	if (!$id) error("IDが指定されていません");
	/*
	$pwd = $_GET['pwd'];
	if (!$pwd) $pwd=$_POST['pwd'];
	if (!$pwd) error("PWDが指定されていません");
	*/
	$input_data = $_POST['input_data'];
	if ($input_data == "") error("データを入力して下さい");

//表定義を読み込む
$rs_category = pg_query($con, "select * from $gridcategory where owner = '$id' and category='$category'");
$num_rows_category = pg_num_rows($rs_category);
if ($num_rows_category != 1) error("表が定義されていません");

/*
//オリジナルデータ
$rs = pg_query($con, "select * from $griddata where owner = '$id' and category='$category'");
$row = pg_fetch_array($rs);

//データの配列への取り込み
while ($row = pg_fetch_array($rs)) {
	$page = $row['page'];
	$yoko = $row['yoko'];
	$tate = $row['tate'];
	$content = $row['content'];
	$data[$yoko][$tate][$page] = $content;
}
*/

//input_dataのフィールド分解
$i = 0;
$tok = strtok($input_data,"\n");
while($tok) {
	$i++;
	$buf[$i] = $tok;
//		print $buf[$i];
	$tok = strtok("\n");
}

foreach( $buf as $key_buf => $value_buf) {
	$flg = 0;
	$temp = preg_split('/[,\t]/',$value_buf,4);
	$yoko_rep = preg_replace("/[^0-9]/","",$temp[1]);
	$tate_rep = preg_replace("/[^0-9]/","",$temp[2]);
	$page_rep = preg_replace("/[^0-9]/","",$temp[0]);
//	$content_rep = pg_escape_string(trim($temp[3]));
//	$content_rep = trim($temp[3]);
//	$content_rep = str_replace("\\n","\n",pg_escape_string(trim($temp[3])));
	$content_rep = str_replace("\\n","\n",trim($temp[3]));
	$content_rep = pg_escape_string($content_rep);

	if (preg_match("/^[0-9]+/",$yoko_rep) == false || preg_match("/^[0-9]+/",$tate_rep) == false || preg_match("/^[0-9]+/",$page_rep) == false) {
		print "エラーowner：{$id} 分類：{$category} 頁：{$page_rep} 横：{$yoko_rep} 縦：{$tate_rep}データ：{$content_rep}<br>";
	}
	else {
		$rs_org = pg_query($con, "select content,yoko,tate,page from $griddata where owner = '$id' and category='$category' and page=$page_rep and yoko = $yoko_rep and tate = $tate_rep");
		$dataexist = pg_num_rows($rs_org);
		if ($dataexist == 1) {
			$flg = 1;
			$data[$yoko_rep][$tate_rep][$page_rep] = pg_fetch_result($rs_org,0,0);
		}
		else {
			$data[$yoko_rep][$tate_rep][$page_rep] = "";
		}
		if ($dataexist > 0 and $content_rep == "") {
			$sql_update = "delete from $griddata where owner = '$id' and category=$category and page=$page_rep and yoko = $yoko_rep and tate = $tate_rep";
			$rs_update = pg_query($con, $sql_update);
			print "削除owner：{$id} 分類：{$category} 頁：{$page_rep} 横：{$yoko_rep} 縦：{$tate_rep}データ：{$content_rep} sql:{$flg}<br>";
		}
		elseif ($dataexist > 0 and strcmp($data[$yoko_rep][$tate_rep][$page_rep], $content_rep) != 0){//横、縦、頁＝データ
			$sql_update = "UPDATE $griddata SET content = '$content_rep', time = 'NOW' where owner = '$id' and category = $category and page = $page_rep and yoko = $yoko_rep and tate = $tate_rep";
			$rs_update = pg_query($con, $sql_update);
			print "置換owner：{$id} 分類：{$category} 頁：{$page_rep} 横：{$yoko_rep} 縦：{$tate_rep}データ：{$data[$yoko_rep][$tate_rep][$page_rep]}-->{$content_rep}<br>";
		//バックアップ用
			$rs_action = pg_query($con, "insert into $gridback (owner, category, yoko, tate, page, content,time) values('$id', $category, $yoko_rep, $tate_rep, $page_rep,'$content_rep', 'NOW')");
		}
		elseif ($dataexist < 1 and $content_rep != "") {
			$sql_update = "insert into $griddata (owner, category, yoko, tate, page, content,time) values('$id', $category, $yoko_rep, $tate_rep, $page_rep,'$content_rep', 'NOW')";
			$rs_update = pg_query($con, $sql_update);
			print "挿入owner：{$id} 分類：{$category} 頁：{$page_rep} 横：{$yoko_rep} 縦：{$tate_rep}データ：{$content_rep} sql:{$flg}<br>";
		//バックアップ用
			$rs_action = pg_query($con, "insert into $gridback (owner, category, yoko, tate, page, content,time) values('$id', $category, $yoko_rep, $tate_rep, $page_rep,'$content_rep', 'NOW')");
		}
		else {
			print "n/a owner：{$id} 分類：{$category} 頁：{$page_rep} 横：{$yoko_rep} 縦：{$tate_rep}データ：{$content_rep} sql:{$flg}<br>";
		}
	}
}


?>
<p>挿入、置換しました。<br>
<a href="griddata_list.php?id=<?=$id?>&category=<?=$category?>&page=<?=$page_rep?>&mode=view">頁表示に戻る</a>　
<a href="griddata_yoko.php?id=<?=$id?>&category=<?=$category?>&page=<?=$yoko_rep?>&mode=view">列表示に戻る</a>　
<a href="griddata_tate.php?id=<?=$id?>&category=<?=$category?>&page=<?=$tate_rep?>&mode=view">行表示に戻る</a>　
</p>
</body>
</html>
