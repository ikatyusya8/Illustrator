<?php
//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('パスワード変更 passEdit.php');
debug('================================');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
//DBからユーザーデータを取得
$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($userData,true));

//post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  
  //変数にユーザー情報を代入
  $pass_old = $_POST['pass_old'];
  $pass_new = $_POST['pass_new'];
  $pass_new_re = $_POST['pass_new_re'];
  
  //未入力チェック
  validRequired($pass_old, 'pass_old');
  validRequired($pass_new, 'pass_new');
  validRequired($pass_new_re, 'pass_new_re');
  
  if(empty($err_msg)){
    debug('未入力チェックOK');
    
    //古いパスワードのチェック
    validPass($pass_old, 'pass_old');
    //新しいパスワードのチェック
    validPass($pass_new, 'pass_new');
    
    //古いパスワードとDBパスワードを照合
    if(!password_verify($pass_old, $userData['password'])){
      $err_msg['pass_old'] = MSG11;
    }
    
    //新しいパスワードと古いパスワードが同じかチェック
    if($pass_old === $pass_new){
      $err_msg['pass_new'] = MSG12;
    }
    
    //パスワードとパスワードの再入力が同じかチェック
    validMatch($pass_new, $pass_new_re, 'pass_new_re');
    
    if(empty($err_msg)){
      debug('パリデーションOK');
      
      //例外処理
      try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'UPDATE users SET password = :pass where id = :id';
        $data = array( ':id' => $_SESSION['user_id'], ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));
        //クエリ実行
        debug('パスワード更新処理');
        $stmt = queryPost($dbh, $sql, $data);
        
        //クエリ成功の場合
        if($stmt){
          $_SESSION['msg_success'] = SUC02;
          
          //メールを送信
          $username = ($userData['username']) ? $userData['username'] : '名無し';
          $from = 'info@webukatu.com';
          $to = $userData['email'];
          $subject = 'パスワード変更通知　イラストレーター';
          //空白も文字として扱う
          $comment = <<<EOT
{$username} さん
パスワードが変更されました。

//////////////////////////////////////////
イラストレーター　カスタマーセンター
URL http://illustrator.com/
E-email info@illustrator.com
//////////////////////////////////////////
EOT;
          sendMail($from, $to, $subject, $comment);
          
          header("Location:mypage.php");
        }
        
      } catch (Exception $e) {
        error_log('エラー発生：' . $e->getMessage());
        $err_msg['common'] = MSG06;
      }
    }
  }
}

?>

<?php 
$siteTitle = 'イラストレーター　パスワード変更画面';
require('head.php');
?>

<body>
  <?php
  require('header.php');
  ?>
  <div class="pass-edit">
    <div class="edit-title">パスワードの変更</div>
    <form action="" method="post" class="form">
     <div class="area-msg">
       <?php echo getErrMsg('common'); ?>
     </div>
      <div class="container">
        <label class="old_password">
          <div class="area-msg">
            <?php echo getErrMsg('pass_old'); ?>
          </div>
          <input type="password" name="pass_old" class="<?php if(!empty($err_msg['pass_old'])) echo 'err'; ?>" placeholder="パスワード（現在）">
        </label>
        <label class="new_password">
          <div class="area-msg">
            <?php echo getErrMsg('pass_new'); ?>
          </div>
          <input type="password" name="pass_new" class="<?php if(!empty($err_msg['pass_new'])) echo 'err'; ?>" placeholder="新しいパスワード">
        </label>
        <label class="new_pass_re">
          <div class="area-msg">
            <?php echo getErrMsg('pass_new_re'); ?>
          </div>
          <input type="password" name="pass_new_re" class="<?php if(!empty($err_msg['pass_new_re'])) echo 'err'; ?>" placeholder="新しいパスワードの再入力">
        </label>
        <div><input type="submit" value="登録" class="b-submit"></div>
      </div>
    </form>
  </div>
  <?php
  require('footer.php');
  ?>
</body>
