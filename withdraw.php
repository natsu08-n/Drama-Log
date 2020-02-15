<?php
require('function.php');

debug('===========================================');
debug('退会ページ');
debug('===========================================');
debugLogStart();

require('auth.php');

if(!empty($_POST)){
    debug('POST送信があります');
    
    try {
        $dbh = dbConnect();

        $sql1 = 'UPDATE users SET delete_flg = 1 WHERE id = :us_id';
        $sql2 = 'UPDATE work SET delete_flg = 1 WHERE user_id = :us_id';
        
        $data = array(':us_id' => $_SESSION['user_id']);
        
        $stmt1 = queryPost($dbh, $sql1, $data);
        $stmt2 = queryPost($dbh, $sql2, $data);
        
        if($stmt1){
            $_SESSION = array();
            if(isset($_COOKIE[session_name()])){
                setcookie(session_name(), '', time()-42000, '/');
            }

            session_destroy();
            debug('セッション変数の中身：'.print_r($_SESSION, true));
            debug('トップページへ');
            header("Location:index.php");
        }else{
            debug('クエリが失敗');
            $err_msg['common'] = MSG7;
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG7;
    }
}
debug('画面表示処理終了++++++++++++++++++++++++++++++++');
?>

<?php
$siteTitle = '退会';
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
   <form action="" method="post" class="form-out">
   <div class="area-msg area-msg-common">
       <?php if(!empty($err_msg['common'])) echo $err_msg['common']
; ?>   </div>
    <h1>退会</h1>
       <h3>※ご注意</h3>
       <p>退会すると全てのデータが消去されます。</p>
       <p>本当に退会しますか？</p>
       <div class="btn">
         <input type="submit"  name="submit" class="btn-box btn-out" value="退会する">
      
      
  </div>
       
   </form>
</div>
</article>


<!--フッター-->
<?php
       require('footer.php');
       ?>