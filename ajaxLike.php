
<?php

require('function.php');

debug('===========================================');
debug('Ajax');
debug('===========================================');
debugLogStart();

if(isset($_POST['workId']) && isset($_SESSION['user_id']) && isLogin()){
    debug('POST送信あり');
    $w_id = $_POST['workId'];
    debug('作品ID：'.$w_id);
    
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM `like` WHERE work_id = :w_id AND user_id = :u_id';
        $data = array(':w_id' => $w_id, ':u_id' => $_SESSION['user_id']);
        $stmt = queryPost($dbh, $sql, $data);
        $resultCount = $stmt->rowCount();
        debug($resultCount);
        
        if(!empty($resultCount)){
            $sql = 'DELETE FROM `like` WHERE work_id = :w_id AND user_id = :u_id';
            $data = array(':w_id' => $w_id, ':u_id' => $_SESSION['user_id']);
            $stmt = queryPost($dbh, $sql, $data);
        }else{
            $sql = 'INSERT INTO `like` (work_id, user_id, create_date) VALUES (:w_id, :u_id, :date)';
            $data = array(':w_id' => $w_id, ':u_id' => $_SESSION['user_id'], ':date' => date('Y-m-d H:i:s'));
            $stmt = queryPost($dbh, $sql, $data);
        }
    } catch  (Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
    }

}

debug('Ajax処理終了++++++++++++++++++++++++++++++++');
?>