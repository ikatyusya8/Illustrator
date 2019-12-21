<?php 
$siteTitle = 'イラストレーター　メール詳細画面';
require('head.php');
?>


<?php 
$siteTitle = 'イラストレーター　メール一覧画面';
require('head.php');
?>

<body>
  <div class="mail-detail">
    <div class="container">
      <div class="head-wrap">
        <div class="send-user-name">相手の名前</div>
        <a href="mailList.php">メール一覧に戻る</a>
      </div>
      <div class="send-comment">
        <div class="triangle"></div>
        テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト
      </div>
      <div class="receive-wrap">
        <div class="receive-comment">
          <div class="triangle2"></div>
          テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト
        </div>
        <div class="receive-icon"><img src="img/1385069.jpg" alt=""></div>
      </div>
      <div class="send-comment">
        <div class="triangle"></div>
        テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト
      </div>
      <div class="receive-wrap">
        <div class="receive-comment">
          <div class="triangle2"></div>
          テキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキストテキスト
        </div>
        <div class="receive-icon"><img src="img/1385069.jpg" alt=""></div>
      </div>

    </div>
    <form action="" class="input-mail">
      <textarea name="mail" rows="4"></textarea>
      <input type="submit">
    </form>

  </div>

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</body>

</html>
