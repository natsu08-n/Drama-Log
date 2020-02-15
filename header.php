
<header>
   <div class="header">
       <h1><a href="index.php">Drama Log</a></h1>
       <nav class="nav-home">
           <ul>

               <?php
               if(empty($_SESSION['user_id'])){
                ?>
                 <li class="nav-box"><a href="login.php" class="nav-btn">ログイン</a></li>
             <li><a href="signup.php" class="nav-right">新規登録</a></li>

             <?php
             }else{
               ?>
                <li class="nav-box"><a href="mypage.php" class="nav-btn">マイページ</a></li>
             <li><a href="logout.php" class="nav-right">ログアウト</a></li>

                <?php
             }
               ?>

           </ul>
       </nav>
   </div>

</header>