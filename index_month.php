<?PHP 
//���������������ڡ����ֹ�Ȥ��ƥ����������롣
$myid = "mmm"; //Ŭ���ѹ����뤳�ȡ�
$mypage = date("n"); //���ڡ����ֹ�ˤ���
$myyear =date('y');//ǯ��2��Ǽ���
$mydate = date("j"); //����Ĥ��ֹ�ˤ���
$myyoko = 2;
$mycategory = $myyear - 11; //Ŭ���ѹ����뤳�ȡ�2022ǯ���鼫ưŪ�˷���夲��褦�ˤ�����

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
