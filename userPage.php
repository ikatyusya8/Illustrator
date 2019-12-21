<?php
//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('ユーザーページ userPage.php');
debug('================================');
debugLogStart();

//================================
// 画面処理
//================================
//ページデータ(現在のページ、データがなければ1ページとする)
$currentPageNum = (!empty($_GET['p'])) ? $_GET['p']: 1;
//投稿者のユーザーID
$postUserId = (!empty($_GET['u_id'])) ? $_GET['u_id']: 1;
//ソート順
$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';
debug('現在のページ：'.print_r($currentPageNum,true));
//不正な値が入っているかチェック
if(!is_int((int)$currentPageNum)){
  error_log('エラー発生:指定ページに不正な値が入りました');
  header("Location:index.php"); //トップページへ
}

//表示件数
$showNum = 20;
//先頭の絵が全体の何番目か
$headNum = ($currentPageNum -1) * $showNum;
//表示順
switch ($sort) {
  case 'date':
    $sort = 1;
    break;
  case 'bookmark':
    $sort = 2;
    break;
  case 'look':
    $sort = 3;
    break;
}
//DBからページ内で表示するデータと総レコード数、総ページ数を取得
$dbIllustData = getIllustList($headNum, $sort, $showNum, '', $postUserId);
//ユーザーデータを取得
$dbUserData = getUser($postUserId);

debug('画面表示処理終了 <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<');
?>

<?php 
$siteTitle = 'イラストレーター　ユーザーページ';
require('head.php');
?>

<body>
  <?php
  require('header.php');
  ?>

  <p id="js-show-msg" style="display:none;" class="msg-complete">
    <?php echo getSessionFlash('msg_success'); ?>
  </p>

  <div class="profile">
    <div class="prof-header"><img alt="" src="<?php echo $dbUserData['pic_header']; ?>"></div>
    <div class="prof-icon"><img alt="" src="<?php echo $dbUserData['pic_icon']; ?>"></div>
    <div class="container">
      <div class="user-name"><?php echo $dbUserData['username']; ?></div>
      <section class="range-menu">
        <form action="" method="post" class="illust-or-bookmark">
          <ul class="order_select">
            <li>
              <a href="userPage.php<?php echo '?u_id=' .$postUserId ?>&sort=date<?php if(!empty($searchWord)) echo ('&search-word=' .$searchWord); ?>" class="current">イラスト</a>
            </li>
            <li>
              <a href="userBookmarkPage.php<?php echo '?u_id=' .$postUserId ?>&sort=date<?php if(!empty($searchWord)) echo ('&search-word=' .$searchWord); ?>">ブックマーク</a>
            </li>
          </ul>
        </form>
      </section>
    </div>
  </div>
  <ul class="order_select">
    <li>
      <a href="userPage.php<?php echo '?u_id=' .$postUserId ?>&sort=date<?php if(!empty($searchWord)) echo ('&search-word=' .$searchWord); ?>" class="<?php echo getCurrentSort($sort); ?>">投稿順</a>
    </li>
    <li>
      <a href="userPage.php<?php echo '?u_id=' .$postUserId ?>&sort=bookmark<?php if(!empty($searchWord)) echo ('&search-word=' .$searchWord); ?>" class="<?php if($sort === 2) echo 'current' ?>">人気順</a>
    </li>
    <!--
<li>
<a href="mypage.php?sort=look<?php if(!empty($searchWord)) echo ('$search-word=' .$searchWord); ?>" class="<?php if($sort === 3) echo 'current'; ?>">閲覧数順</a>
</li>
-->
  </ul>
  <?php 
  $totalPage = $dbIllustData['total_page'];
  $totalNumber = $dbIllustData['total'];
  pagination($currentPageNum, $totalPage, sanitize($sort), '', 1); ?>
  <div class="my-pic">
    <div class="pic-container">
      <div class="search"><?php echo ((!empty($dbIllustData['data'])) ? $headNum +1 : 0); ?>-<?php echo $headNum + count($dbIllustData['data']); ?> /<span><?php echo $totalNumber; ?></span>件</div>
      <div class="pic-flex">
        <?php 
        foreach ( $dbIllustData['data'] as $key => $value):
        ?>
        <div class="picture">
          <a href="illustDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&i_id=' .$value['id'] : '?i_id='.$value['id']; ?>" class="author"><img alt="" src="<?php echo sanitize($value['pic']); ?>"></a>
          <div class="pic-info">
            <div><a href="illustDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam() . '&i_id=' .$value['id'] : '?i_id='.$value['id']; ?>" class="pic-title"><span><?php echo sanitize($value['title']); ?></span></a></div>
            <div class="author-wrap"><a href="illustDetail.php" class="author"></a></div>
          </div>
        </div>
        <?php 
        endforeach;
        ?>
      </div>
    </div>
    <?php 
    $totalPage = $dbIllustData['total_page'];
    $totalNumber = $dbIllustData['total'];
    pagination($currentPageNum, $totalPage, sanitize($sort), '', 1); ?>
  </div>


  <?php
  require('footer.php');
  ?>
