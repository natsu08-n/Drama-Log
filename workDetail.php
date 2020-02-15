<?php
require('function.php');
require('auth.php');
debug('===========================================');
debug('作品詳細ページ');
debug('===========================================');
debugLogStart();

//作品IDのGETパラメータを取得
$w_id = (!empty($_GET['w_id'])) ? $_GET['w_id'] : '';
//データベースから商品データを取得
$viewData = getWorkOne($w_id);

$u_id = '';
$postUserData = getUserOne($u_id);
debug('投稿者情報：'.print_r($postUserData,true));

//パラメータに不正な値が入っているかチェック
if(empty($viewData)){
    error_log('エラー発生：指定ページに不正な値あり');
    header("Location:index.php");
}
debug('取得したデータベースデータ：'.print_r($viewData, true));


debug('画面表示処理終了++++++++++++++++++++++++++++++++');

?>


<?php
$siteTitle = '作品詳細';
require('head.php');
?>


<body>

<style>
    .work-detail-top {
        overflow: hidden;
        min-height: 450px;
        width: 1060px;
    }
    .work-box {
        float: left;
        margin-left: 20px;
        height: 500px;
        box-sizing: border-box;
    }
    
    .main-container h1 {
        font-size: 28px;
        margin-left: 20px;
        margin-bottom: 30px;
        color: #cc66ff;
    }
    
     .work-left {
        float: left;
         border: 5px solid white;
    }
    
    .work-left img {
        width: 300px;
        height: 460px;
        margin: 20px;
    }
    
    .work-episode  {
        min-height: 200px;
        width: 30%;
        background-color: rgba(255, 255, 255, 0.4);
        border-radius: 5px;
    }
    
    .work-comment  {
        min-height: 300px;
        width: 100%;
        box-sizing: border-box;
        margin: 20px 0 0 10px;
        background-color: rgba(255, 255, 255, 0.4);
        border-radius: 5px;


    }
    .work-episode p, .work-comment p {
/*        break-wordは可能な限り禁則処理が施される*/
        overflow-wrap: break-word;
        word-wrap: break-word;
        padding: 10px;
        box-sizing: border-box;
        margin: 0 auto;
        width: 95%;
        

    }
    
    .work-episode h3, .work-comment h3 {
        margin: 5px 0 10px 0;
        padding: 10px;
        box-sizing: border-box;
    }
    .work-right{
        margin-top: 15px;
        font-size: 18px;
        width: 30%;
    }
    .work-right-contents {
     margin-bottom: 40px;
    font-size: 18px;
    font-weight: bold;
    }
    
/*    お気に入りアイコン*/
    .icn-like{
        float: left;
        color: dimgray;
        margin-left: 15px;
    }
    .icn-like:hover{
        cursor: pointer;
    }
    .icn-like.active{
        float: left;
        color: #990000;
        margin-left: 15px;
    }
    
    </style>

<!--    ヘッダー-->
<?php
       require('header.php');
       ?>
      
      
<!--メインコンテンツ-->
<div class="main ">
    <section class="main-container">
        <div class="work-detail-top">
        <h1><?php echo sanitize($viewData['title']); ?></h1>
        
        <div class="work-left work-box">
<!--           javaScript追加するならimgタグIDに-->
            <img src="<?php echo showImg(sanitize($viewData['pic'])); ?> " alt="<?php echo sanitize($viewData['title']); ?>">
        </div>
           <div class="work-episode work-box">
               <h3>あらすじ</h3>
               
               <p><?php echo sanitize($viewData['episode']); ?></p>
           </div>
           
           <div class="work-right work-box">
           
            <div class="work-right-contents">
            <i class="fas fa-heart fa-lg icn-like js-click-like <?php if(isLike($_SESSION['user_id'], $viewData['id'])){ echo 'active'; } ?>" area-hidden="true" data-workid="<?php echo sanitize($viewData['id']); ?>"></i>
               
           </div>
             <div class="work-right-contents">
               カテゴリー : <span style="color:white; font-weight:bold; margin-left:10px; font-size:20px; background-color: #cc66ff; padding:5px; box-sizing:border-box; border-radius: 5px;"><?php echo sanitize($viewData['category']); ?> </span>
            
              </div>
              <div class="work-right-contents">
               シーズンどこまで観た？ :<span style="color: #cc66ff; font-weight:bold; margin-left:10px; font-size:20px; background-color: rgba(255, 255, 255, 0.4); width: 35px; padding:5px; box-sizing:border-box; border-radius: 5px; "><?php echo sanitize($viewData['season']); ?></span>まで
              </div>
               <div class="work-right-contents">
               <i class="fas fa-star icn-star"></i>評価（５段階で）  :<span style="color:#cc66ff; font-weight:bold; margin-left:10px; font-size:20px; background-color: rgba(255, 255, 255, 0.4); padding:5px; box-sizing:border-box; border-radius: 5px;"><?php echo sanitize($viewData['rating']); ?></span>
              </div>
           </div>
            
        </div>
        
        <div class="work-detail-bottom work-comment">
            <h3>感想</h3>
               <p><?php echo sanitize($viewData['comment']); ?></p>

        </div>

         <div class="back">
            <a href="index.php<?php echo appendGetParam(array('w_id')); ?> ">◁ 作品一覧に戻る</a>
          </div>
    </section>
</div>


<!--フッター-->
<?php
       require('footer.php');
       ?>