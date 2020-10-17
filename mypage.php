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
  </head>

  <h1>マイページ</h1>
  <div>
    <a href="addTask.php">新規追加</a>
  </div>
  <table id="data-list">
    <tr><th>配送先</th><th>集荷先</th><th>配送時間</th><th>集荷時間</th></tr>
  </table>
  <script>
    let user_id="<?=$user_id?>";
    let output='';
    db.collection('task').where("user_id","==",user_id).get().then(function(querySnapshot){
      querySnapshot.docs.forEach(function(doc){
        const data=doc.data();
        output+="<tr>"
        output+="<td>"+data.shipping_address+"</td>";
        output+="<td>"+data.pick_up_address+"</td>";
        output+="<td>"+data.shipping_time+"</td>";
        output+="<td>"+data.pick_up_time+"</td>";
        output+="<td>";
        output+="<form action='sendPoint.php' method='POST'>"
        output+="<input type='hidden' name='doc_id' value='"+doc.id+"'>"
        output+="<input type='submit' value='出発する'>";
        output+="</form>"
        output+="</td>";
        output+="</tr>"
      });
      console.log(output)
      $("#data-list").append(output);
    });
  </script>
</html>