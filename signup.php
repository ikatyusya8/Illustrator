<?php
//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('新規会員登録 signup.php');
debug('================================');
debugLogStart();

//post送信されていた場合
if(!empty($_POST)){
  
  //変数にユーザー情報を代入
  $user_name = $_POST['user_name'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $pass_re = $_POST['pass_re'];
  
  //未入力チェック
  validRequired($user_name, 'user_name');
  validRequired($email, 'email');
  validRequired($password, 'password');
  validRequired($pass_re, 'pass_re');
  
  if(empty($err_msg)){
    
    //名前の最大文字数チェック
    validMaxLenName($user_name, 'user_name', 40);
    
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email', 255);
    //email重複チェック
    validEmailDup($email);
    
    //パスワードの半角英数字チェック
    validHalf($password, 'password');
    //パスワードの最大文字数チェック
    validMaxLen($password, 'password');
    //パスワードの最小文字数チェック
    validMinLen($password, 'password');
    
    //パスワード（再入力）の最大文字数チェック
    validMaxLen($pass_re, 'pass_re');
    //パスワード（再入力）の最小文字数チェック
    validMinLen($pass_re, 'pass_re');
    
    if(empty($err_msg)){
      //パスワードとパスワード（再入力）の同値チェック
      validMatch($password, $pass_re, 'password');
      
      
      if(empty($err_msg)){

        //例外処理
        try {
          //DBへ接続
          $dbh = dbConnect();
          //SQL文作成
          $sql = 'INSERT INTO users (username,email,password,login_time,create_date) VALUES (:username,:email,:password,:login_time,:create_date)';
          $data = array(':username' => $user_name, ':email' => $email, ':password' => password_hash($password, PASSWORD_DEFAULT), ':login_time' => date('Y-m-d H:i:s'), ':create_date' => date('Y-m-d H:i:s'));
          debug('insert-新規会員登録');
          //クエリ実行
          $stmt = queryPost($dbh, $sql, $data);

          //クエリ成功の場合
          if($stmt){
            //ログイン有効期限（１時間）
            $sesLimit = 60 * 60;
            //最終ログイン日時を現在時刻にする
            $_SESSION['login_date'] = time();
            $_SESSION['login_limit'] = $sesLimit;
            //ユーザーIDを格納
            $_SESSION['user_id'] = $dbh->lastInsertId();

            debug('セッション変数の中身：'.print_r($_SESSION,true));

            sendMail($from, $to, $subject, $comment);
            
            header("Location:mypage.php");
            exit();
          }

        } catch (Exception $e) {
          error_log('エラー発生' . $e->getMessage());
          $err_msg['common'] = MSG06;
        }
        
      }
    }
  }
}
    
?>

<?php 
$siteTitle = 'イラストレーター　新規会員登録画面';
require('head.php');
?>

<body>
  <?php
  require('header.php');
  ?>
  <div class="register">
    <div class="register-title">新規会員登録</div>
    <form action="" method="post">
      <div class="area-msg">
        <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
      </div>
      <div class="label-container">
        <label class="user_name">
          <input type="text" name="user_name" placeholder="名前" class="<?php if(!empty($err_msg['user_name'])) echo 'err';?>" value="<?php if(!empty($_POST['user_name'])) echo $_POST['user_name']; ?>">
        </label>
        <div class="area-msg">
          <?php if(!empty($err_msg['user_name'])) echo $err_msg['user_name'] ?>
        </div>
        <label class="email">
          <input type="text" name="email" placeholder="email" class="<?php if(!empty($err_msg['email'])) echo 'err';?>" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
        </label>
        <div class="area-msg">
          <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
        </div>
        <label class="password">
          <input type="password" name="password" placeholder="パスワード" class="<?php if(!empty($err_msg['password'])) echo 'err';?>" value="<?php if(!empty($_POST['password'])) echo $_POST['password']; ?>">
        </label>
        <div class="area-msg">
          <?php if(!empty($err_msg['password'])) echo $err_msg['password']; ?>
        </div>
        <label class="pass_re">
          <input type="password" name="pass_re" placeholder="パスワードの再入力" class="<?php if(!empty($err_msg['pass_re'])) echo 'err';?>" value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re']; ?>">
        </label>
        <div class="area-msg">
          <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
        </div>
        <div><input type="submit" value="登録" class="b-submit"></div>
      </div>
    </form>
  </div>
  <?php
  require('footer.php');
  ?>
</body>
