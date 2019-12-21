<?php
//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('イラスト詳細ページ illustDetail.php');
debug('================================');
debugLogStart();

//================================
// 画面処理
//================================

//イラストIDのGETパラメータ取得
$i_id = (!empty($_GET['i_id'])) ? $_GET['i_id'] : '';
//DBからイラストデータを取得
$dbContributionData = getContribution($i_id);
debug('一時的に見ています：'.$dbContributionData);

//パラメータに不正な値が入っているかチェック
if(empty($dbContributionData)){
  error_log('エラー発生：指定ページに不正な値が入りました');
  header("Location:index.php");
}
debug('取得したDBデータ：'.print_r($dbContributionData,true));

//サイドバーのイラスト情報を取得
$sideIllust = getSideIllust($dbContributionData['user_id']);

//ブックマークしているかを取得
$bookmark = getBookmark($i_id, $_SESSION['user_id']);

?>

<?php 

$siteTitle = 'イラストレーター　イラスト詳細画面';
require('head.php');
?>

<body>
  <?php
require('header.php');
?>

  <section class="detail-picture">
    <div class="first-half">
      <div class="sidebar">
        <div class="icon">
          <img alt="" src="<?php echo $dbContributionData['pic_icon']; ?>">
        </div>
        <div class="user-name"><a class="user-detail"><?php echo $dbContributionData['username']; ?></a></div>
        <div class="pictures-head"><a href="userPage.php?u_id=<?php echo $dbContributionData['user_id']; ?>"><?php echo $dbContributionData['username']; ?> さんの作品集</a></div>
        <div class="user-pictures">
          <?php foreach ( $sideIllust as $key => $value){
  echo '<div class="img"><a href="illustDetail.php?i_id=' .$value['id'];
  echo '"><img src="' .$value['pic'];
  echo '"></a></div>';
          } ?>
        </div>
<!--        <div class="dm"><a>DMを送る</a></div>-->
      </div>
      <div class="picture">
        <img src="<?php echo $dbContributionData['pic']; ?>">
      </div>
    </div>
    <div class="picture-info second-half">
      <div class="picture-title">
        <div><?php echo $dbContributionData['title']; ?></div>
      </div>
      <div class="under-title">
        <div class="range">
        </div>
        <label data-illustid="<?php echo sanitize($dbContributionData['id']); ?>" class="bookmark js-bookmark-click <?php if($bookmark){ echo 'active'; } ?>"><span class="bookmark-span"><?php if($bookmark){ echo 'ブックマーク済み '; }else{ echo 'ブックマーク '; } ?></span><i class="fa fa-heart-o" aria-hidden="true"></i></label>
      </div>
      <div class="picture-comment"><?php echo $dbContributionData['comment']; ?></div>
      
      <?php require('openMessage.php'); //イラストのコメント欄 ?>
      
    </div>
  </section>

  <?php
require('footer.php');
?>
</body>
