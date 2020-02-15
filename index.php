<?php
require('function.php');

debug('===========================================');
debug('トップページ');
debug('===========================================');
debugLogStart();

$category = (!empty($_GET['c_id'])) ? $_GET['c_id'] : '';

$sort = (!empty($_GET['sort'])) ? $_GET['sort'] : '';

$currentPageNum = (!empty($_GET['p'])) ? $_GET['p'] : 1;

//１ページあたりの表示件数
$listSpan = 12;
//現在の表示レコード先頭を算出
$currentMinNum = (($currentPageNum-1)*$listSpan);
//DBから作品データを取得
$dbWorkData = getWorkList($currentMinNum, $category, $sort);
//DBからカテゴリーデータを取得
$dbCategoryData = getCategory();

if(!is_int((int)$currentPageNum)){
    error_log('エラー発生：指定ページに不正な値あり');
    header("Location:index.php");
}

debug('画面表示処理終了++++++++++++++++++++++++++++++++');

?>


<?php
$siteTitle = 'トップページ';
require('head.php');
?>


<body>

<!--    ヘッダー-->
<?php
       require('header.php');
       ?>

<!--メインコンテンツ-->
<section class="main">
<div class="main-container">
    
 <div class="intro">
  <p>このサイトは視聴した海外ドラマの記録を保存するためのサイトです。</p>
<p>自分の気に入った海外ドラマを記録していきましょう！！</p>
 </div>
    
<div class="main-container-under">
    
     <!--検索バー-->
<section id="searchbar">
    <form method="get" class="form-search">
       
        <h1 class="title">カテゴリー</h1>
        <div class="selectbox">
<!--          右の三角印を出すspan-->
           <span class="icn_select"></span>
            <select name="c_id">
                <option value="0" <?php if(getFormData('c_id',true) == 0){
    echo 'selected';
} ?>>選択してください</option>
               <?php 
                foreach($dbCategoryData as $key => $val){
                    ?>
                    <option value="<?php echo $val['id'] ?>" <?php if(getFormData('c_id',true) == $val['id'] ){
                        echo 'selected'; } ?>> 
                        <?php echo $val['name']; ?>
                        </option>
                    <?php
                }
                ?>
            </select>
        </div>

         <h1 class="title">表示順</h1>
        <div class="selectbox">
           <span class="icn_select"></span>
            <select name="sort">
               <option value="0" <?php if(getFormData('sort',true) == 0){
    echo 'selected';
} ?>>選択してください</option>
                <option value="1" <?php if(getFormData('sort',true) == 1){
    echo 'selected'; } ?>>評価低い順</option>
                <option value="2" <?php if(getFormData('sort',true) == 2){
    echo 'selected'; } ?>>評価高い順</option>
            </select>
        </div>
        <input type="submit" value="検索" class="btn-search">

    </form>
</section>

  <section class="panel-contents">
      
   <div class="search-title">
       <div class="search-left">
           <span class="total-num" style="color:#cc66ff;"><?php echo sanitize($dbWorkData['total']); ?> </span>件あり
       </div>
       
       <div class="search-right">
           <span class="num" style="color:#cc66ff; font-weight: bold;"><?php echo (!empty($dbWorkData['data'])) ? $currentMinNum+1 : 0; ?>
           </span> - <span class="num" style="color:#cc66ff;"><?php 
           if(is_array($dbWorkData['data'])){
           echo $currentMinNum+count($dbWorkData['data']);
           } ?> </span>件 / <span class="num" style="color:#cc66ff;"><?php  echo sanitize($dbWorkData['total']); ?></span>件中

       </div>
   </div>
   
   
     <h1><i class="fas fa-caret-right fa-lg"></i>最近の投稿一覧</h1>

    <div class="panel-list">
        <?php 
        foreach($dbWorkData['data'] as $key => $val):
        ?>

      <a href="workDetail.php<?php echo (!empty(appendGetParam())) ? appendGetParam().'&w_id='.$val['id'] : '?w_id='.$val['id']; ?> " class="panel-top">
          <div class="panel-pic">
              <img src="<?php echo showImg(sanitize($val['pic'])); ?>" alt="<?php echo sanitize($val['title']); ?> " class="img-top">
          </div>
          <div class="panel-body">
              <p class="panel-title">
                  <?php echo sanitize($val['title']); ?>
                  <span class="rating"><i class="fas fa-star icn-star"></i>評価：<?php echo sanitize(number_format($val['rating'],1)); ?> </span>
              </p>
          </div>
      </a>

      <?php
        endforeach;
      ?>

    </div>
  </section>

</div>

        <?php pagination($currentPageNum, $dbWorkData['total_page']); ?>

    </div>
  </section>


<!--フッター-->
<?php
       require('footer.php');
       ?>