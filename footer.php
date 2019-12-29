<footer id="footer">
  <div class="copyright">Copyright タカヨシ. All Rights Reserved.</div>
</footer>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
  
  $(function(){
    
    //フッター位置固定
    var $ftr = $('#footer');
    if( window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
      $ftr.attr({'style': 'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;' });
    }
    
    //ボタンクリック時の動作
    $('.reply-label').on('click',function () {
      $('.hidden-box').show();
      $('.hidden-box').css('display', 'flex');
    });
    
    $('.mail-botton').on('click', function() {
      window.open('mailList.php', null, 'left=50%,width=810,height=720');
    });
    
    $('.dm').on('click', function() {
      window.open('mailDetail.php', null, 'left=50%,width=840,height=720');
    });
    
    $('.user-detail').on('click', function() {
      window.open('profUser.php?u_id=<?php if(!empty($dbContributionData['user_id'])){ echo sanitize($dbContributionData['user_id']); } ?>', null, 'left=50%,width=840,height=720');
    });
    
    $('.profile .user-name-link').on('click', function() {
      window.open('profUser.php?u_id=<?php if(!empty($dbContributionData['user_id'])){ echo sanitize($dbContributionData['user_id']); } ?>', null, 'left=50%,width=840,height=720');
    });
    
    //画像ライブプレビュー
    //画像アップロードのラベルタグを取得
    var $myImg = $('.area-drop');
    //画像ファイルを取得
    var $fileInput = $('.file-input');
    $myImg.on('dragover', function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', '3px #808080 dashed');
    });
    $myImg.on('dragleave', function(e){
      e.stopPropagation();
      e.preventDefault();
      $(this).css('border', 'none');
    });
    $fileInput.on('change', function(e){
      $myImg.css('border', 'none');
      var file = this.files[0], //選択したファイル情報を取得
          $img = $(this).siblings('.prev-img'), //siblingメソッドから兄弟のimgを取得する
          fileReader = new FileReader(); //fileを読み込むFileReaderオブジェクト生成
      
      fileReader.onload = function(event) {
        // 読み込んだデータをimgに設定
        // event.target.resultは画像ファイルのURI
        $img.attr('src', event.target.result).show();
      };
      
      // 画像読み込み
      fileReader.readAsDataURL(file);
      
    });
    
    //メッセージを表示
    var $jsShowMsg = $('#js-show-msg');
    var msg = $jsShowMsg.text();
    if(msg.replace(/^[\s　]+|[\s　]+$/g, "").length){
      $jsShowMsg.slideToggle('slow');
      setTimeout(function(){ $jsShowMsg.fadeOut('normal'); }, 5000);
    }
    
    //文字数カウント
    $('.title > #js-count').keyup(function(){
      var counter = $(this).val().length;
      $('.title > .count').text(counter);
    });
    $('.comment > #js-count').keyup(function(){
      var counter = $(this).val().length;
      $('.comment > .count').text(counter);
    });
    
    //ブックマーク登録・削除
    var $bookmarkclick,
        bookmarkIllustId,
        $bookmarkspan;
    $bookmarkspan = $('.bookmark-span');
    $bookmarkclick = $('.js-bookmark-click') || null;
    bookmarkIllustId = $bookmarkclick.data('illustid') || null;
    if(bookmarkIllustId !== null ){
      $bookmarkclick.on('click',function(){
        var $this = $(this);
        $.ajax({
          type: "POST", //使用するHTTPメソッド
          url: "ajaxBookmark.php", //通信先のURL
          data: { illustId : bookmarkIllustId} //送信するデータ
        }).done(function( data ){
          $this.toggleClass('active');
          if($this.hasClass('active')){
            $bookmarkspan.text('ブックマーク済み ');
          }else{
            $bookmarkspan.text('ブックマーク ');
          }
          
          
          console.log('Ajax Success');
        }).fail(function( msg ) {
          console.log('Ajax Error');
        });
      });
    }

  });
  
</script>

</body>
</html>
