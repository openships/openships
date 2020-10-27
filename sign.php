<!DOCTYPE html>
<html lang="ja">

  <head>
    <meta charset="UTF-8">
    <title>openShips</title>
    <script src="http://maps.google.com/maps/api/js?key=AIzaSyAi0pHE0RZrJ0x07V-SqFgcErqL54FAWdE&language=ja"></script>
    <script src='https://code.jquery.com/jquery-3.3.1.js'></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase.js"></script>   
    <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase-analytics.js"></script>
    <script src="js/firebase.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/login.css">
    <link rel="stylesheet" type="text/css" href="./css/common.css">
    <link rel="stylesheet" type="text/css" href="./css/sign.css">
  </head>
<!-- <form action="../controller/login-act.php" method="POST"> -->
  <div class="switch-buttons">
    <div class="switch left">ログイン</div><!--
--><div class="switch right">登録</div>
  </div>
  <div id="msg"></div>
  <table>
    <tr><th>会社名：</th><td><input type="text" name="user_id" id="user_id"></td></tr>
    <tr><th>パスワード：</th><td><input type="text" name="password" id="password"></td></tr>
    <tr class="signUp"><th>住所：</th><td><input type="text" name="address" id="address"></td></tr>
    <tr class="signUp"><th>電話番号：</th><td><input type="tel" name="tel" id="tel"></td></tr>
    <tr class="signUp"><th>ホームページURL：</th><td><input type="url" name="url" id="url"></td></tr>
    <tr>
      <td colspan="2">
        <input id="signIn" class="submit-btn" type="submit" value="ログイン"><input id="signUp" class="submit-btn signUp" type="submit" value="登録">
      </td>
    </tr>
  </table>
  <img class="logo string-logo" src="./img/login-background-logo.png">
  <img class="logo" src="./img/background-Earth.png">
<!-- </form> -->

<script>
  //ログイン・登録切り替え処理
  $(".left").addClass('active');
  $(".signUp").hide();
  $(".switch").on('click',function(){
    $(this).parent().find('.switch').removeClass('active');
    $(this).addClass('active');
    if($(this).hasClass('left')){
      $("#signIn").show();
      $(".signUp").hide();
    }else{
      $(".signUp").show();
      $("#signIn").hide();
    }
  });

  //サイン処理
  $('.submit-btn').on('click',function(){
    let user_id=$("#user_id").val();
    let password=$("#password").val();
    let address=$("#address").val();
    let tel=$("#tel").val();
    let url=$("#url").val();

    if($(this).attr("id")=="signIn"){
      //ログイン処理
      db.collection('user').get().then(function(querySnapshot){
        querySnapshot.docs.forEach(function(doc){
          const data=doc.data();
          const userIdInFirebase=data.user_id;
          const passwordInFirebase=data.password;
          if(userIdInFirebase==user_id && passwordInFirebase==password){
            const data={"user_id":user_id,"password":password};
            $.ajax({
              type:"POST",
              url:"./controller/sessionCreateController.php",
              data:data,
              success:function(){
                location.href="mypage.php";
              },
              error:function(XMLHttpRequest,textStatus,errorThrown){
                alert(errorThrown);
              }
            });
          }
        });
      });
    }else{
      //登録処理
      let existJudge='no';
      db.collection('user').get().then(function(querySnapshot){
        querySnapshot.docs.forEach(function(doc){
          const data=doc.data();
          const userIdInFirebase=data.user_id;
          if(userIdInFirebase==user_id){
            existJudge='yes';
          }
        });
        if(existJudge=='no'){
          const data={"user_id":user_id,"password":password};
          $.ajax({
            type:"POST",
            url:"./controller/sessionCreateController.php",
            data:data,
            success:function(){
              db.collection('user').add({
                user_id:user_id,
                password:password,
                address:address,
                tel:tel,
                url:url
              });
              setTimeout(function(){location.href="mypage.php"},500);
            },
            error:function(XMLHttpRequest,textStatus,errorThrown){
              alert(errorThrown);
            }
          });

        }else{
          $("#msg").text('すでに登録されているユーザーIDです');
        }
      });
    }
  });
</script>
</html>