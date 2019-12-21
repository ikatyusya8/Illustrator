<?php
//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('プロフィール編集 profEdit.php');
debug('================================');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
//DBからユーザーデータを取得
$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData,true));

//post送信されていた場合
if(!empty($_POST)){
  debug('POST送信があります');
  debug('POST情報：' . print_r($_POST,true));
  debug('FILE情報：' . print_r($_FILES,true));
  
  //変数にユーザー情報を代入
  $username = $_POST['username'];
  $sex = $_POST['sex'];
  $address = $_POST['address'];
  $birthday = (!empty($_POST['birthday']) && !empty($dbFormData['birthday'])) ? $dbFormData['birthday'] : '2000-1-1';
  $work = $_POST['work'];
  $email = $_POST['email'];
  $comment = $_POST['comment'];
  $pic_icon = ( !empty($_FILES['pic_icon']['name']) ) ? uploadImg($_FILES['pic_icon'],'pic_icon') : '';
  $pic_icon = (empty($pic_icon) && !empty($dbFormData['pic_icon'])) ? $dbFormData['pic_icon'] : $pic_icon;
  $pic_header = ( !empty($_FILES['pic_header']['name']) ) ? uploadImg($_FILES['pic_header'],'pic_header') : '';
  $pic_header = (empty($pic_header) && !empty($dbFormData['pic_header'])) ? $dbFormData['pic_header'] : $pic_header;
  
  //DBの情報と入力情報が異なる場合にバリデーションを行う
  if($dbFormData['username'] !== $username){
    //名前の最大文字数チェック
    validMaxLenName($username,'username');
  }
  
  if($dbFormData['address'] !== $address){
    //居住地の最大文字数チェック
    validMaxLen($address, 'address');
  }
  
  if($dbFormData['work'] !== $work){
    //職業の最大文字数チェック
    validMaxLen($work, 'work');
  }
  
  if($dbFormData['email'] !== $email){
    //emailの最大文字数チェック
    validMaxLen($email, 'email');
    if(empty($err_msg['email'])){
      //emailの重複チェック
      validEmailDup($email);
    }
    //emailの形式チェック
    validEmail($email, 'email');
    //emailの未入力チェック
    validRequired($email, 'email');
  }
  
  if($dbFormData['comment'] !== $comment){
    //コメントの最大文字数チェック
    validMaxLen($comment, 'comment');
  }
  
  if(empty($err_msg)){
    debug('バリデーションOKです。');
    
    //例外処理
    try {
      //DBへ接続
      $dbh = dbConnect();
      //SQL文作成
      $sql = 'UPDATE users SET username = :username, sex = :sex, address = :address, birthday = :birthday, work = :work, email = :email, comment = :comment , pic_icon = :pic_icon, pic_header = :pic_header WHERE id = :user_id';
      $data = array(':username' => $username, ':sex' => $sex, ':address' => $address, ':birthday' => $birthday , 'work' => $work, ':email' => $email, ':comment' => $comment, ':pic_icon' => $pic_icon, ':pic_header' => $pic_header, ':user_id' => $dbFormData['id']);
      debug('プロフィール情報アップデート');
      // クエリ実行
      $stmt = queryPost($dbh, $sql, $data);

      //クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = SUC01;
        debug('マイページへ遷移します。');
        header("Location:mypage.php"); //マイページへ
        exit();
      }
    } catch (Exception $e) {
      error_log('エラー発生：'. $e->getMessage());
      $err_msg['common'] = MSG06;
    }
  }
}

?>

<?php
$siteTitle = 'イラストレーター プロフィール編集';
require('head.php');
?>

<body>
  <?php
  require('header.php');
  ?>

  <section class="prof-edit">
    <div class="wrap">
      <div class="edit-title">プロフィール編集</div>
      <div class="area-msg">
        <?php
        getErrMsg('common');
        ?>
      </div>
      <form class="container" action="" method="post" enctype="multipart/form-data">
        <table class="prof-table">
          <tbody>
            <tr>
              <th>名前</th>
              <td>
                <div class="area-msg"><?php echo getErrMsg('username'); ?></div>
                <input class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>" name="username" type="text" value="<?php echo getFormData('username'); ?>">
              </td>
            </tr>
            <tr>
              <th>性別</th>
              <td>
                <label class="men"><input name="sex" type="radio" value="1" <?php if(empty(getFormData('sex')) || getFormData('sex') === '1') echo 'checked'; ?>>男性</label>
                <label class="women"><input name="sex" type="radio" value="2" <?php if(getFormData('sex') === '2') echo 'checked'; ?>>女性</label></td>
            </tr>
            <tr>
              <th>居住地</th>
              <td>
                <div class="area-msg"><?php echo getErrMsg('address'); ?></div>
                <input class="<?php if(!empty($err_msg['address'])) echo 'err'; ?>" name="address" type="text" value="<?php echo getFormData('address'); ?>">
              </td>
            </tr>
            <tr>
              <th>生年月日</th>
              <td>
                <div class="area-msg"><?php echo getErrMsg('birthday'); ?></div>
                <input class="<?php if(!empty($err_msg['birthday'])) echo 'err'; ?>" name="birthday" type="date" min="1900-1-1" value="<?php echo getFormData('birthday'); ?>">
              </td>
            </tr>
            <tr>
              <th>職業</th>
              <td>
                <div class="area-msg"><?php echo getErrMsg('work'); ?></div>
                <input class="<?php if(!empty($err_msg['work'])) echo 'err'; ?>" name="work" type="text" value="<?php echo getFormData('work'); ?>">
              </td>
            </tr>
            <tr>
              <th>メールアドレス</th>
              <td>
                <div class="area-msg"><?php echo getErrMsg('email'); ?></div>
                <input class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>" name="email" type="text" value="<?php echo getFormData('email'); ?>" name="email" type="text">
              </td>
            </tr>
            <tr>
              <th>自己紹介</th>
              <div class="area-msg"><?php echo getErrMsg('comment'); ?></div>
              <td>
                <textarea class="<?php getErrMsg('comment'); ?>" name="comment" cols="40" rows="5"><?php echo getFormData('comment'); ?></textarea>
              </td>
            </tr>
            <tr>
              <th>プロフィール画像</th>
              <td>
                <label class="area-drop my-img">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <img src="<?php echo getFormData('pic_icon'); ?>" class="prev-img" style="<?php if(empty(getFormData('pic_icon'))) echo 'display:none;'?>">
                  <input type="file" name="pic_icon" class="file-input">
                  <div class="drop-comment">クリックしてファイルを選択<br>またはドラッグ＆ドロップ</div>
                </label>
              </td>
            </tr>
            <tr>
              <th>プロフィールのヘッダー画像</th>
              <td>
                <label class="area-drop my-header">
                  <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                  <img src="<?php echo getFormData('pic_header'); ?>" class="prev-img" style="<?php if(empty(getFormData('pic_header'))) echo 'display:none;'?>">
                  <input type="file" name="pic_header" class="file-input">
                  <div class="drop-comment">クリックしてファイルを選択<br>またはドラッグ＆ドロップ</div>
                </label>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="submit"><input type="submit" value="変更を保存"></div>
      </form>
    </div>
  </section>


  <?php
  require('footer.php');
  ?>
