<?php
//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('ログイン login.php');
debug('================================');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// ログイン画面処理
//================================
//post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります。');
  
  //変数にユーザー情報を代入
  $email = $_POST['email'];
  $password = $_POST['password'];
  $pass_save = (!empty($_POST['pass_save'])) ? true : false;
  
  //emailの形式チェック
  validEmail($email,'email');
  //emailの最大文字数チェック
  validMaxLen($email, 'email');
  
  //パスワードの半角英数字チェック
  validHalf($password, 'password');
  //パスワードの最大文字数チェック
  validMaxLen($password, 'password');
  //パスワードの最小文字数チェック
  validMinLen($password, 'password');
  
  //未入力チェック
  validRequired($email,'email');
  validRequired($password,'password');

  
  //入力したemailがDBのemailと一致する場合、IDとパスワードを取得する
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT password,id FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    //クエリ実行
    debug('入力したemailとDBのemailが一致するデータの検索');
    $stmt = queryPost($dbh, $sql, $data);
    //クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    debug('クエリ結果の中身：'.print_r($result,true));
    
    //パスワード照合
    if(!empty($result) && password_verify($password, array_shift($result))){
      debug('パスワードがマッチしました。');
      
      //ログイン有効期限（１時間）
      $sesLimit = 60 * 60;
      //最終ログイン日時を現在日時にする
      $_SESSION['login_date'] = time();
      
      //ログイン保持にチェックがある場合
      if($pass_save){
        debug('ログイン保持にチェックがあります。');
        //ログイン有効期限を30日にする
        $_SESSION['login_limit'] = $sesLimit * 24 * 30;
      }else{
        debug('ログイン保持にチェックはありません。');
        //ログイン有効期限を１時間にする
        $_SESSION['login_limit'] = $sesLimit;
      }
      // ユーザーIDを格納
      $_SESSION['user_id'] = $result['id'];
      
      debug('セッション変数の中身：'.print_r($_SESSION,true));
      debug('マイページへ遷移します。');
      header("Location:mypage.php");; //マイページへ
      exit();
    }else{
      debug('パスワードがアンマッチです。');
      $err_msg['common'] = MSG10;
    }
    
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG06;
  }
}
?>

<?php 
$siteTitle = 'イラストレーター　ログイン画面';
require('head.php');
?>

<body>
  <?php
  require('header.php');
  ?>

  <div class="login">
    <div class="login-title">ログイン</div>
    <form action="" method="post">
      <div class="label-container">
        <div class="area-msg"><?php echo getErrMsg('common'); ?></div>
        <label class="email">
          <input type="text" name="email" placeholder="email" class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>" >
        </label>
        <div class="area-msg"><?php echo getErrMsg('email'); ?></div>
        <label class="password">
          <input type="password" name="password" placeholder="パスワード"  class="<?php if(!empty($err_msg['email'])) echo 'err';?>" value="<?php if(!empty($_POST['password'])) echo $_POST['password']; ?>">
        </label>
        <div class="area-msg"><?php echo getErrMsg('password'); ?></div>
        <label>
          <input type="checkbox" name="pass_save">
          次回からログインを省略する
        </label>
        <div class="submit"><input type="submit" value="ログイン"></div>
        <div class="pass-reminder">
          <a href="passRemindSend.php">パスワードを忘れた</a>
        </div>
      </div>
    </form>
  </div>



  <?php
  require('footer.php');
  ?>
