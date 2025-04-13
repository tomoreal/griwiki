<?PHP 
//アクセスした月をページ番号としてアクセスする。
$mycategory = 2; //適宜変更すること。
$myid = "mmm"; //適宜変更すること。
$mypage = date("n"); //月をページ番号にする
$mydate = date("j"); //日を縦の番号にする
$myyoko = 2;
if ($mydate >=20) {
	$jump_saki = "#{$myyoko}_{$mydate}";
}
else {
	$jump_saki = "";
}
$jump_url = "http://".$_SERVER['HTTP_HOST']
                      .dirname($_SERVER['PHP_SELF'])
                      ."/griddata_list.php?id={$myid}&category={$mycategory}&page={$mypage}&mode=view&edit_yoko={$myyoko}&edit_tate={$mydate}{$jump_saki}";
header("Location: ".$jump_url);
?>
