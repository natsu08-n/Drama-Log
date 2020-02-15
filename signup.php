<?php
//共通関数読み込み
require('function.php');
debug('===========================================');
debug('新規登録ページ');
debug('===========================================');
debugLogStart();


       if(!empty($_POST)){
           $username = $_POST['username'];
           $email = $_POST['email'];
           $pass = $_POST['pass'];
           $pass_re = $_POST['pass_re'];
           
//           全項目入力チェック
           validRequired($username, 'username');
           validRequired($email, 'email');
           validRequired($pass, 'pass');
           validRequired($pass_re, 'pass_re');
           
           if(empty($err_msg)){
               
//               各種チェック（ユーザー名）
               validMaxLen($username, 'username');
               
//               各種チェック（Email）
               validEmail($email, 'email');
               validMaxLen($email, 'email');
               validEmailDup($email);
               
//               各種チェック（パスワード）
               validHalf($pass, 'pass');
               validMaxLen($pass, 'pass');
               validMinLen($pass, 'pass');
               
//               各種チェック（パスワード再入力）
               validMaxLen($pass_re, 'pass_re');
               validMinLen($pass_re, 'pass_re');
               
               if(empty($err_msg)){
                   validMatch($pass, $pass_re, 'pass_re');
                   
                   if(empty($err_msg)){
                       try {
                           $dbh = dbConnect();
                           $sql = 'INSERT INTO users (username, email, password, login_time, create_date) VALUES (:username, :email, :pass, :login_time, :create_date)';
                           
                           $data = array(':username' => $username, ':email' => $email, ':pass' => password_hash($pass, PASSWORD_DEFAULT), ':login_time' => date('Y-m-d H:i:s'), 'create_date' => date('Y-m-d H:i:s'));
                           
                           $stmt = queryPost($dbh, $sql, $data);
                         
                           if($stmt){
                               $sesLimit = 60*60;
                               $_SESSION['login_date'] = time();
                               $_SESSION['login_limit'] = $sesLimit;
                               $_SESSION['user_id'] = $dbh->LastInsertId();
                               
                                debug('セッション変数の中身：'.print_r($_SESSION,true));
                    header("Location:mypage.php");//マイページへ
                               
                           }

                       } catch (Exception $e) {
                           error_log('エラー発生：'.$e->getMessage());
                           $err_msg['common'] = MSG7;
                       }
                   }
               }
           }
       }

?>

<?php
$siteTitle = '新規登録';
require('head.php');
?>
   
   <body>
       
       
<!--    ヘッダー-->
<?php
       require('header.php');
       ?>
      
      
<!--メインコンテンツ-->
<article class="main">
<div class="form-container">
 <form action="" method="post" class="form">

   <h1>新規登録</h1>
   <div class="form-edit">
       
    <div class="area-msg area-msg-common">
       <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
    <div class="area-msg">
       <?php if(!empty($err_msg['username'])) echo $err_msg['username']; ?>
    </div>
     <label class="<?php if(!empty($err_msg['username'])) echo 'err'; ?>">
             <input type="text" name="username" placeholder="ユーザー名"  value="<?php if(!empty($_POST['username'])) echo $_POST['username'] ?>">
           </label>
           
           <div class="area-msg">
         <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
    </div>
           <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
             <input type="text" name="email" placeholder="Email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'] ?>">
           </label>
           
           <div class="area-msg">
        <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
    </div>
           <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>"><p style="font-size:14px; text-align:left;">※6文字以上の半角英数字で入力してください。</p>
             <input type="password" name="pass" placeholder="パスワード"  value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'] ?>">
           </label>
           <div class="area-msg">
         <?php if(!empty($err_msg['pass_re'])) echo $err_msg['pass_re']; ?>
    </div>
           <label class="<?php if(!empty($err_msg['pass_re'])) echo 'err'; ?>">
             <input type="password" name="pass_re" placeholder="パスワード(再入力)"  value="<?php if(!empty($_POST['pass_re'])) echo $_POST['pass_re'] ?>">
           </label>
     <div class="btn">
         <input type="submit" class="btn-box" value="登録する">

  </div>
   </div>
 </form>

</div>
</article>


<!--フッター-->
<?php
       require('footer.php');
       ?>
