<?php //上にある、クッキーと連動させること
if ($reloadtime > 0) {
	if ($repeat_count > 0) {
?>
	setTimeout("location.reload()",<?= $reloadtime * 1000 ?>);
<?php
	}
}
?>
-->
</script>
<?php
$filename_self = basename($_SERVER['PHP_SELF']);
$junp_option = "id={$id}&category={$category}&page={$page}&yoko={$yoko_in}&tate={$tate_in}&mode=view";
if ($reloadtime > 0) {
?>

　<?= $reloadtime ?> 秒毎に更新中！(残り<?=$repeat_count?>回)　→　
<a href="<?= $filename_self ?>?<?=$junp_option?>&reloadtime=">≪更新停止≫</a>　
<?php
}
else {
?>
<select onchange="LinkJump(this)" name="select_url">
<option value="-">更新停止中！</option>
<option value="<?= $filename_self ?>?<?=$junp_option?>&reloadtime=10">≪10秒毎に更新≫</option>
<option value="<?= $filename_self ?>?<?=$junp_option?>&reloadtime=20">≪20秒毎に更新≫</option>
<option value="<?= $filename_self ?>?<?=$junp_option?>&reloadtime=30">≪30秒毎に更新≫</option>
</select>
<?php
}
?>


