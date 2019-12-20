<?php
//================================
// ログ設定
//================================
//ログの取得有無
ini_set('log_errors', 'on');
//ログの出力先
ini_set('error_log','php_log');

//================================
// デバッグログ
//================================
//デバッグフラグ
$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

//================================
// 画面表示処理開始ログ関数
//================================
function debugLogStart(){
  debug('>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> 画面表示処理開始');
  debug('セッションID：'.session_id());
  debug('セッション変数の中身：'.print_r($_SESSION,true));
  debug('現在日時タイムスタンプ：'.time());
}

//================================
// セッション準備・セッション有効期限を延ばす
//================================
//セッションファイルの置き場を変更する（/var/tmp/以下に置くと30日は削除されない）
session_save_path("/var/tmp/");
//ガページコレクションが削除するセッションの有効期限を設定（30日以上経っているものに対してだけ1/100の確率で削除）
ini_set('session.cookie_gc_maxlifetime', 60*60*24*30);
//ブラウザを閉じても削除されないようにクッキー自体の有効期限を延ばす
ini_set('session.cookie_lifetime', 60*60*24*30);
//セッションを使う
session_start();
//現在のセッションIDを新しく生成したものと置き換える（セキュリティ対策）
session_regenerate_id();

//================================
// 定数
//================================
//エラーメッセージを定数に設定
define('MSG01','入力必須です');
define('MSG02','Emailの形式で入力してください');
define('MSG03','20文字以内で入力してください');
define('MSG04','255文字以内で入力してください');
define('MSG05','そのEmailはすでに登録されています');
define('MSG06','エラーが発生しました。しばらく経ってからやり直してください。');
define('MSG07','6文字以上で入力してください');
define('MSG08','半角英数字のみご利用いただけます');
define('MSG09','パスワード（再入力）が合っていません');
define('MSG10','メールアドレスまたはパスワードが違います');
define('MSG11','古いパスワードが違います');
define('MSG12','古いパスワードと同じです');
define('MSG13','文字で入力してください');
define('MSG14','正しくありません');
define('MSG15','有効期限が切れています');
define('MSG16','画像がない状態で投稿はできません');
define('SUC01','プロフィールを変更しました');
define('SUC02','パスワードを変更しました');
define('SUC03','メールを送信しました');
define('SUC04','登録しました');

//================================
// グローバル変数
//================================
//エラーメッセージ格納用の配列
  $err_msg = array();

//================================
// バリデーション関数
//================================
//未入力チェック
function validRequired($str, $key){
  if($str === ''){
    global $err_msg;
    $err_msg[$key] = MSG01;
  }
}

//Email形式チェック
function validEmail($str, $key){
  if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG02;
    debug('Email');
  }
}

//バリデーション関数（Email重複チェック）
function validEmailDup($email){
  global $err_msg;
  //例外処理
  try {
    // DB接続
    $dbh = dbConnect();
    // SQL文作成
    $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
    $data = array(':email' => $email);
    // クエリ実行
    debug('Email重複チェック');
    $stmt = queryPost($dbh, $sql, $data);
    // クエリ結果の値を取得
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //array_shiftで配列の先頭を取り出す
    if(!empty(array_shift($result))){
      $err_msg['email'] = MSG05;
    }
    
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
    $err_msg['common'] = MSG06;
  }
}

//最大文字数チェック(名前)
function validMaxLenName($str, $key, $maxName = 20){
  if(mb_strlen($str) > $maxName){
    debug(mb_strlen($str));
    debug($maxName);
    global $err_msg;
    $err_msg[$key] = MSG03;
  }
}

//最大文字数チェック(255文字)
function validMaxLen($str, $key, $max = 255){
  if(mb_strlen($str) > $max){
    global $err_msg;
    $err_msg[$key] = MSG04;
  }
}

//最小文字数チェック
function validMinLen($str, $key, $min = 6){
  if(mb_strlen($str) < $min){
    global $err_msg;
    $err_msg[$key] = MSG07;
  }
}

//半角チェック
function validHalf($str, $key){
  if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG08;
  }
}

//同値チェック
function validMatch($str1, $str2, $key){
  if($str1 !== $str2){
    global $err_msg;
    $err_msg[$key] = MSG09;
  }
}

//パスワードチェック
function validPass($str, $key){
  //半角英数字チェック
  validHalf($str, $key);
  //最大文字数チェック
  validMaxLen($str, $key);
  //最小文字数チェック
  validMinLen($str, $key);
}

//固定長チェック
function validLength($str, $key, $length = 8){
  if( mb_strlen($str) !== $length ){
    global $err_msg;
    $err_msg[$key] = $length . MSG13;
  }
}

//================================
// データベース
//================================
//DB接続関数
function dbConnect(){
  //DBへの接続準備
  $dsn = 'mysql:dbname=illustrator;host=localhost;charset=utf8';
  $user = 'root';
  $password = 'root';
  $options = array(
    // SQL実行失敗時にはエラーコードのみ設定
    PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
    // デフォルトフェッチモードを連想配列形式に設定
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
    // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
  );
  //PDOオブジェクト作成（DBへ接続）
  $dbh = new PDO($dsn, $user, $password, $options);
  return $dbh;
}

function queryPost($dbh, $sql, $data){
  //クエリ作成
  $stmt = $dbh->prepare($sql);
  //プレースホルダに値をセットし、SQL文を実行
  if(!$stmt->execute($data)){
    debug('クエリに失敗しました。');
    debug('失敗したSQL：'.print_r($stmt,true));
    debug('SQLエラー'.print_r($stmt->errorInfo(),true));
    $err_msg['common'] = MSG06;
    return 0;
  }
  debug('クエリ成功');
  return $stmt;
}

//ユーザー情報取得
function getUser($user_id){
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT * from users WHERE id = :user_id AND delete_flg = 0';
    $data = array(':user_id' => $user_id);
    //クエリ実行
    debug('ユーザー情報取得');
    $stmt = queryPost($dbh, $sql, $data);
    
    //クエリ結果のデータを１レコード返却
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
    
  } catch (Exception $e) {
    error_log('エラー発生' . $e->getMessage());
  }
}

//イラスト情報取得
function getIllust($user_id, $p_id){
  debug('イラスト情報を取得します');
  debug('ユーザーID：' . $user_id);
  debug('イラストID：' . $p_id);
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    $sql = 'SELECT * FROM illust WHERE id = :p_id AND user_id = :user_id';
    $data = array(':p_id' => $p_id, ':user_id' => $user_id);
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
    
  } catch (Exception $e) {
    error_log('エラー発生' . $e->getMessage());
  }
}

//イラスト検索用(ページ内で表示するデータと総レコード数、総ページ数)の情報取得
function getIllustList($headNum = 1, $sort = 1, $showNum = 20, $searchWord = '', $u_id = ''){
  debug('イラスト情報を取得します');
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //マイページの場合は引数にユーザーIDを入れ、SQL文の検索条件にユーザーIDを追加する
    $user_id = (!empty($u_id)) ? ' WHERE il.user_id = ' .$u_id : '';
    //SQL文作成
    $sql = 'SELECT id FROM illust AS il' .$user_id;
    $data = array();
    //クエリ実行
    debug('イラストID取得');
    debug($sql);
    $stmt = queryPost($dbh, $sql, $data);
    //総レコード数取得
    $rst['total'] = $stmt->rowCount();
    //総ページ数取得
    $rst['total_page'] = ceil($rst['total']/$showNum);
    if(!$stmt){
      return false;
    }
    //ホワイトリスト(1.新着順 2.ブックマーク順 3.閲覧数順)
    $sort_whitelist = array(1 => 'il.id', 2 => 'COUNT(b.illust_id)', 3 => 'il.look');
    //ホワイトリストによる検証
    $sort_safe = isset($sort_whitelist[$sort]) ? $sort_whitelist[$sort] : $sort_whitelist[1];
    $searchWhere = (!empty($searchWord)) ? 'WHERE il.title LIKE :search_word' : '';
    //ページング用のSQL文作成
    if($sort === 2){
      //マイページの場合は引数にユーザーIDを入れ、SQL文の検索条件にユーザーIDを追加する
      $user_id = (!empty($u_id)) ? ' il.user_id = ' .$u_id .' AND ' : '';
      $sql = 'SELECT il.id, il.pic, il.title, il.user_id, il.scope, il.comment, il.sort, il.look, il.create_date, il.delete_flg, u.username, ' .$sort_safe .' as bookmarker from illust AS il LEFT JOIN bookmark AS b ON il.id = b.illust_id LEFT JOIN users AS u ON il.user_id = u.id WHERE u.delete_flg = 0 AND ' .$user_id .' il.title LIKE :search_word GROUP BY il.id, il.id, il.pic, il.title, il.user_id, il.scope, il.comment, il.sort, il.look, il.create_date, il.delete_flg ORDER BY bookmarker DESC LIMIT ' .$showNum .' OFFSET ' .$headNum;
//    }elseif($sort === 3){
//      $sql = 'SELECT * from illust AS il LEFT JOIN users AS u ON il.user_id = u.id LEFT JOIN bookmark AS b ON u.id = b.illust_id ' .'ORDER BY LIMIT ' .$showNum .' OFFSET ' .$headNum .(!empty($searchWord)) ? 'WHERE il.title LIKE :search_word' : '';
    }else{
      //マイページの場合は引数にユーザーIDを入れ、SQL文の検索条件にユーザーIDを追加する
      $user_id = (!empty($u_id)) ? ' il.user_id = ' .$u_id .' AND' : '';
      $sql = 'SELECT il.id, il.pic, il.title, il.user_id, il.scope, il.comment, il.sort, il.look, il.create_date, il.delete_flg, u.username from illust AS il LEFT JOIN users AS u ON il.user_id = u.id WHERE u.delete_flg = 0 AND ' .$user_id .' il.title LIKE :search_word ORDER BY ' .$sort_safe .' DESC' .' LIMIT ' .$showNum .' OFFSET ' .$headNum;
    }
    
    $data = array(':search_word' => '%'.$searchWord.'%');
    debug('SQL：'.$sql);
    //クエリ実行
    debug($headNum + 1 .'番目から' .$showNum .'件分のデータを取得');
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      //クエリ結果のデータの全レコードを取得
      $rst['data'] = $stmt->fetchAll();
      debug('総レコード数：' . print_r($rst['total'],true));
      debug('総ページ数：' . print_r($rst['total_page'],true));
      
      //検索を使った場合の総レコード数を取得
      if(!empty($searchWord)){
        $sql = 'SELECT title from illust WHERE title LIKE :search_word';
        debug('検索した総レコード数を取得');
        $stmt = queryPost($dbh, $sql, $data);
        $rst['search_total'] = $stmt->rowCount();
        $rst['search_total_page'] = ceil($rst['search_total']/$showNum);
        debug('検索結果の総レコード数：' . print_r($rst['search_total'],true));
        debug('検索結果の総ページ数：' . print_r($rst['search_total_page'],true));
      }
      return $rst;
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
  }
}

//マイページブックマーク検索用(ページ内で表示するデータと総レコード数、総ページ数)の情報取得
function getIllustBookmarkList($headNum = 1, $sort = 1, $showNum = 20, $u_id ){
  debug('イラストのブックマーク情報を取得します');
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT il.id FROM illust AS il LEFT JOIN users AS u ON il.user_id = u.id LEFT JOIN bookmark as b ON il.id = b.illust_id WHERE b.user_id = :u_id';
    $data = array(':u_id' => $u_id);
    //クエリ実行
    debug('ブックマークしたイラストIDを取得');
    debug($sql);
    $stmt = queryPost($dbh, $sql, $data);
    //総レコード数取得
    $rst['total'] = $stmt->rowCount();
    debug('一時的に見ています：'.print_r($rst['total'],true));
    //総ページ数取得
    $rst['total_page'] = ceil($rst['total']/$showNum);
    debug('一時的に見ています：'.print_r($rst['total_page'],true));
    if(!$stmt){
      return false;
    }
    //ホワイトリスト(1.新着順 2.ブックマーク順 3.閲覧数順)
    $sort_whitelist = array(1 => 'il.id', 2 => 'COUNT(b.illust_id)', 3 => 'il.look');
    //ホワイトリストによる検証
    $sort_safe = isset($sort_whitelist[$sort]) ? $sort_whitelist[$sort] : $sort_whitelist[1];
    //ページング用のSQL文作成
    if($sort === 2){
      $sql = 'SELECT il.id, il.pic, il.title, il.user_id, il.scope, il.comment, il.sort, il.look, il.create_date, il.delete_flg, u.username, ' .$sort_safe .' as bookmarker from illust AS il LEFT JOIN bookmark AS b ON il.id = b.illust_id LEFT JOIN users AS u ON il.user_id = u.id WHERE b.user_id = :u_id GROUP BY il.id, il.id, il.pic, il.title, il.user_id, il.scope, il.comment, il.sort, il.look, il.create_date, il.delete_flg ORDER BY bookmarker DESC LIMIT ' .$showNum .' OFFSET ' .$headNum;
      //    }elseif($sort === 3){
      //      $sql = 'SELECT * from illust AS il LEFT JOIN users AS u ON il.user_id = u.id LEFT JOIN bookmark AS b ON u.id = b.illust_id ' .'ORDER BY LIMIT ' .$showNum .' OFFSET ' .$headNum .(!empty($searchWord)) ? 'WHERE il.title LIKE :search_word' : '';
    }else{
      //マイページの場合は引数にユーザーIDを入れ、SQL文の検索条件にユーザーIDを追加する
      $sql = 'SELECT il.id, il.pic, il.title, il.user_id, il.scope, il.comment, il.sort, il.look, il.create_date, il.delete_flg, u.username from illust AS il LEFT JOIN users AS u ON il.user_id = u.id LEFT JOIN bookmark as b ON il.id = b.illust_id WHERE b.user_id = :u_id ORDER BY ' .$sort_safe .' DESC' .' LIMIT ' .$showNum .' OFFSET ' .$headNum;
    }

    $data = array(':u_id' => $u_id);
    debug('SQL：'.$sql);
    //クエリ実行
    debug($headNum + 1 .'番目から' .$showNum .'件分のデータを取得');
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      //クエリ結果のデータの全レコードを取得
      $rst['data'] = $stmt->fetchAll();
      debug('総レコード数：' . print_r($rst['total'],true));
      debug('総ページ数：' . print_r($rst['total_page'],true));
      return $rst;
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
  }
}

//イラスト詳細画面の投稿情報を取得
function getContribution($i_id){
  debug('イラスト情報を取得します');
  debug('イラストID：'.$i_id);
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT il.id, il.pic, il.title, il.user_id, il.scope, il.comment, il.sort, il.look, u.username, u.pic_icon from illust AS il LEFT JOIN users AS u ON il.user_id = u.id WHERE il.id = :i_id AND il.delete_flg = 0 AND u.delete_flg = 0';
    $data = array(':i_id' => $i_id);
    debug('イラスト詳細画面の投稿情報を取得');
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);
    
    if($stmt){
      return $stmt->fetch(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
    
  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
  }
}

//イラスト詳細画面のサイドバーのイラスト情報を取得
function getSideIllust($u_id){
  debug('サイドバーのイラスト情報を取得します');
  debug('投稿者のID：'.$u_id);
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT il.id, il.pic, il.user_id, il.scope, il.sort from illust AS il LEFT JOIN users AS u ON il.user_id = u.id WHERE u.id = :u_id AND il.delete_flg = 0 AND u.delete_flg = 0 ORDER BY il.id DESC LIMIT 4';
    $data = array(':u_id' => $u_id);
    debug('イラスト詳細画面の投稿情報を取得');
    debug('SQL：'.print_r($sql,true));
    //クエリ実行
    $stmt = queryPost($dbh, $sql, $data);

    if($stmt){
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }else{
      return false;
    }

  } catch (Exception $e) {
    error_log('エラー発生：' . $e->getMessage());
  }
}

//メッセージ取得
function getOpenMessage($i_id){
  debug('メッセージ情報を取得します');
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT m.id, m.il_id, m.my_user_id, m.msg, m.send_date, u.username, u.pic_icon from message AS m LEFT JOIN illust AS il ON m.il_id = il.id LEFT JOIN users AS u ON u.id = m.my_user_id WHERE il.id = :i_id';
    debug('SQL：'.$sql);
    $data = array(':i_id' => $i_id);
    $stmt = queryPost($dbh, $sql, $data);
    if($stmt){
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }else{
      return false;
    }
    
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

//ブックマークされているかチェック
function getBookmark($i_id, $u_id){
  debug('ブックマーク情報を取得します');
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT * from bookmark WHERE illust_id = :i_id AND user_id = :u_id';
    debug('SQL：'.$sql);
    debug($i_id);
    debug($u_id);
    $data = array(':i_id' => $i_id, ':u_id' => $u_id);
    //クエリ実行
    debug('このイラストに対してブックマークをしているかを検索');
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->rowCount();
    debug('検索結果：'.$result);
    if(!empty($result)){
      return true;
    }else{
      return false;
    }
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

//================================
// メール送信
//================================
function sendMail($from, $to, $subject, $comment){
  if(!empty($to) && !empty($subject) && !empty($comment)){
    //文字化け対策
    mb_language("japanese");
    mb_internal_encoding("UTF-8");
    
    //メールを送信
    $result = mb_send_mail($to, $subject, $comment, "From: ".$from);
    //送信結果を判定
    if ($result) {
      debug('メールを送信しました。');
    } else {
      debug('【エラー発生】メールの送信に失敗しました。');
    }
  }
}

//================================
// その他
//================================
//サニタイズ
function sanitize($str){
  return htmlspecialchars($str,ENT_QUOTES);
}
//エラーメッセージ
function getErrMsg($key){
  global $err_msg;
  if(!empty($err_msg[$key])){
    return $err_msg[$key];
  }
}
//sessionを一回だけ取得できる
function getSessionFlash($key){
  if(!empty($_SESSION[$key])){
    $data = $_SESSION[$key];
    $_SESSION[$key] = '';
    return $data;
  }
}
//ランダム8桁生成
function makeRandKey($length = 8){
  return substr(bin2hex(random_bytes($length)), 0, $length);
  }

//フォームの入力保持
function getFormData($str, $flg = false){
  if($flg){
    $method = $_GET;
  }else{
    $method = $_POST;
  }
  global $dbFormData;
  //ユーザーデータがある場合
  if(!empty($dbFormData)){
    //フォームのエラーがある場合
    if(!empty($err_msg[$str])){
      //POSTにデータがある場合
      if(isset($method[$str])){
        return sanitize($method[$str]);
      }else{
        //ない場合はDBの情報を表示
        return sanitize($dbFormData[$str]);
      }
    }else{
      //POSTにデータがあり、DBの情報と違う場合
      if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
        return sanitize($method[$str]);
      }else{
        //POSTにデータがないかDBの情報とPOSTが同じ場合はDBの情報を表示
        return sanitize($dbFormData[$str]);
      }
    }
  }else{
    if(isset($method[$str])){
      return sanitize($method[$str]);
    }
  }
}
//画像アップロード
function uploadImg($file, $key){
  debug('画像アップロード処理開始');
  debug('FILE情報：'.print_r($file,true));
  
 if(isset($file['error']) && is_int($file['error'])){
   try {
     switch ($file['error']) {
       case UPLOAD_ERR_OK:
         break;
       case UPLOAD_ERR_NO_FILE:  //ファイル未選択時
         throw new RuntimeException('ファイルが選択されていません');
       case UPLOAD_ERR_INI_SIZE:  //php.ini定義の最大サイズが超過した時
       case UPLOAD_ERR_FORM_SIZE:  //フォーム定義の最大サイズが超過した時
         throw new RuntimeException('ファイルサイズが大きすぎます');
       default:  //その他
         throw new RuntimeException('その他のエラーが発生しました');
     }
     //MIMEタイプのチェック
     $type = @exif_imagetype($file['tmp_name']);
     if (!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG,true])) {
      throw new RuntimeException('画像形式が未対応です');
     }
         
     //ハッシュ化して画像を保存
     $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
     //temp領域からファイルを移動させる
     if( !move_uploaded_file($file['tmp_name'], $path)) {
       throw new RuntimeException('ファイルの保存時にエラーが発生しました');
     }
     //保存したファイルのパーミッション（権限）を変更する
     chmod($path, 0644);
     
     debug('ファイルは正常にアップロードされました');
     debug('ファイルパス：'.$path);
     return $path;
     
   } catch (RuntimeException $e) {
     
     debug($e->getMessage());
     global $err_msg;
     $err_msg[$key] = $e->getMessage();
     
   }
  }
}

//GETパラメータ付与(引数：ページから戻る時などに取り除きたいキー)
function appendGetParam($del_key = array()){
  if(!empty($_GET)){
    $str = '?';
    foreach ( $_GET as $key => $value){
      if(!in_array($key,$del_key,true)){
        $str .= $key .'=' .$value.'&';
      }
    }
    $str = mb_substr($str, 0, -1, "UTF-8");
    return $str;
  }
}

//ソート方法判別(ソート方法のデフォルトは日付順とする)
function getCurrentSort($sort){
  if($sort === 2){
    return '';
  }else if($sort === 3){
    return '';
  }
  return 'current';
}

//ページング
//(現在表示しているページ、総ページ数、検索用GETパラメータリンク、ページネーション表示数)
//ページネーションフラグ：0…トップ画面,1…マイページ
function pagination ( $currentPageNum, $totalPageNum, $sort, $searchWord = '', $pageNationFlg, $pageNationShow = 7){
  //総ページ数が表示項目数以上の場合
  //現在のページが総ページと同じなら、左にリンクを6個出す
  if($currentPageNum == $totalPageNum && $totalPageNum > $pageNationShow){
    $minPageNum = $currentPageNum -6;
    $maxPageNum = $currentPageNum;
    //現在のページが総ページの1つ前なら左にリンク5個、右にリンク1個
  }else if($currentPageNum == ($totalPageNum -1) && $totalPageNum > $pageNationShow){
    $minPageNum = $currentPageNum -5;
    $maxPageNum = $currentPageNum +1;
    //現在のページが総ページの2つ前なら左にリンク4個、右にリンク2個
  }else if($currentPageNum == ($totalPageNum -2) && $totalPageNum > $pageNationShow){
    $minPageNum = $currentPageNum -4;
    $maxPageNum = $currentPageNum +2;
    //現在のページが1の場合、右にリンク6個
  }else if($currentPageNum == 1 && $totalPageNum > $pageNationShow){
    $minPageNum = $currentPageNum;
    $maxPageNum = $currentPageNum +6;
    //現在のページが2の場合、左にリンク1個、右にリンク6個
  }else if($currentPageNum == 2 && $totalPageNum > $pageNationShow){
    $minPageNum = $currentPageNum -1;
    $maxPageNum = $currentPageNum +5;
    //現在のページが3の場合、左にリンク2個、右にリンク4個
  }else if($currentPageNum == 3 && $totalPageNum > $pageNationShow){
    $minPageNum = $currentPageNum -2;
    $maxPageNum = $currentPageNum +4;
    //総ページ数が表示項目数より少ない場合
  }else if($totalPageNum < $pageNationShow){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
    //それ以外
  }else{
    $minPageNum = $currentPageNum -3;
    $maxPageNum = $currentPageNum +3;
  }
  
  //ソート方法をページネーションに与える
  if($sort === '2'){
    $sort_safe = 'sort=bookmark';
  }else if($sort === '3'){
    $sort_safe = 'sort=look';
  }else{
    $sort_safe = 'sort=date';
  }
  
  
  debug('一時的に見ていますcurrent：'.$currentPageNum);
  debug('一時的に見ていますtotal：'.$totalPageNum);
  if($pageNationFlg == 0){
    echo '<div class="pagination">';
    echo '<ul>';
    if($currentPageNum != 1){
      echo '<li class="list-item"><a href="index.php?p='.($currentPageNum-1).'&' .$sort_safe .'&search-word=' .$searchWord .'">◀︎</a></li>';
    }
    for($i = $minPageNum; $i <= $maxPageNum; $i++){
      echo '<li class="list-item ';
      if($currentPageNum == $i){ echo 'active'; } 
      echo '"><a href="index.php?p=' .$i .'&' .$sort_safe .'&search-word=' .$searchWord .'">'.$i.'</a></li>';
    }
    if($currentPageNum != $totalPageNum ){
      echo '<li class="list-item"><a href="index.php?p='.($currentPageNum+1).'&' .$sort_safe .'&search-word' .$searchWord .'">▶️</a></li>';
    }
    echo '<ul>';
    echo '</ul>';
    echo '</div>';
  }else{
    echo '<div class="pagination">';
    echo '<ul>';
    if($currentPageNum != 1){
      echo '<li class="list-item"><a href="mypage.php?p='.($currentPageNum-1).'&' .$sort_safe .'">◀︎</a></li>';
    }
    for($i = $minPageNum; $i <= $maxPageNum; $i++){
      echo '<li class="list-item ';
      if($currentPageNum == $i){ echo 'active'; } 
      echo '"><a href="mypage.php?p=' .$i .'&' .$sort_safe .'">'.$i.'</a></li>';
    }
    if($currentPageNum != $totalPageNum ){
      echo '<li class="list-item"><a href="mypage.php?p='.($currentPageNum+1).'&' .$sort_safe .'">▶️</a></li>';
    }
    echo '<ul>';
    echo '</ul>';
    echo '</div>';
  }
}
