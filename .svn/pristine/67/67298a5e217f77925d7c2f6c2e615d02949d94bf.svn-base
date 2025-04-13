<?php
ini_set("display_errors","Off");
//繰り返し回数のクッキーへのセット（下記のjavascriptと一緒に修正すること）
$reloadtime  = $_GET["reloadtime"];
if ($reloadtime == 0) {
	$repeat_count = 0;
	setcookie("griwiki_count","",Time() - 900);
}
else {
	$repeat_count = $_COOKIE["griwiki_count"];
	if ($repeat_count == "") {
		setcookie("griwiki_count","99",Time() + 900);
		$repeat_count = 100;
	}
	elseif ($repeat_count == 0) {
		setcookie("griwiki_count","",Time() - 900);
	}
	else {
		setcookie("griwiki_count",$repeat_count - 1,Time() + 900);
	}
}
?>