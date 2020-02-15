<!--フッター-->
<footer id="footer">
    ©️DramaLog.All Rights Reserved.
</footer>

<script src="js/jquery-3.4.1.min.js"></script>
<script>
    
$(function(){
    
//    フッターを最下部に固定する
    var $ftr = $('#footer');
    if(window.innerHeight > $ftr.offset().top + $ftr.outerHeight() ){
       $ftr.attr({'style':'position:fixed; top:' + (window.innerHeight - $ftr.outerHeight()) +'px;'});
       }

//    メッセージ表示
    var $jsShowMsg = $('#js-show-msg');
    
    var msg = $jsShowMsg.text();
    
    if(msg.replace(/^[\s]+[\s]+/g, "").length){
        $jsShowMsg.slideToggle('slow');
        setTimeout(function(){
            $jsShowMsg.slideToggle('slow');},5000);
    }
//    画像ライブプレビュー
    var $dropArea = $('.area-drop,.area-drop-work');
    var $fileInput = $('.input-file,.input-file-work');
    $dropArea.on('dragover', function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border','3px #ccc dashed');
    });
    $dropArea.on('dragleave', function(e){
        e.stopPropagation();
        e.preventDefault();
        $(this).css('border', 'none');
    });
    
    $fileInput.on('change', function(e){
        $dropArea.css('border', 'none');
        var file = this.files[0],
            $img = $(this).siblings('.prev-img,.prev-img-work'),
            fileReader = new FileReader();
        
        fileReader.onload = function(event){
            $img.attr('src', event.target.result).show();
        };
        fileReader.readAsDataURL(file);
    });
    
//    テキストエリアカウントそれぞれ表示
    $(function(){
        $('.js-count1,.js-count2').bind('keyup',function(){
             for (num=1; num<=2; num++){
            var thisValueLength = $(".js-count" + num).val().replace(/\s+/g,'').length;
            $(".js-count-view"+num).html(thisValueLength);
        }
                                         });
    });

//    お気に入り登録・削除
     var $like,
            likeworkId;
        $like = $('.js-click-like') || null; //nullというのはnull値という値で、「変数の中身は空ですよ」と明示するためにつかう値
        likeworkId = $like.data('workid') || null;
        // 数値の0はfalseと判定されてしまう。work_idが0の場合もありえるので、0もtrueとする場合にはundefinedとnullを判定する
        if(likeworkId !== undefined && likeworkId !== null){
          $like.on('click',function(){
            // ここでのthisは$likeのこと
            var $this = $(this);
            $.ajax({
              type:"POST",
              url:"ajaxLike.php",
              data:{workId:likeworkId}
            }).done(function(data){
              // ※普通は以下のconsole.logは表示させない、今回はわかりやすくテストで表示
              console.log('Ajax Success');
              // クラス属性をtoggleでつけ外しする
              $this.toggleClass('active');
            }).fail(function(msg){
              console.log('Ajax Error');
            });
          });
        }
    

});
</script>
</body>

</html>