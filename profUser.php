<?php
//共通関数・関数ファイル読み込み
require('function.php');

debug('================================');
debug('ユーザープロフィール profUser.php');
debug('================================');
debugLogStart();

//================================
// 画面処理
//================================
//取得したいユーザーのIDを格納
$u_id = $_GET['u_id'];
//ユーザー情報取得
$dbUserData = getUser($u_id);
if(empty($dbUserData)){ header("Location:mypage.php");}
?>

<?php 
$siteTitle = 'イラストレーター　プロフィール詳細';
require('head.php');
?>


<div class="prof-user">

  <div class="prof-header">
  </div>
  <div class="container">
    <div class="prof-detail">
    <table>
      <tbody>
        <tr>
          <th class="name"><img src="<?php if(!empty($dbUserData['pic_icon'])){ echo $dbUserData['pic_icon']; } ?>" alt=""></th>
          <td><?php if(!empty($dbUserData['username'])){ echo $dbUserData['username']; }else{ echo '未設定'; } ?></td>
        </tr>
        <tr>
          <th>性別</th>
          <td><?php if($dbUserData['sex'] == 1 ){ echo '男性'; }elseif($dbUserData['sex'] == 2 ){ echo '女性'; }else{ echo '未設定';} ?></td>
        </tr>
        <tr>
          <th>生年月日</th>
          <td><?php if(!empty($dbUserData['birthday'])){ echo $dbUserData['birthday']; }else{ echo '未設定';} ?></td>
        </tr>
        <tr>
          <th>職業</th>
          <td><?php if(!empty($dbUserData['work'])){ echo $dbUserData['work']; }else{ echo '未設定';} ?></td>
        </tr>
        <tr>
          <th>コメント</th>
          <td><?php if(!empty($dbUserData['comment'])){ echo $dbUserData['comment']; }else{ echo '未設定';} ?></td>
        </tr>
      </tbody>
    </table>
<!--
      <div class="name">
        <div class="name-left prof-left"></div>
        <div class="name-right prof-right">名前</div>
      </div>
      <div class="sex">
        <div class="sex-left prof-left">性別</div>
        <div class="sex-right prof-right">男性</div>
      </div>
      <div class="work">
        <div class="work-left prof-left">生年月日</div>
        <div class="work-right prof-right">2000/11/22</div>
      </div>
      <div class="comment">
        <div class="comment-left prof-left"></div>
        <div class="comment-right prof-right"></div>
      </div>
-->
    </div>
  </div>

</div>
