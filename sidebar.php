<?php

$profData = getUser($_SESSION['user_id']);
debug('サイドバー情報：'.print_r($profData,true));
?>
<section id="sidebar">
   <img src=" <?php echo (!empty($profData['pic'])) ? $profData['pic'] : 'img/user.jpeg'; ?>"  alt="ユーザープロフィール画像">
   <h3>Name : <?php echo (!empty($profData['username'])) ?  $profData['username'].'さん' : '名無しさん '; ?> </h3>
    <a href="workRegist.php">海外ドラマを登録する</a>
    <a href="profEdit.php">プロフィール編集</a>
    <a href="passChange.php">パスワード変更</a>
    <a href="withdraw.php">退会</a>
</section>