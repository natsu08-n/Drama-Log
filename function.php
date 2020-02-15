<?php

ini_set('log_errors','off');
ini_set('error_log','php.log');

//++++++++++++++++
//デバッグ処理
//++++++++++++++++
$debug_flg = true;

function debug($str){
    global $debug_flg;
    if(!empty($debug_flg)){
        error_log('デバッグ：'.$str);
    }
}
//++++++++++++++++
//セッション
//++++++++++++++++

//！！注意！！！！！！！！！！！！
//セッションファイルの置き場所は、レンタルサーバあげる時変更必要かも
session_save_path("/var/tmp/");
ini_set('session.gc_maxlifetime', 60*60*24*30);
ini_set('session.cookie_lifetime', 60*60*24*30);
session_start();
session_regenerate_id();

//++++++++++++++++
//画面表示
//++++++++++++++++
function debugLogStart(){
    debug('++++++++++++++++++++++++++++++++画面表示処理スタート');
    debug('セッションID：'.session_id());
    debug('セッション変数の中身：'.print_r($_SESSION,true));
    debug('現在日時タイムスタンプ：'.time());
    if(!empty($_SESSION['login_date']) && !empty($_SESSION['login_limit'])){
        debug('ログイン期限日時タイムスタンプ：'.($_SESSION['login_date'] + $_SESSION['login_limit']));
    }
}

//++++++++++++++++
//定数
//++++++++++++++++
define('MSG1','↓入力必須です');
define('MSG2','Emailの形式で入力してください。');
define('MSG3','パスワード（再入力）が合っていません。');
define('MSG4','半角英数字のみご利用いただけます。');
define('MSG5','6文字以上で入力してください。');
define('MSG6','255文字以上で入力してください。');
define('MSG7','	システムエラーが発生いたしました。申し訳ございませんが、暫く時間を置いてから再度操作をお願いします。');
define('MSG8','そのEmailで既に登録済です。');
define('MSG9','メールアドレス又はパスワードが違います。');
define('MSG10','旧パスワードが相違しています。');
define('MSG11','旧パスワードと同じです。');
define('MSG12','必ず選択してください。');
define('SUC01', 'パスワードを変更しました');
define('SUC02', 'プロフィールを変更しました');
define('SUC03', 'メールを送信しました');
define('SUC04', '登録しました');

//++++++++++++++++
//バリデーション各関数
//++++++++++++++++
$err_msg = array();

//入力チェック
function validRequired($str, $key){
    if(empty($str)){
        global $err_msg;
        $err_msg[$key] = MSG1;
    }
}
//Email形式チェック
function validEmail($str, $key){
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str)){
       global $err_msg;
        $err_msg[$key] = MSG2;
    }
}

//パスワード再入力チェック
function validMatch($str1, $str2, $key){
    if($str1 !== $str2){
        global $err_msg;
        $err_msg[$key] = MSG3;
    }
}
//半角チェック
function validHalf($str, $key){
    if(!preg_match("/^[a-zA-Z0-9]+$/", $str)){
        global $err_msg;
        $err_msg[$key] = MSG4;
    }
}
//最少文字数チェック
function validMinLen($str, $key, $min = 6){
    if(mb_strlen($str) < $min){
        global $err_msg;
        $err_msg[$key] = MSG5;
    }
}
//最大文字数チェック
function validMaxLen($str, $key, $max = 255){
    if(mb_strlen($str) > $max){
        global $err_msg;
        $err_msg[$key] = MSG6;
    }
}
//Email重複チェック
function validEmailDup($email){
    global $err_msg;
    try {
        $dbh = dbConnect();
        $sql = 'SELECT count(*) FROM users WHERE email = :email AND delete_flg = 0';
        $data = array(':email' => $email);
        $stmt = queryPost($dbh, $sql, $data);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($result['count(*)'])){
            $err_msg['email'] = MSG8;
        }
    } catch (Exception $e) {
        error_log('エラー発生：'.$e->getMessage());
        $err_msg['common'] = MSG7;
    }
}
//パスワードをまるっとチェック
function validPass($str, $key){
    validHalf($str, $key);
    validMaxLen($str, $key);
    validMinLen($str, $key);
}
//メッセージエラー表示
function getErrMsg($key){
    global $err_msg;
    if(!empty($err_msg[$key])){
        return $err_msg[$key];
    }
}
//セレクトボックス選択チェック
//カテゴリーのvalueが0〜10以外なら選択ミスにする（今はカテゴリー１〜１０まで登録済み）
function validSelect($str, $key){
  if(!preg_match("/^[0-9]+$/", $str)){
    global $err_msg;
    $err_msg[$key] = MSG12;
  }
}

//++++++++++++++++
//ログイン検証
//++++++++++++++++
function isLogin(){
    if(!empty($_SESSION['login_date'])){
        debug('ログイン済みユーザー');
        
        if($_SESSION['login_date'] + $_SESSION['login_limit'] < time()){
            debug('ログイン有効期限オーバー');
            session_destroy();
            return false;
        }else{
            debug('ログイン有効期限内');
            return true;
        }
    }else{
        debug('未ログインユーザー');
        return false;
    }
}


//++++++++++++++++
//データベース
//++++++++++++++++
 function dbConnect(){
           $dsn = 'mysql:dbname=drama_log;host=localhost;charset=utf8';
           $user = 'root';
           $password = 'root';
           $options = array(
           PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
           PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
           PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
           );
           $dbh = new PDO($dsn, $user, $password, $options);
           return $dbh;
       }
       
       function queryPost($dbh, $sql, $data){
           $stmt = $dbh->prepare($sql);
           $stmt->execute($data);
           return $stmt;
       }


       function getUser($u_id){
           debug('ユーザー情報を取得');
           try {
               $dbh = dbConnect();
               $sql = 'SELECT * FROM users WHERE id = :u_id AND delete_flg = 0';
               $data = array(':u_id' => $u_id);
               $stmt = queryPost($dbh, $sql, $data);
               
               if($stmt){
                   return $stmt->fetch(PDO::FETCH_ASSOC);
               }else{
                   return false;
               }
           } catch (Exception $e){
               error_log('エラー発生：'.$e->getMessage());
           }
       }

    function getWork($u_id, $w_id){
        debug('作品情報を取得');
        debug('ユーザーID：'.$u_id);
        debug('作品ID：'.$w_id);
        
        try {
            $dbh = dbConnect();
            $sql = 'SELECT * FROM work WHERE user_id = :id AND id = :w_id AND delete_flg = 0';
            $data = array(':id' => $u_id, ':w_id' => $w_id);
            $stmt = queryPost($dbh, $sql, $data);
            
            if($stmt){

                return $stmt->fetch(PDO::FETCH_ASSOC);
            }else{
                return false;
            }
        } catch (Exception $e) {
            error_log('エラー発生：'.$e->getMessage());
        }
    }

    function getWorkList($currentMinNum = 1, $category, $sort, $span = 12){
        debug('作品情報を取得');
        try {
            $dbh = dbConnect();
            $sql = 'SELECT id FROM work';
//            以下でSQL文それぞれくっつけて検索できるようにする
//            カテゴリー検索
            if(!empty($category)) $sql .= ' WHERE category_id = '.$category;
//            評価検索
            if(!empty($sort)){
                switch($sort){
                    case 1:
                        $sql .=' ORDER BY rating ASC';
                        break;
                    case 2:
                        $sql .=' ORDER BY rating DESC';
                        break;
                }
            }
             $data = array();
            $stmt = queryPost($dbh, $sql, $data);
//            総レコード数
            $rst['total'] = $stmt->rowCount();
//            総ページ数
            $rst['total_page'] = ceil($rst['total']/$span);
            if(!$stmt){
                return false;
            }
            
//            ページング用のSQL文
            $sql = 'SELECT * FROM work';
            if(!empty($category)) $sql .=' WHERE category_id = '.$category;
//            評価検索
            if(!empty($sort)){
                switch($sort){
                    case 1:
                        $sql .= ' ORDER BY rating ASC';
                        break;
                    case 2:
                        $sql .= ' ORDER BY rating DESC';
                        break;
                        
                       
                }
            }
//            全レコードのうち$currentMinNum番目から$spanレコードを取得
//            つまり全レコードのうち０番目（ここは変わる）から12レコードを取得
            $sql .= ' LIMIT '.$span.' OFFSET '.$currentMinNum;
            $data = array();
            debug('SQL:'.$sql);
            $stmt = queryPost($dbh, $sql, $data);
            
            if($stmt){
//                クエリ結果のデータを全レコード格納
                $rst['data'] = $stmt->fetchAll();
                return $rst;
            }else{
                return false;
            }
        } catch (Exception $e) {
            error_log('エラー発生：'.$e->getMessage());
        }
    }

    function getWorkOne($w_id){
        debug('作品情報を取得します。');
        debug('作品ID：'.$w_id);
        
        
    try {
        $dbh = dbConnect();
        $sql = 'SELECT w.id, w.title, w.season, w.rating, w.episode, w.comment, w.pic, w.user_id, w.create_date, w.update_date, c.name AS category FROM work AS w LEFT JOIN category AS c ON w.category_id = c.id WHERE w.id = :w_id AND w.delete_flg = 0 AND c.delete_flg = 0';
        
        $data = array(':w_id' => $w_id);
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }

    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
  }
}

 function getUserOne($u_id){
        debug('投稿者情報を取得します。');
        debug('投稿者ID：'.$u_id);
        
        
    try {
        $dbh = dbConnect();
        $sql = 'SELECT u.id, u.username, u.pic, u.introduction, u.create_date, u.update_date FROM users AS u LEFT JOIN work AS w ON u.id = w.user_id WHERE u.id = :u_id AND u.delete_flg = 0 AND w.delete_flg = 0';
        
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }else{
            return false;
        }

    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
  }
}


function getMyWorks($u_id){
    debug('自分の登録作品を取得');
    debug('ユーザーID:'.$u_id);
    
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM work WHERE user_id = :u_id AND delete_flg = 0';
        $data = array(':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}


    function getCategory(){
        debug('カテゴリー情報を取得');
        try {
            $dbh = dbConnect();
            $sql = 'SELECT * FROM category';
            $data = array();
            $stmt = queryPost($dbh, $sql, $data);
            
            if($stmt){

                return $stmt->fetchAll();
            }else{
                return false;
            }
        } catch (Exception $e){
            error_log('エラー発生：'.$e->getMessage());
        }
    }

function isLike($u_id, $w_id){
    debug('お気に入り情報があるか確認');
    debug('ユーザーID：'.$u_id);
    debug('作品ID：'.$w_id);
    
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM `like` WHERE work_id = :w_id AND user_id = :u_id';
        $data = array(':w_id' => $w_id, ':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt->rowCount()){
            debug('お気に入り');
            return true;
        }else{
            debug('お気に入りではない');
            return false;
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

function getMyLike($u_id){
    debug('自分のお気に入り情報を取得');
    debug('ユーザーID：'.$u_id);
    
    try {
        $dbh = dbConnect();
        $sql = 'SELECT * FROM `like` AS l LEFT JOIN work AS w ON l.work_id = w.id WHERE l.user_id = :u_id';
        $data = array (':u_id' => $u_id);
        $stmt = queryPost($dbh, $sql, $data);
        
        if($stmt){
            return $stmt->fetchAll();
        }else{
            return false;
        }
    } catch (Exception $e){
        error_log('エラー発生：'.$e->getMessage());
    }
}

//++++++++++++++++
//その他
//++++++++++++++++


//サニタイズ関数
function sanitize($str){
//htmlspecialchars(文字列を指定, 変換スタイルを指定, 文字コードを指定（※省略可）);
    return htmlspecialchars($str, ENT_QUOTES);
}



//フォーム入力保持
function getFormData($str, $flg = false){
    if($flg){
        $method = $_GET;
    }else{
        $method = $_POST;
    }
    global $dbFormData;
//    ユーザーデータがある場合
    if(!empty($dbFormData)){
//        ユーザーデータがあり、フォームのエラーあり
        if(!empty($err_msg[$str])){
//            ユーザーデータがあり、フォームのエラーあり、POSTにデータがある
            if(isset($method[$str])){
//                サニタイズしたPOSTの入力値を返す
                return sanitize($method[$str]);
            }else{
//                POSTにデータがない場合はDBの情報を表示
                return sanitize($dbFormData[$str]);
            }
        }else{
//            ユーザーデータがあり、フォームエラーがなく、そしてDBの情報と違う場合
            if(isset($method[$str]) && $method[$str] !== $dbFormData[$str]){
                return sanitize($method[$str]);
            }else{
                return sanitize($dbFormData[$str]);
            }
        }
    }else{
        if(isset($method[$str])){
            return sanitize($method[$str]);
        }
    }
}

//sessionを１回だけ取得できる
function getSessionFlash($key){
    if(!empty($_SESSION[$key])){
        $data = $_SESSION[$key];
        $_SESSION[$key] = '';
        return $data;
    }
}



function uploadImg($file, $key){
    debug('画像アップロード処理開始');
    debug('FILE情報：'.print_r($file, true));
    
    if(isset($file['error']) && is_int($file['error'])){
        try {

            switch($file['error']){
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new RuntimeException('ファイルが未選択です');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new RuntimeException('ファイルサイズが大きすぎます');
                default:
                     throw new RuntimeException('その他のエラーが発生しました');
            }
            
                    $type = @exif_imagetype($file['tmp_name']);
                    if(!in_array($type, [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_PSD], true)){
                        throw new RuntimeException('画像形式が未対応です');
                    }
//$pathにファイルの保存先を指定している。uploadsは設置したディレクトリ名
            $path = 'uploads/'.sha1_file($file['tmp_name']).image_type_to_extension($type);
//            move_uploaded_fileの第一引数に移動前の場所、第二引数に移動後の$pathの場所を指定して、きちんと移動していない時、エラーを出す。
            if(!move_uploaded_file($file['tmp_name'], $path)){
                throw new RuntimeException('ファイル保存時にエラーが発生しました');
            }
            chmod($path, 0644);
               debug('ファイルは正常にアップロード済');
               debug('ファイルパス：'.$path);
               return $path;
        } catch (RuntimeException $e){
            debug($e->getMessage());
            global $err_msg;
            $err_msg[$key] = $e->getMessage();
        }
    }
}

//ページング
// $currentPageNum : 現在のページ数
// $totalPageNum : 総ページ数
// $link : 検索用GETパラメータリンク
// $pageColNum : ページネーション表示数
function pagination( $currentPageNum, $totalPageNum, $link = '', $pageColNum = 5){
  // 現在のページが、総ページ数と同じ　かつ　総ページ数が表示項目数以上なら、左にリンク４個出す
  if( $currentPageNum == $totalPageNum && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 4;
    $maxPageNum = $currentPageNum;
  // 現在のページが、総ページ数の１ページ前なら、左にリンク３個、右に１個出す
  }elseif( $currentPageNum == ($totalPageNum-1) && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 3;
    $maxPageNum = $currentPageNum + 1;
  // 現ページが2の場合は左にリンク１個、右にリンク３個だす。
  }elseif( $currentPageNum == 2 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum - 1;
    $maxPageNum = $currentPageNum + 3;
  // 現ページが1の場合は左に何も出さない。右に５個出す。
  }elseif( $currentPageNum == 1 && $totalPageNum > $pageColNum){
    $minPageNum = $currentPageNum;
    $maxPageNum = 5;
  // 総ページ数が表示項目数より少ない場合は、総ページ数をループのMax、ループのMinを１に設定
  }elseif($totalPageNum < $pageColNum){
    $minPageNum = 1;
    $maxPageNum = $totalPageNum;
  // それ以外は左に２個出す。
  }else{
    $minPageNum = $currentPageNum - 2;
    $maxPageNum = $currentPageNum + 2;
  }
  
  echo '<div class="pagination">';
    echo '<ul class="pagination-list">';
      if($currentPageNum != 1){
        echo '<li class="list-item"><a href="?p=1'.$link.'">&lt;</a></li>';
      }
      for($i = $minPageNum; $i <= $maxPageNum; $i++){
        echo '<li class="list-item ';
        if($currentPageNum == $i ){ echo 'active'; }
        echo '"><a href="?p='.$i.$link.'">'.$i.'</a></li>';
      }
      if($currentPageNum != $maxPageNum && $maxPageNum > 1){
        echo '<li class="list-item"><a href="?p='.$maxPageNum.$link.'">&gt;</a></li>';
      }
    echo '</ul>';
  echo '</div>';
}

//画像表示用関数
function showImg($path){
    if(empty($path)){
        return 'img/No_image.jpg';
    }else{
        return $path;
    }
}

//GETパラメータ付与
function appendGetParam($arr_del_key = array()){
    if(!empty($_GET)){
        $str = '?';
        foreach($_GET as $key => $val){
            if(!in_array($key, $arr_del_key, true)){
                $str .=$key.'='.$val.'&';
            }
        }
        $str = mb_substr($str, 0, -1, "UTF-8");
        return $str;
    }
}


