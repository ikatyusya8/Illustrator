<?php
//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('パスワード再発行メール送信 PassRemindSend.php');
debug('================================');
debugLogStart();

//================================
// 画面処理
//================================
//post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報' .print_r($_POST,true));
  
  //変数にPOST情報代入
  $email = $_POST['email'];
  
  //未入力チェック
  validRequired($email, 'email');
  
  if(empty($err_msg)){
    debug('未入力チェックOK');
    
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    
    if(empty($err_msg)){
      debug('バリデーションOK');
      
      //例外処理
      try {
        //DBへ接続
        $dbh = dbConnect();
        //SQL文作成
        $sql = 'SELECT count(*) FROM users WHERE email = :email and delete_flg = 0';
        $data = array(':email' => $email);
        //クエリ実行
        debug('送信したEmailがDBに登録されているか検索');
        $stmt = queryPost($dbh, $sql, $data);
        //クエリ結果の値を取得
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        //EmailがDBに登録されている場合
        if($stmt && array_shift($result)){
          $_SESSION['msg_success'] = SUC03;
          
          $auth_key = makeRandKey(); //認証キー生成
          
          //メールを送信
          $from = 'info@illustrator.com';
          $to = $email;
          $subject = '【パスワード再発行認証】｜illustrator';
          $comment = <<<EOT
本メールアドレス宛にパスワード再発行のご依頼がありました。
下記のURLにて認証キーをご入力頂くとパスワードが再発行されます。

パスワード再発行認証キー入力ページ：http://localhost:8888/イラスト投稿サイト/passRemindSend.php
認証キー：{$auth_key}
※認証キーの有効期限は30分となります

認証キーを再発行されたい場合は下記ページより再度再発行をお願い致します。
http://localhost:8888/webservice_practice07/passRemindSend.php

////////////////////////////////////////
イラストレーターカスタマーセンター
URL  http://illustrator.com/
E-mail info@illustrator.com
////////////////////////////////////////
EOT;
          sendMail($from, $to, $subject, $comment);
          
          //認証に必要な情報をセッションに保存
          $_SESSION['auth_key'] = $auth_key;
          $_SESSION['auth_email'] = $email;
          $_SESSION['auth_key_limit'] = time() + (60*30); //認証キー制限は30分
          debug('セッション変数の中身：' .print_r($_SESSION,true));
          
          header("Location:passRemindReceive.php");
        }else{
          debug('クエリに失敗したかDBに登録のないEmailが入力されました');
          $err_msg['common'] = MSG06;
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
$siteTitle = 'イラストレーター　パスワードを忘れた';
require('head.php');
?>

<body>
  <?php
  require('header.php');
  ?>

  <div class="reminder">
    <form action="" method="post">
      <div class="label-container">
        <div class="explain">ご指定のメールアドレス宛にパスワード再発行用のURLと認証キーをお送り致します。</div>
        <label class="email">
          <div class="area-msg"><?php echo getErrMsg('email'); ?></div>
          <input type="text" name="email" placeholder="email"  class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
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
