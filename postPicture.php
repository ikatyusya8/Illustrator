<?php

//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('イラスト投稿 postPicture.php');
debug('================================');
debugLogStart();

//ログイン認証
require('auth.php');

//================================
// 画面処理
//================================
//GETデータを格納
$p_id = (!empty($_GET['p_id'])) ? $_GET['p_id'] : '';
//イラストデータを取得
$dbFormData = (!empty($p_id)) ? getIllust($_SESSION['user_id'], $p_id) : '';
//新規登録か更新の判別用フラグ
$edit_flg = (!empty($dbFormData)) ? true : false;
debug('イラストID：' . print_r($p_id, true));
debug('イラストデータ：' . print_r($dbFormData, true));

//GETのイラストのIDが不正に変えられた場合はマイページへ遷移
if(!empty($p_id) && empty($dbFormData)){
  header("Location:mypage.php");
}

//POSTされているかチェック
if(!empty($_POST)){
  debug('POSTされています');
  debug('POST情報；' . print_r($_POST, true));
  debug('FILE情報：' . print_r($_FILES, true));
  
  //変数にユーザー情報を代入
  $post_pic = (!empty($_FILES['post_pic']['name'])) ? uploadImg($_FILES['post_pic'], 'post_pic') : '';
  $title = $_POST['title'];
  $scope = $_POST['scope'];
  $comment = $_POST['comment'];
  
  //画像をPOSTしていないが、DBに登録されている場合
  $post_pic = (empty($post_pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $post_pic;
  
  //画像がない場合エラー表示
  if(empty($post_pic)){
    $err_msg['file'] = MSG16;
  }
  
  //新規登録
  if(empty($dbFormData)){
    //未入力チェック
    validRequired($title, 'title');
    //最大文字数チェック
    validMaxLenName($title, 'title');
    validMaxLen($comment, 'comment');
    //POSTがありDBにも登録されている場合
  }else{
    if($dbFormData['title'] !== $title){
      //最大文字数チェック
      validMaxLenName($title, 'title');
    }
    if($dbFormData['comment'] !== $comment){
      //最大文字数チェック
      validMaxLen($comment, 'comment');
    }
  }
  
  if(empty($err_msg)){
    debug('バリデーションOKです');
    try {
      //DBへ接続
      $dbh = dbConnect();
      //SQL文（更新）
      if($edit_flg){
        debug('DB更新です');
        $sql = 'UPDATE illust SET pic = :pic, title = :title, scope = :scope, comment = :comment WHERE user_id = :user_id, id = :id';
        $data = array(':pic' => $post_pic, ':title' => $title, ':scope' => $scope, ':comment' => $comment, ':user_id' => $_SESSION['user_id'], ':id' => $p_id);
      }else{
        debug('新規登録です');
        $sql = 'INSERT INTO illust (pic, title, user_id, scope, comment, create_date) VALUES (:pic, :title, :user_id, :scope, :comment, :create_date)';
        $data = array(':pic' => $post_pic, ':title' => $title, ':user_id' => $_SESSION['user_id'], ':scope' => $scope, ':comment' => $comment, ':create_date' => date('Y-m-d H:i:s'));
      }
      debug('SQL：' . $sql);
      debug('流し込みデータ：'.print_r($data,true));
      //クエリ実行
      debug('イラスト情報登録or更新');
      $stmt = queryPost($dbh, $sql, $data);
      
      //クエリ成功の場合
      if($stmt){
        $_SESSION['msg_success'] = SUC04;
        debug('マイページへ遷移します');
        header("Location:mypage.php");
      }
      
    } catch (Exception $e) {
      error_log('エラー発生：' . $e->getMessage());
      $err_msg['common'] = MSG06;
    }
  }
}
?>

<?php
$siteTitle = 'イラストレーター イラスト投稿ページ';
require('head.php');
?>

<body>
  <?php
  require('header.php');
  ?>

  <div class="title-top">イラスト投稿</div>
  <div class="area-msg"><?php echo getErrMsg('common'); ?></div>
  <form action="" method="post" enctype="multipart/form-data">
    <div class="post-picture">
      <div class="container">
        <div class="area">
          <div class="area-msg"><?php echo getErrMsg('file'); ?></div>
          <label class="area-drop">
            <div class="drop-comment">クリックしてファイルを選択またはドラッグ＆ドロップ</div>
            <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
            <input class="file-input" type="file" name="post_pic">
            <img src="" alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;'; ?>">
          </label>
        </div>
        <div class="wrap">
          <div class="title">
            <div class="area-msg"><?php echo getErrMsg('title'); ?></div>
            <input id="js-count" class="title-input <?php if(!empty($err_msg['title'])) echo 'err'; ?>" type="text" name="title" placeholder="タイトル" value="<?php if(!empty($dbFormData['title'])) echo $dbFormData['title']; ?>">
            <span class="count">0</span>/40
          </div>
          <div class="scope">
            <div class="area-msg"></div>
            <span class="scope-name">公開範囲</span>
            <label>
              <input type="radio" name="scope" value="1" checked>全体
            </label>
            <label>
              <input type="radio" name="scope" value="2">フォロワー
            </label>
            <label>
              <input type="radio" name="scope" value="3">非公開
            </label>
          </div>
          <div class="comment">
            <div class="area-msg"><?php echo getErrMsg('comment'); ?></div>
            <textarea id="js-count" class="comment-textarea <?php if(!empty($err_msg['comment'])) echo 'err'; ?>" name="comment" cols="50" rows="5" placeholder="コメント" value="<?php if(!empty($dbFormData['comment'])) echo $dbFormData['comment']; ?>"></textarea>
            <span class="count">0</span>/255
          </div>
          <div class="submit-container"><input type="submit"></div>
        </div>
      </div>
    </div>
  </form>

  <?php
  require('footer.php');
  ?>
