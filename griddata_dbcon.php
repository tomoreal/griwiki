<?php
//データベースを作成し、そのログイン情報を３つ記入して下さい。
$db_name = "";//データベース名
$db_user = "";//データベースのログインユーザー名
$db_pswd = "";//データベースのログインパスワード

//下記は変更の必要はありません。
$con = pg_connect("dbname={$db_name} user={$db_user} password={$db_pswd}");

//同じデータベースを利用して、複数のgriwikiを設置する場合は、
//下記のデータベース名を異なるものに変更して下さい。
//ひとつしか作成しない場合は変更不要です。
$gridcategory = "gridcategory"; // データ定義用
$griddata = "griddata"; //データ保存用
$gridback = "gridback"; //データバックアップ用
?>