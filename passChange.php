<?php
require('function.php');

debug('===========================================');
debug('パスワード変更');
debug('===========================================');
debugLogStart();

require('auth.php');


$dbFormData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($dbFormData, true));

$userData = getUser($_SESSION['user_id']);
debug('取得したユーザー情報：'.print_r($userData, true));

if(!empty($_POST)){
    debug(' POST送信あり');
    debug(' POST情報：'.print_r($_POST, true));

    $pass_old = $_POST['pass_old'];
    $pass_new = $_POST['pass_new'];
    $pass_new_re = $_POST['pass_new_re'];

    validRequired($pass_old, 'pass_old');
    validRequired($pass_new, 'pass_new');
    validRequired($pass_new_re, 'pass_new_re');
    
    if(empty($err_msg)){
        debug('未入力チェックOK');

        validPass($pass_old, 'pass_old');
        validPass($pass_new, 'pass_new');

        if(!password_verify($pass_old, $userData['password'])){
            $err_msg['pass_old'] = MSG10;
        }

        if($pass_old === $pass_new){
            $err_msg['pass_new'] = MSG11;
        }

        validMatch($pass_new, $pass_new_re, 'pass_new_re');

        if(empty($err_msg)){
            debug('バリデーションOK');

        try {
            $dbh = dbConnect();

            $sql = 'UPDATE users SET password = :pass WHERE id = :id';
            $data = array(':id' => $_SESSION['user_id'], ':pass' => password_hash($pass_new, PASSWORD_DEFAULT));
            $stmt = queryPost($dbh, $sql, $data);

            if($stmt){
                $_SESSION['msg_success'] = SUC01;
                header("Location:mypage.php");
            }

        } catch (Exception $e) {
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG7;
            }
        }
    }
}


?>


<?php
$siteTitle = 'パスワード変更';
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
<h1 class="page-title">パスワード変更</h1>
 <form action="" method="post" class="form form-input">
   <h3>ログインパスワードを変更します。</h3>
   
   <div class="form-edit">
       
    <div class="area-msg">
    <?php echo getErrMsg('common'); ?>
    </div>
    <div class="area-msg">
    <?php echo getErrMsg('pass_old'); ?>
    </div>
     <label>旧パスワード
             <input type="password" name="pass_old" value="<?php echo getFormData('pass_old'); ?>">
           </label>
           <div class="area-msg">
    <?php echo getErrMsg('pass_new'); ?>
    </div>
           <label>
            新パスワード
             <input type="password" name="pass_new" value="<?php echo getFormData('pass_new'); ?>">
           </label>
            <div class="area-msg">
     <?php echo getErrMsg('pass_new_re'); ?>
    </div>
           <label>
            新パスワード（再入力）
             <input type="password" name="pass_new_re" value="<?php echo getFormData('pass_new_re'); ?>">
           </label>
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