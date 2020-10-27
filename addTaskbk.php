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
    <link rel="stylesheet" type="text/css" href="./css/common.css">
  </head>

  <h1>配送案件追加</h1>
  <div id="msg"></div>
  <table>
    <tr><th>配送先</th><td><input type="text" name="shipping-address" id="shipping-address"></td></tr>
    <tr><th>集荷先</th><td><input type="text" name="pick-up-adress" id="pick-up-address"></td></tr>
    <tr><th>配送時間</th><td><input type="datetime-local" name="shipping-time" id="shipping-time"></td></tr>
    <tr><th>集荷時間</th><td><input type="datetime-local" name="pick-up-time" id="pick-up-time"></td></tr>
    <tr><td><button id="submit">登録</button></td></tr>
  </table>
  <script>
    //登録処理
    $("#submit").on("click",function(){
      let shipping_address=$("#shipping-address").val();
      let pick_up_address=$("#pick-up-address").val();
      let shipping_time=$("#shipping-time").val();
      let pick_up_time=$("#pick-up-time").val();
      let user_id="<?=$user_id?>"
      if(shipping_address=='' || pick_up_address=='' || shipping_time=='' || pick_up_time==''){
        $("#msg").text('必須項目を入力してください');
        return false;
      }else{
        db.collection('user').where("user_id","==",user_id).get().then(function(querySnapshot){
          doc=querySnapshot.docs[0]
          data=doc.data()
          tel=data.tel
          db.collection('task').add({
            shipping_address:shipping_address,
            pick_up_address:pick_up_address,
            shipping_time:shipping_time,
            pick_up_time:pick_up_time,
            user_id:user_id,
            tel:tel,
          });
          setTimeout(function(){location.href="./mypage.php"},500);
        })
      }
    });
  </script>
</html>
