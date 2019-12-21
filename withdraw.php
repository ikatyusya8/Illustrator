<?php
//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('退会画面 withdraw.php');
debug('================================');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
// post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :user_id';
    $sql2 = 'UPDATE illust SET delete_flg = 1 WHERE user_id = :user_id';
    $data = array(':user_id' => $_SESSION['user_id']);
    //クエリ実行
    debug('usersテーブルの削除フラグアップデート');
    $stmt1 = queryPost($dbh, $sql1, $data);
    debug('pictureテーブルの削除フラグアップデート');
    $stmt2 = queryPost($dbh, $sql2, $data);
    
    //クエリ実行成功の場合
    if($stmt1 && $stmt2){
      //セッション削除
      session_destroy();
      debug('セッション変数の中身：'.print_r($_SESSION,true));
      debug('トップページへ遷移します。');
      header("Location:index.php");
      exit();
    }else{
      debug('クエリが失敗しました。');
      $err_msg['common'] = MSG06;
    }
    
  } catch (Exception $e) {
    error_msg('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG06;
  }
}
debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>

<?php
$siteTitle = 'イラストレーター 退会画面';
require('head.php');
?>

<body>
<?php
require('header.php');
?>

  <div class="sign-out">
    <div class="sign-out-title">退会しますか？</div>
    <form action="" method="post">
    <div class="area-msg center">
      <?php if(!empty($err_msg['common'])) echo $err_msg['common'];?>
    </div>
      <div class="container">
        <input type="submit" value="退会する" class="b-submit" name="submit">
      </div>
    </form>
  </div>

<?php
require('footer.php');
?>
