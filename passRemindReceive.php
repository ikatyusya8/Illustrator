<?php

//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('パスワード再発行認証キー入力 PassRemindReceive.php');
debug('================================');
debugLogStart();

//================================
// 画面処理
//================================
//post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST：'.print_r($_POST,true));
  
  //認証キーを変数に代入
  $auth_key = $_POST['auth_key'];
  
  //未入力チェック
  validRequired($auth_key, 'auth_key');
  
  if(empty($err_msg)){
    debug('未入力チェックOK');
    
    //固定長チェック
    validLength($auth_key, 'auth_key');
    //半角チェック
    validHalf($auth_key, 'auth_key');
    
    if(empty($err_msg)){
      debug('バリデーションOK');
      
      if($auth_key !== $_SESSION['auth_key']){
        $err_msg['common'] = MSG14;
      }
      if($_SESSION['auth_key_limit'] < time()){
        $err_msg['auth_key'] = MSG15;
      }
      if(empty($err_msg)){
        debug('認証OK');
        
        $pass = makeRandkey(); //パスワード作成
        debug($pass);
        
        //例外処理
        try {
          //DB接続
          $dbh = dbConnect();
          $sql = 'UPDATE users SET password = :password WHERE email = :email AND delete_flg = 0';
          $data = array(':password' => password_hash($pass, PASSWORD_DEFAULT) , ':email' => $_SESSION['auth_email']);
          //クエリ実行
          debug('パスワードをアップデート');
          $stmt = queryPost($dbh, $sql, $data);
          
          //クエリ成功のばあい
          if($stmt){
            debug('クエリ成功');
            
            //メールを送信
            $from = 'info@illustrator.com';
            $to = $_SESSION['auth_email'];
            $subject = '【パスワード再発行完了】｜illustrator';
            $comment = <<<EOT
本メールアドレス宛にパスワードの再発行を致しました。
下記のURLにて再発行パスワードをご入力頂き、ログインください。

ログインページ：http://localhost:8888/イラスト投稿サイト/login.php
再発行パスワード：{$pass}
※ログイン後、パスワードのご変更をお願い致します

////////////////////////////////////////
イラストレーターカスタマーセンター
URL  http://illustrator.com/
E-mail info@illustrator.com
////////////////////////////////////////
EOT;
            sendMail($from, $to, $subject, $comment);
            
            //セッション削除
            session_unset();
            $_SESSION['msg_success'] = SUC03;
            debug('セッション変数の中身：'.print_r($_SESSION,true));
            
            header("Location:login.php");
            
          }else{
            debug('クエリに失敗しました');
            $err_msg['common'] = MSG06;
          }
          
        } catch (Exception $e) {
          error_log('エラー発生：' . $e->getMessage());
          $err_msg['common'] = MSG06;
        }
      }
    }
  }
}
?>

<?php 
$siteTitle = 'イラストレーター　認証キー入力';
require('head.php');
?>

<body>
  <?php
  require('header.php');
  ?>

  <div class="reminder">
    <form action="" method="post">
      <div class="label-container">
        <div class="explain">ご指定のメールアドレスにお送りした【パスワード再発行認証】メール内に記載されている「認証キー」をご入力ください。</div>
        <label class="authority">
          <div class="area-msg"><?php echo getErrMsg('common'); ?></div>
          <div class="area-msg"><?php echo getErrMsg('auth_key'); ?></div>
          <input type="text" name="auth_key" placeholder="認証キー">
        </label>
        <div class="submit"><input type="submit" value="送信" class="b-submit"></div>
        <div class="pass-reminder">
        </div>
      </div>
    </form>
  </div>

  <?php
  require('footer.php');
  ?>
