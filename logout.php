<?php
require('function.php');

debug('===========================================');
debug('ログアウトページ');
debug('===========================================');
debugLogStart();

debug('ログアウト');
session_destroy();
debug('ログインページへ');
header("Location:login.php");