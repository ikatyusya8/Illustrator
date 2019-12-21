<header id="header">
  <div class="header-top">
    <a href="index.php" class="logo">illustrator</a>
    <form action="index.php" method=get class="search">
      <input type="text" name="search-word" class="s-text" placeholder="キーワード、またはタグを入力してください" value="<?php if(!empty($_GET['search-word'])) echo sanitize($_GET['search-word']); ?>">
      <input type="submit" value="🔍" class="s-submit">
    </form>
  </div>
  
  <?php if(!empty($_SESSION['login_date'])){
  echo('<div class="header-bottom">
  <ul class="menu">
    <div class="wrap">
      <li><a class="menu-botton" href="postPicture.php">イラスト投稿</a></li>
      <li><a class="menu-botton" href="mypage.php">マイページ</a></li>
      <li class="menu-single">
        <a href="#" class="init-bottom menu-botton">設定</a>
        <ul class="menu-second-level">
          <li><a class="botton-list" href="profEdit.php">プロフィール設定</a></li>
          <li><a class="botton-list" href="passEdit.php">パスワード変更</a></li>
          <li><a class="botton-list" href="logout.php">ログアウト</a></li>
          <li><a class="botton-list" href="withdraw.php">退会</a></li>
        </ul>
      </li>
    </div>
  </ul>
</div>');
  }else{
  echo('<div class="header-bottom">
    <ul class="menu">
      <div class="wrap">
        <li><a href="login.php" class="login-botton menu-botton-logout">ログイン</a></li>
        <li><a href="signup.php" class="sign-up-botton menu-botton-logout">新規会員登録</a></li>
      </div>
    </ul>
  </div>');
} ?>

  

</header>
