<?php
//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('ログアウト logout.php');
debug('================================');
debugLogStart();

debug('ログアウトします');
//セッション削除
session_destroy();
debug('ログインページへ遷移します。');
//ログインページへ
header("Location:login.php");
exit();
