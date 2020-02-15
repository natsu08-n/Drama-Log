<?php
require('function.php');

debug('===========================================');
debug('プロフィール編集ページ');
debug('===========================================');
debugLogStart();

require('auth.php');

$dbFormData = getUser($_SESSION['user_id']);

debug('取得したユーザー情報：'.print_r($dbFormData,true));


if(!empty($_POST)){
    debug('POST送信あり');
    debug('POST情報：'.print_r($_POST, true));
    debug('FILE情報：'.print_r($_FILES, true));
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $intro = $_POST['introduction'];
    
//    画像がある時、自作関数uploadImgで画像をアップロードし,パスを格納。
//    画像がない時は$picは初期化
    $pic = (!empty($_FILES['pic']['name'])) ? uploadImg($_FILES['pic'], 'pic') : '';
    
    $pic = (empty($pic) && !empty($dbFormData['pic'])) ? $dbFormData['pic'] : $pic;
    
    if($dbFormData['username'] !== $username){
        validMaxLen($username, 'username');
    }
    
    if($dbFormData['email'] !== $email){
        validMaxLen($email, 'email');
        if(empty($err_msg['email'])){
            validEmailDup($email);
        }
        validEmail($email, 'email');
        validRequired($email, 'email');
    }
    
    if(empty($err_msg)){
        debug('プロフィール編集バリデーションOK');
        
        try {
            $dbh = dbConnect();
            $sql = 'UPDATE users SET pic = :pic, username = :u_name, email = :email, introduction = :intro WHERE id = :u_id';
            $data = array(':pic' => $pic, ':u_name' => $username, ':email' => $email, ':intro' => $intro, ':u_id' => $dbFormData['id']);
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                $_SESSION['msg_success'] = SUC02;
                debug('プロフィール編集からマイページへ');
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
$siteTitle = 'プロフィール変更';
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
<h1 class="page-title">プロフィール変更</h1>
 <form action="" method="post" enctype="multipart/form-data" class="form form-input">
   <div class="form-edit">
   
    <div class="area-msg area-msg-common">
       <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <div class="area-msg">
       <?php if(!empty($err_msg['pic'])) echo $err_msg['pic']; ?>
    </div>
   <label class="area-drop <?php if(!empty($err_msg['pic'])) echo 'err'; ?>" style="height:200px; line-height:200px;">
       <input type="hidden" name="MAX_FILE_SIZE" value="3145728">
       <input type="file" name="pic" class="input-file" style="height:200px;" >
    <img src="<?php echo getFormData('pic'); ?>"  alt="" class="prev-img" style="<?php if(empty(getFormData('pic'))) echo 'display:none;' ?>">
       ドラッグ＆ドロップ
   </label>
 
    <div class="area-msg">
      <?php if(!empty($err_msg['username'])) echo $err_msg['username'] ?>
    </div>
     <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">ユーザー名
             <input type="text" name="username" value="<?php echo getFormData('username'); ?>">
           </label>
           
            <div class="area-msg">
       <?php if(!empty($err_msg['email'])) echo $err_msg['email'] ?>
    </div>
           <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
            メールアドレス
             <input type="text" name="email" value="<?php echo getFormData('email'); ?>">
           </label>
           
            <div class="area-msg">
       <?php if(!empty($err_msg['introduction'])) echo $err_msg['introduction'] ?>
    </div>
           <label class="<?php if(!empty($err_msg['introduction'])) echo 'err'; ?>">
            自己紹介
             <textarea name="introduction" class="js-count1" cols="30" rows="10"><?php echo getFormData('introduction'); ?></textarea>
           </label>
            <p class="counter-text" style="text-align:right; color:dimgray; padding-right:15px; box-sizing:border-box; "><span class="js-count-view1">0</span>/500文字</p>
     <div class="btn">
         <input type="submit" class="btn-box" value="変更する">
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
