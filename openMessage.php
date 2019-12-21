<?php

debug('================================');
debug('イラスト詳細ページ メッセージ欄 openmessage.php');
debug('================================');

//================================
// 画面処理
//================================

// DBから自分のユーザー情報を取得
$myUserInfo = getUser($_SESSION['user_id']);

//POSTされているかチェック
if(!empty($_POST)){
  debug('POST送信があります。');
  debug('POST情報：'.print_r($_POST,true));
  
  //変数にメッセージ情報を代入
  $comment = $_POST['open-comment'];
  
  //未入力チェック
  validRequired($comment, 'comment');
  
  //文字数制限チェック
  validMaxLen($comment, 'comment');
  
  if(empty($err_msg)){
    debug('バリデーションチェックOK');
    
    //送信したメッセージをDBに登録
    //例外処理
    try {
      //DB接続
      $dbh = dbConnect();
      //SQL文作成
      $sql = 'INSERT INTO message ( il_id, my_user_id, msg, send_date) VALUES (:il_id, :my_user_id, :msg, :send_date)';
      $data = array( ':il_id' => $i_id, ':my_user_id' => $_SESSION['user_id'], ':msg' => $comment, ':send_date' => date('Y-m-d H:i:s'));
      debug('SQL：'.print_r($sql,true));
      debug('公開コメントを追加');
      $stmt = queryPost($dbh, $sql, $data);
      
    } catch (Exception $e) {
      error_log('エラー発生：' . $e->getMessage());
      
    }
  }
}

//メッセージ欄表示用データ取得
$dbMessageData = getOpenMessage($i_id);

?>
 
 <div class="comment-public">
  <div class="container">
    <?php foreach ( $dbMessageData as $key => $value){
  echo '<div class="contribute"';
  if($value['my_user_id'] != $_SESSION['user_id']) echo 'style = "margin-left: 15px;"';
  echo '>';
  echo '<div class="contribute-icon"><a href=""><img alt="" src="' .$value['pic_icon'] .'"></a></div>';
  echo '<span class="triangle"></span>';
  echo '<div class="contribute-comment'  .'">';
  echo '<div class="comment-username">＜' .$value['username'] .'＞</div>';
  echo $value['msg'] .'</div>';
  echo '</div>';
   }  
    ?>
    
    <div class="submit-comment">
      <div class="contribute-icon"><a href=""><img src="<?php echo $myUserInfo['pic_icon']; ?>"></a></div>
      <form action="" method="post">
        <textarea name="open-comment" cols="50" rows="3"></textarea>
        <input type="submit">
      </form>
    </div>
  </div>
</div>
    <!--
<div class="contribute">
<div class="contribute-icon"><a href=""><img src="img/1438164.jpg"></a></div>
<span class="triangle"></span>
<div class="contribute-comment">コメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメントコメント</div>
</div>
<div class="submit-comment">
<div class="contribute-icon"><a href=""><img src="img/1438164.jpg"></a></div>
<form action="">
<textarea name="" id="" cols="50" rows="3"></textarea>
<input type="submit">
</form>
</div>
-->
