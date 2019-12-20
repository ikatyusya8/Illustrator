<?php
//共通変数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('Ajax処理 Ajax.php');
debug('================================');
debugLogStart();

//postがあり、ログインしている場合
if(isset($_POST['illustId']) && isset($_SESSION['user_id'])){
  debug('P0ST送信があります');
  $i_id = $_POST['illustId'];
  debug('イラストID：' .$i_id);
  //例外処理
  try {
    //DBへ接続
    $dbh = dbConnect();
    //SQL文作成
    $sql = 'SELECT * from bookmark WHERE illust_id = :i_id AND user_id = :u_id';
    $data = array(':i_id' => $i_id, ':u_id' => $_SESSION['user_id']);
    //クエリ実行
    debug('このイラストに対してブックマークをしているかを検索');
    $stmt = queryPost($dbh, $sql, $data);
    $result = $stmt->rowCount();
    debug('検索結果：'.$result);
    
    //ブックマークしていた場合
    if(!empty($result)){
      $sql = 'DELETE FROM bookmark WHERE illust_id = :i_id AND user_id = :u_id';
      $data = array(':i_id' => $i_id, 'u_id' => $_SESSION['user_id']);
      debug('ブックマークレコードを削除します');
      $stmt = queryPost($dbh, $sql, $data);
      //ブックマークしていなかった場合
    }else{
      $sql = 'INSERT INTO bookmark (user_id, illust_id, create_date) VALUES (:u_id, :i_id, :date)';
      $data = array(':u_id'=>$_SESSION['user_id'], ':i_id'=>$i_id, ':date'=>date('Y-m-d H:i:s'));
      debug('ブックマークレコードを追加します');
      $stmt = queryPost($dbh, $sql, $data);
    }
    
  } catch (Exception $e) {
    error_log('エラー発生：'.$e->getMessage());
  }
}

debug('Ajax処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');

?>
