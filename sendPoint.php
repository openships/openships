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
  </head>
  <script>
    let now=new Date(); 
    let user_id='<?=$user_id?>';
    let doc_id='<?=$doc_id?>';
    navigator.geolocation.watchPosition(function(pos) {//watchPositionでおそらく追跡 getCurrentPositionは一度取得
      // 取得成功時の処理
      let latitude  = pos.coords.latitude;//緯度
      let longitude = pos.coords.longitude;//経度
      let LatLng=new firebase.firestore.GeoPoint(latitude, longitude);
      
      let ref=db.collection('task').doc(doc_id)
      ref.update({
        latlng:LatLng
      })
      //   let existJudge='no';
      //   let updeId=''
      //   querySnapshot.docs.forEach(function(doc){
      //     // const data=doc.data();
      //     // const docID=doc.id;
      //     // if(docID==task_id){
      //     //   existJudge='yes';
      //     //   updeId=doc.id;
      //     // }
      //   });
      //   if(existJudge=='no'){
      //     db.add({
      //         user_id:user_id,
      //         latlng:LatLng
      //     });
      //   }else{
      //     updeDoc=db.doc(updeId);
      //     updeDoc.update({
      //       latlng:LatLng
      //     });
      //   }
      // });
    });
  </script>
</html>