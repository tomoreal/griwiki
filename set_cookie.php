<?php //��ˤ��롢���å�����Ϣư�����뤳��
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

��<?= $reloadtime ?> ����˹����桪(�Ĥ�<?=$repeat_count?>��)������
<a href="<?= $filename_self ?>?<?=$junp_option?>&reloadtime=">�㹹����ߢ�</a>��
<?php
}
else {
?>
<select onchange="LinkJump(this)" name="select_url">
<option value="-">��������桪</option>
<option value="<?= $filename_self ?>?<?=$junp_option?>&reloadtime=10">��10����˹�����</option>
<option value="<?= $filename_self ?>?<?=$junp_option?>&reloadtime=20">��20����˹�����</option>
<option value="<?= $filename_self ?>?<?=$junp_option?>&reloadtime=30">��30����˹�����</option>
</select>
<?php
}
?>


