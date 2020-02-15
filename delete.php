<?php
require('function.php');

debug('===========================================');
debug('削除ページ');
debug('===========================================');
debugLogStart();

require('auth.php');

$workData = getMyWorks($_SESSION['user_id']);

debug('取得した作品情報：'.print_r($workData,true));

$w_id = (!empty($_GET['w_id'])) ? $_GET['w_id'] : '';

 debug('POST情報：'.print_r($_POST, true));
 debug('削除ページでのユーザーID:'.print_r($_SESSION['user_id'], true));


if(!empty($_POST)){
    debug('POST送信があります');

    try {
        $dbh = dbConnect();
         $sql = 'UPDATE work SET delete_flg = 1 WHERE id = :w_id';
//        $sql = 'DELETE FROM work WHERE id = :w_id';
        $data = array(':w_id' => $w_id);

        $stmt = queryPost($dbh, $sql, $data);
        if($stmt){
            debug('削除確認：'.print_r($stmt, true));
            debug('作品削除したのでマイページへ');
            header("Location:mypage.php");
           return true;
        }else{
            debug('クエリが失敗');
            $err_msg['common'] = MSG7;
           return false;
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG7;
    }
    }

debug('画面表示処理終了++++++++++++++++++++++++++++++++');
?>

<?php
$siteTitle = '削除';
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
    <h1>削除</h1>
       <h3>※ご注意</h3>
       <p>削除すると全てのデータが消去されます。</p>
       <p>本当に削除しますか？</p>
       <div class="btn">
         <input type="submit"  name="submit" class="btn-box btn-out" value="削除する">

  </div>

   </form>
</div>
</article>


<!--フッター-->
<?php
       require('footer.php');
       ?>
