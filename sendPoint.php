<?php 
  include("controller/sessionAuthController.php");
  $doc_id=$_POST["doc_id"];
?>
  <head>
    <meta charset="UTF-8">
    <title>openShips</title>
    <script src="http://maps.google.com/maps/api/js?key=AIzaSyAi0pHE0RZrJ0x07V-SqFgcErqL54FAWdE&language=ja"></script>
    <script src='https://code.jquery.com/jquery-3.3.1.js'></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase.js"></script>   
    <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase-analytics.js"></script>
    <script src="js/firebase.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/common.css">
    <link rel="stylesheet" type="text/css" href="./css/style.css">
  </head>
  <div class="sendPointButtons">
    <button id="sendStart" class="sendPointButton" onclick="startSend()">出発する</button>
    <button id="sendStop" class="sendPointButton" onclick="stopSend()">終了する</button>
  </div>
  <script>
    let now=new Date(); 
    let user_id='<?=$user_id?>';
    let doc_id='<?=$doc_id?>';
    running_status=''
    $("#sendStop").prop("disabled",false)


    function startSend(){
      $("#sendStart").prop("disabled",true)
      $("#sendStop").prop("disabled",false)
      running_status='on'
      let ref=db.collection('task').doc(doc_id)
      ref.update({
         running_status:'on'
      })
      doSendPoint()
    }

    function stopSend(){
      $("#sendStart").prop("disabled",false)
      $("#sendStop").prop("disabled",true)   
      running_status='off'   
      let ref=db.collection('task').doc(doc_id)
      ref.update({
         running_status:'off'
      })
    }

    function doSendPoint(){
      navigator.geolocation.watchPosition(function(pos) {//watchPositionでおそらく追跡 getCurrentPositionは一度取得
        // 取得成功時の処理
        if(running_status=='on'){
          let latitude  = pos.coords.latitude;//緯度
          let longitude = pos.coords.longitude;//経度
          let LatLng=new firebase.firestore.GeoPoint(latitude, longitude);
          console.log(LatLng)
          let ref=db.collection('task').doc(doc_id)
          ref.update({
            latlng:LatLng
          })
        }
      })
    }

    //ページを抜けるとき
    // $(window).on('beforeunload', function(event) {
    //   // stopTest()
    //   return 'jquery beforeunload';
    // });
  </script>
</html>