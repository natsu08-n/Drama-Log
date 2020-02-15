
<?php
require('function.php');

debug('===========================================');
debug('商品登録/編集ページ');
debug('===========================================');
debugLogStart();

require('auth.php');

$w_id = (!empty($_GET['w_id'])) ? $_GET['w_id'] : '';

$dbFormData = (!empty($w_id)) ? getWork($_SESSION['user_id'], $w_id) : '';
debug('$dbFormDataの中身:'.print_r($dbFormData,true));

//新規登録ならfalse,登録済みの編集ならtrue
$edit_flg = (empty($dbFormData)) ? false : true;

$dbCategoryData = getCategory();
debug('作品ID：'.$w_id);
debug('フォーム用DBデータ：'.print_r($dbFormData,true));
debug('カテゴリーデータ：'.print_r($dbCategoryData,true));

if(!empty($w_id) && empty($dbFormData)){
    debug('GETパラメータの商品ID相違のため、マイページへ遷移');
    header("Location:mypage.php");
}

if(!empty($_POST)){
    debug('POST送信あり');
    debug('POST情報：'.print_r($_POST, true));
    debug('FILE情報：'.print_r($_FILES, true));
    
    $title = $_POST['title'];
    $category = $_POST['category_id'];
    $season = $_POST['season'];
    $rating = $_POST['rating'];
    $episode = $_POST['episode'];
    $comment = $_POST['comment'];

    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
    
//    登録済み内容編集時
    $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;
    
//    新規登録時のバリデーション
    if(empty($dbFormData)){
        validRequired($title, 'title');
        validMaxLen($title, 'title');
        validSelect($category, 'category_id');
        validRequired($rating, 'rating');
        validMaxLen($episode, 'episode', 500);
        validMaxLen($comment, 'comment', 500);
    }else{
        //    登録済み内容編集時バリデーション
    if($dbFormData['title'] !== $title){
        validRequired($title, 'title');
        validMaxLen($title, 'title');
    }
    if($dbFormData['category_id'] !== $category){
        validSelect($category, 'category_id');
    }

    if($dbFormData['rating'] !== $rating){
       validRequired($rating, 'rating');
    }
    if($dbFormData['episode'] !== $episode){
       validMaxLen($episode, 'episode', 500);
    }
    if($dbFormData['comment'] !== $comment){
       validMaxLen($comment, 'comment', 500);
    }
}
    
    if(empty($err_msg)){
        debug('商品登録/編集バリデーションOK');
        
        try {
            $dbh = dbConnect();
            
            if($edit_flg){
                debug('DB更新');
                $sql = 'UPDATE work SET title = :title, category_id = :category, season = :season, rating = :rating, episode = :episode,comment = :comment, pic = :pic WHERE user_id = :u_id AND id = :w_id';
                $data = array(':title' => $title, ':category' => $category, ':season' => $season, ':rating' => $rating, ':episode' => $episode, ':comment' => $comment, ':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':w_id' => $w_id);
            }else{
                debug('DB新規登録');
                $sql = 'INSERT INTO work (title, category_id, season, rating, episode, comment, pic, user_id, create_date) VALUES (:title, :category, :season, :rating, :episode, :comment, :pic, :u_id, :date)';
                $data = array(':title' => $title, ':category' => $category, ':season' => $season, ':rating' => $rating, ':episode' => $episode, ':comment' => $comment, ':pic' => $pic, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
            }
            
//            上記更新・（削除）・新規登録について
            debug('SQL:'.$sql);
            debug('流し込みデータ：'.print_r($data, true));
            
            $stmt = queryPost($dbh, $sql, $data);
            if($stmt){
                $_SESSION['msg_success'] = SUC04;
                debug('マイページへ遷移');
                header("Location:mypage.php");
            }
         
        } catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG7;
        }

    }

}
debug('画面表示処理終了++++++++++++++++++++++++++++++++');

?>


<?php
$siteTitle = '作品登録/編集';
require('head.php');
?>
   
   <body>
       
       
<!--    ヘッダー-->
<?php
       require('header.php');
       ?>
      
      
<!--メインコンテンツ-->
<div class="main">

<section class="main-container">
<h1 class="page-title"><?php echo (!$edit_flg) ? '作品登録': '作品編集'; ?> </h1>
 <form action="" method="post" class="form form-input" enctype="multipart/form-data">
   
   <div class="form-edit">
       
       
       
    <div class="area-msg">
       <?php if(!empty($err_msg['common'])) echo $err_msg['common'] ?>
    </div>
     
<!--     ドラマタイトル箇所-->
      <div class="area-msg">
       <?php if(!empty($err_msg['title'])) echo $err_msg['title'] ?>
    </div>
     <label class="<?php if(!empty($err_msg['title'])) echo 'err'; ?>">ドラマタイトル <span class="label-require">必須</span>
             <input type="text" name="title" value="<?php echo getFormData('title'); ?>">
           </label>
           
<!--           カテゴリー箇所-->
           <div class="area-msg">
       <?php if(!empty($err_msg['category_id'])) echo $err_msg['category_id'] ?>
    </div>
        
         <label class="<?php if(!empty($err_msg['category_id'])) echo 'err'; ?>">
              カテゴリ<span class="label-require">必須</span>
              <select name="category_id" id="">
                <option value="0" <?php if(getFormData('category_id') == 0 ){ echo 'selected'; } ?> >選択してください</option>
                <?php
                  foreach($dbCategoryData as $key => $val){
                ?>
                  <option value="<?php echo $val['id'] ?>" <?php if(getFormData('category_id') == $val['id'] ){ echo 'selected'; } ?> >
                    <?php echo $val['name']; ?>
                  </option>
                <?php
                  }
                ?>
              </select>
            </label>
              
<!--              シーズン箇所-->
           <div class="area-msg">
       <?php if(!empty($err_msg['season'])) echo $err_msg['season'] ?>
    </div>
           <label class="<?php if(!empty($err_msg['season'])) echo 'err'; ?>">
            シーズンどこまで観た？
            <input type="text" name="season" value="<?php echo getFormData('season'); ?>">
           </label>
           
<!--             評価箇所-->
           <div class="area-msg">
        <?php if(!empty($err_msg['rating'])) echo $err_msg['rating'] ?>
    </div>
            <label class="<?php if(!empty($err_msg['rating'])) echo 'err'; ?>">評価（
             <span><i class="fas fa-star icn-star"></i></span> ５段階で）
            <span class="label-require">必須</span>
            <input type="text" name="rating" value="<?php echo getFormData('rating'); ?>">
           </label>
             
<!--             あらすじ箇所-->
              <div class="area-msg">
       <?php if(!empty($err_msg['episode'])) echo $err_msg['episode'] ?>
    </div>
            <label class="<?php if(!empty($err_msg['episode'])) echo 'err'; ?>">あらすじ
           <textarea name="episode" class="js-count1" cols="30" rows="10"><?php echo getFormData('episode'); ?></textarea>
           </label>
           <p class="counter-text" style="text-align:right; color:dimgray; padding-right:15px; box-sizing:border-box; "><span class="js-count-view1">0</span>/500文字</p>
           
           <!--             感想箇所-->
              <div class="area-msg">
       <?php if(!empty($err_msg['comment'])) echo $err_msg['comment'] ?>
    </div>
           <label class="<?php if(!empty($err_msg['comment'])) echo 'err'; ?>">感想
           <textarea name="comment" class="js-count2" cols="30" rows="10"><?php echo getFormData('comment'); ?></textarea>
           </label>
               <p class="counter-text" style="text-align:right; color:dimgray; padding-right:15px; box-sizing:border-box; "><span class="js-count-view2">0</span>/500文字</p>
               
<!--              画像箇所-->
               <div class="area-msg">
       <?php if(!empty($err_msg['pic'])) echo $err_msg['pic'] ?>
    </div>
             <div class="img-container">
                 画像
              <label class=" area-drop-work <?php if(!empty($err_msg['pic'])) echo 'err'; ?>" style="height:250px; ">
                <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
                <input type="file" name="pic" class="input-file-work" style="height:250px">
               <img class="prev-img-work" src="<?php echo getFormData('pic'); ?>" alt="" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
                  ドラッグ＆ドロップ
              </label>
             </div>

     <div class="btn">
         <input type="submit" class="btn-box" value="<?php echo (!$edit_flg) ? '登録する' : '更新する'; ?> ">
  </div>
   </div>
 </form>

<!-- サイドバー -->
<?php
       require('sidebar.php');
       ?>

</section>


</div>

<!--フッター-->
<?php
       require('footer.php');
       ?>