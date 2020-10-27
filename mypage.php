<?php 
  include("controller/sessionAuthController.php");
?>
<!DOCTYPE html>
<html lang="ja">
  
  <head>
    <meta charset="UTF-8">
    <title>openShips</title>
    <script src="https://maps.google.com/maps/api/js?key=AIzaSyAi0pHE0RZrJ0x07V-SqFgcErqL54FAWdE&language=ja"></script>
    <script src='https://code.jquery.com/jquery-3.3.1.js'></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase.js"></script>   
    <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase-analytics.js"></script>
    <script src="js/firebase.js"></script>
    <link rel="stylesheet" type="text/css" href="css/mypage.css">
  </head>

  <div class="header">
    マイページ
  </div>

  <div class="track-pic"></div>

  <div class="global-nav">
    <ul class="nav-lists">
      <li class="menu">メニュー</li>
      <li id="menu-task" class="menu-list">案件一覧</li>
      <li id="menu-track" class="menu-list">トラック一覧</li>
    </u>
  </div>

  <div class="content-area"></div>

  <script>
    let user_id="<?=$user_id?>";
    $("#menu-task").on("click",function(){
      script = document.createElement('script'); //変数名は適当なものにでも
      script.src = "js/showTaskList.js"; //ファイルパス
      $(".content-area").html(script)
      $(".header").html(this.textContent)
    })

    $("#menu-track").on("click",function(){
      script = document.createElement('script'); //変数名は適当なものにでも
      script.src = "js/showTrackList.js"; //ファイルパス
      $(".content-area").html(script)
      $(".header").html(this.textContent)
    })
  </script>
</html>