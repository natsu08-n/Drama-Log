<?php

require('function.php');

debug('===========================================');
debug('マイページ');
debug('===========================================');
debugLogStart();

require('auth.php');

//ユーザーデータ取得
$u_id = $_SESSION['user_id'];
//データベースから作品データ取得
$workData = getMyWorks($u_id);

//データベースからお気に入り情報データを取得
$likeData = getMyLike($u_id);

debug('取得した作品データ：'.print_r($workData,true));
debug('取得したお気に入りデータ：'.print_r($likeData,true));

 debug('画面表示処理終了++++++++++++++++++++++++++++++++');

?>


<?php
$siteTitle = 'マイページ';
require('head.php');
?>



   <body>
       <style>
           .panel-container {
               width: 810px;
               float: left;
           }

       </style>

<!--    ヘッダー-->
<?php
       require('header.php');
       ?>
      
<p id="js-show-msg" style="display:none;" class="msg-slide"><?php echo getSessionFlash('msg_success'); ?></p>


<!--メインコンテンツ-->
<div class="main">
    <section class="main-container">
        
<h1 class="page-title" style="font-size:28px;">MYPAGE</h1>
    
        
<!-- サイドバー -->
<?php
       require('sidebar.php');
       ?>
      
        
<div class="panel-container">
  <section class="panel-list ">
     <h1><i class="fas fa-caret-right fa-lg"></i>最近の投稿一覧</h1>
     
     <?php
      if(!empty($workData)):
      foreach($workData as $key => $val):
      ?>
      
      <a href="workRegist.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&w_id='.$val['id'] : '?w_id='.$val['id']; ?>"   class="panel-top">
          <div class="panel-pic">
              <img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt="<?php echo sanitize($val['title']); ?> " class="img-top">
          </div>
          <div class="panel-body">
              <p class="panel-title"><?php echo sanitize($val['title']); ?>
                  <span class="rating"><i class="fas fa-star icn-star"></i>評価：<?php echo sanitize(number_format($val['rating'],1)); ?> </span>
              </p>
          </div>
      </a>
      <?php
      endforeach;
      endif;
      ?>
      
       </section>
       
       
      <section class="panel-list ">
     <h1><i class="fas fa-caret-right fa-lg"></i>お気に入り一覧</h1>
     <?php
          if(!empty($likeData)):
          foreach($likeData as $ley => $val):
          ?>
      <a href="workDetail.php<?php echo
         (!empty(appendGetParam())) ?
        appendGetParam().'&w_id='.$val['id'] : '?w_id='.$val['id']; ?>" class="panel-top">
          <div class="panel-pic">
              <img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt="<?php echo sanitize($val['title']); ?>" class="img-top">
          </div>
          <div class="panel-body">
              <p class="panel-title">
                  <?php echo sanitize($val['title']); ?>
                  <span class="rating"><i class="fas fa-star icn-star"></i>評価：<?php echo sanitize(number_format($val['rating'],1)); ?> </span>
              </p>
          </div>
      </a>
      <?php
          endforeach;
          endif;
          ?>
    </section>
   
        </div>
    </section>
       </div>


<!--フッター-->
<?php
       require('footer.php');
       ?>
