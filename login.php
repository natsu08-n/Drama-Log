<?php 

//共通関数読み込み
require('function.php');
debug('===========================================');
debug('ログインページ');
debug('===========================================');
debugLogStart();

//ログイン認証
require('auth.php');

//post送信されていた場合
if(!empty($_POST)){
    debug('POST送信あり');
    
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_save = (!empty($_POST['pass_save'])) ? true : false;
    
//    バリデーション
    validRequired($email, 'email');
    validRequired($pass, 'pass');
    
    validEmail($email, 'email');
    validMaxLen($email, 'email');
    
    validMinLen($pass, 'pass');
    validHalf($pass, 'pass');
    validMaxLen($pass, 'pass');
    
    if(empty($err_msg)){
        debug('バリデーション確認OK');
        
        try {
            $dbh = dbConnect();
            
//            パスワードとIDを取ってくる条件として、Emailが合っているものを指定してあげる
            $sql = 'SELECT password, id FROM users WHERE email = :email AND delete_flg = 0';
            $data = array(':email' => $email);
            $stmt = queryPost($dbh, $sql, $data);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            debug('クエリ結果の中身：'.print_r($result, true));
            
            if(!empty($result) && password_verify($pass, array_shift($result))){
                debug('パスワード合致');
                
                $sesLimit = 60*60;
                $_SESSION['login_date'] = time();
                
                if($pass_save){
                    debug('ログイン保持にチェックあり');
                    $_SESSION['login_limit'] = $sesLimit * 24 * 30;
                }else{
                    debug('ログイン保持にチェックなし');
                    
                    $_SESSION['login_limit'] = $sesLimit;
                }
                $_SESSION['user_id'] = $result['id'];
                debug('セッション変数の中身：'.print_r($_SESSION,true));
                header("Location:mypage.php");
            }else{
                debug('パスワード相違');
                $err_msg['common'] = MSG9;
            }
        } catch (Exception $e) {
            error_log('エラー発生：'.$e->getMessage());
            $err_msg['common'] = MSG7;
        }
    }
}
debug('画面表示処理終了++++++++++++++++++++++++++++++++');

?>

<?php
$siteTitle = 'ログイン';
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

   <h1>ログイン</h1>
   
   <div class="form-edit">
       
       
    <div class="area-msg area-msg-common">
       <?php if(!empty($err_msg['common'])) echo $err_msg['common']; ?>
    </div>
     <div class="area-msg">
       <?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?>
    </div>
     <label class="<?php if(!empty($err_msg['email'])) echo 'err'; ?>">
             <input type="text" name="email" placeholder="Email" value="<?php if(!empty($_POST['email'])) echo $_POST['email']; ?>">
           </label>
           <div class="area-msg">
         <div class="area-msg">
       <?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?>
    </div>
    </div>
           <label class="<?php if(!empty($err_msg['pass'])) echo 'err'; ?>">
             <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass']; ?>">
           </label>
     <label for="">

         <input type="checkbox" name="pass_save"> 次回ログインを省略する
     </label>

     <div class="btn">
         <input type="submit" class="btn-box" value="Login!">


  </div>
   </div>

 </form>

</div>
    
</article>


<!--フッター-->
<?php
       require('footer.php');
       ?>