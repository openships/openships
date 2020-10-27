<!DOCTYPE html>
<html lang="ja">
  
  <head>
    <meta charset="UTF-8">
    <title>openShips</title>
    <script src="https://maps.google.com/maps/api/js?key=AIzaSyAi0pHE0RZrJ0x07V-SqFgcErqL54FAWdE&language=ja"></script>
    <script src='https://code.jquery.com/jquery-3.3.1.js'></script>
    <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase.js"></script>   
    <script src="https://www.gstatic.com/firebasejs/7.14.5/firebase-analytics.js"></script>
    <script src="js/testProgram.js"></script>
    <script src="js/firebase.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
    <!-- <link rel="stylesheet" type="text/css" href="./main.css"> -->
    <link rel="stylesheet" type="text/css" href="./css/mapmain.css">
  </head>
  

  
 
  <body>
  <img class="logo" src="img/logo.png">
    <div class="testButtons">
      <input id="testStart" type="button" value="テスト開始" onclick=doTest()>
      <input id="testStop" type="button" value="テスト終了" disabled onclick=stopTest()>
    </div>
    <!-- <div id="output"></div> -->
    <!---->
    <div class="map-area">
    <div id="map"></div>
    <!-- <button id="send">送信</button> -->
    <div id="infoBoxesContent"></div>
    </div>


    <script>
      let directionsService = new google.maps.DirectionsService();

      //マーカーサイズの初期サイズをデバイスで切り分ける
      let ua = navigator.userAgent;
      let markerSize='';
      let markerIcon='';
      let device=''

      // let pickLatLng=''
      shippingLatLng=''
      if (ua.indexOf('iPhone') > 0 || ua.indexOf('Android') > 0 && ua.indexOf('Mobile') > 0) {
        // スマートフォン用コード
        markerSize=48;
        markerIcon='./img/48×48.png';
        device='SP'
      }else{
        // PC用コード
        markerSize=32;
        markerIcon='./img/32×32.png';
        device='PC'
      }

      let map="";
      let marker='';
      let zoom='';
      let markers=[];
      let LatsLngs=[];
      let directionsDisplay=[];
      let infoBoxes=[];
      let pick_up_addresses=[];
      let shipping_addresses=[];

      //現在地を中心にMapを展開
      navigator.geolocation.getCurrentPosition(function(pos) {
        let Mylatitude  = pos.coords.latitude;//緯度
        let Mylongitude = pos.coords.longitude;//経度
        let MyLatLng = new google.maps.LatLng(33.57662, 130.403055);
        console.log('緯度:'+Mylatitude+' 経度:'+Mylongitude);
        //設定
        let Options = {
          zoom: 13,      //地図の縮尺値
          center: MyLatLng,    //地図の中心座標
          //mapTypeId: 'roadmap'   //地図の種類
        };
        map = new google.maps.Map(document.getElementById('map'), Options);
      });

      //firebaseに変更があるたびマーカーを再壁画
      db.collection('task').onSnapshot(function(querySnapshot){
        setMarker(querySnapshot);
        // output = '緯度'+latitude+'<br> 経度:'+longitude;
        // $('#output').html(output);
      });

      //マーカーを壁画
      function setMarker(querySnapshot){
        let data=''
        let LatLng=''

        LatsLngs=[];
        pick_up_addresses=[];
        shipping_addresses=[];
        
        //マーカーを削除
        if(!(markers=='')){
          for (let i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
          }
        }
        markers=[];

        querySnapshot.docs.forEach(function(doc){
          data=doc.data();
          running_status=data.running_status
          if(running_status=='on'){
            latitude=data.latlng['Ic'];
            longitude=data.latlng['wc'];
            user_id=data.user_id;
            pick_up_time=data.pick_up_time
            pick_up_time=pick_up_time.replace('T', ' ')
            pick_up_time=pick_up_time.replace(/-/g, '/')
            shipping_time=data.shipping_time
            shipping_time=shipping_time.replace('T', ' ')
            shipping_time=shipping_time.replace(/-/g, '/')
            pick_up_address=data.pick_up_address
            shipping_address=data.shipping_address
            tel=data.tel
            track_number=data.track_number
            track_img=data.track_img
            url=data.url
           // console.log(track_img)
            // track_type=data.track_type
            // max_weight=data.max_weight
            // max_length=data.max_length              

            //インフォメーション作成
            let infoboxContent = document.createElement('div');
            infoboxContent=`
              <div class="infobox">
                <div class="non-display-button" id="non-display">×</div>
                <div class="inner">
                  <div class="header">`+user_id+`</div>
                  <div class="contact"><a href="tel:`+tel+`"><img src="img/icon_tel.png"><p><u>`+tel+`</u></p></a></div>
                  <div class="site"><a href="`+url+`"><img src="img/icon_site.png"><p><u>`+url+`</u></p></a></div>
                  <div class="main-img"><img src="./uploads/`+track_img+`"></div>
                  <div class="fromTo">
                    <table>
                      <tr>
                        <td class="fromTo_inner left">`+pick_up_address+`</td>
                        <td class="fromTo_inner right">`+shipping_address+`</td>
                      </tr>
                    </table>
                    <div class="fromTo_icon"><img src="img/info_icon.png"></div>
                  </div>
                  <div class="date">
                    <div class="date_inner left">`+pick_up_time+`</div><!--
                    --><div class="date_inner right">`+shipping_time+`</div>
                  </div>
                  <div class="container"></div>
                  <div class="footer"></div>
                </div>
              </div>`
              infoBoxes.push(infoboxContent);
    
            LatLng = new google.maps.LatLng(latitude, longitude);

            //マーカーを表示
            marker=new google.maps.Marker({
                map: map,
                position: LatLng,
                icon :new google.maps.MarkerImage(
                  markerIcon,
                  new google.maps.Size(markerSize, markerSize),
                ),
            });
            markers.push(marker);


            LatsLngs.push(LatLng)   

            pick_up_addresses.push(pick_up_address)
            shipping_addresses.push(shipping_address)
          }
        })

        //マーカークリックでインフォと経路を表示する
        for(let i=0; i<markers.length; i++){
          markers[i].addListener('click',function(){
            $(".gm-style-iw-a").remove();
            //infoes[i].open(map,markers[i]);
            getRoute(LatsLngs[i],zoom,pick_up_addresses[i],shipping_addresses[i])
            LatLng=LatsLngs[i];
            $("#infoBoxesContent").html("");
            $("#infoBoxesContent").html(infoBoxes[i])
            $("#infoBoxesContent").css('left','0');
          });
        }
      }

      //×ボタンクリックでインフォ非表示
      $(document).on('click','#non-display',function(){
        $("#infoBoxesContent").css('left','-380px');
        setTimeout(function(){$("#infoBoxesContent").html("")},500);
        directionsDisplay.setMap(null);
      })

      //インフォを閉じるとき経路を削除する
      // $(document).on('click','.gm-ui-hover-effect',function(){
      //   directionsDisplay.setMap(null);
      // });

      //マップのズーム率変更に合わせてアイコンサイズを変更
      $(document).on('click',".gm-control-active",function(){
        //インかアウトか判定
        let zoomOption=$(this)[0].getAttribute("aria-label");
        zoom=map["zoom"];
        resizeMarker(zoom)
      })


      //ルートを壁画する処理
      function getRoute(LatLng,zoom,pick_up_address,shipping_address){
        // let result=""
        if(!(directionsDisplay=='')){
          directionsDisplay.setMap(null);
        }
        let geocoder = new google.maps.Geocoder();      // geocoderのコンストラクタ
        geocoder.geocode({address: shipping_address}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            let bounds = new google.maps.LatLngBounds();
            if (results[0].geometry) {
              // 緯度経度を取得
              shippingLatLng = results[0].geometry.location;
              let geocoder2 = new google.maps.Geocoder();
              geocoder2.geocode({address: pick_up_address}, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                  let bounds = new google.maps.LatLngBounds();
                  if (results[0].geometry) {
                    // 緯度経度を取得
                    pickLatLng=results[0].geometry.location;
                    let request = {
                      origin:LatLng,           //現在地
                      destination:shippingLatLng,//目的地
                      waypoints: [ // 経由地点(指定なしでも可)
                        { location: pickLatLng }
                      ],
                      travelMode: google.maps.DirectionsTravelMode.DRIVING, //ルートの種類
                    }
                    directionsService.route(request,function(result, status){
                      directionsDisplay = new google.maps.DirectionsRenderer();
                      directionsDisplay.setOptions({suppressMarkers: true,zoom:20});
                      directionsDisplay.setDirections(result); //取得した情報をset
                      console.log(result.routes[0].overview_path[0].lat())
                      console.log(result.routes[0].overview_path[0].lng())
                      directionsDisplay.setMap(map); //マップに描画
                      setTimeout(function(){
                        zoom=directionsDisplay["map"]["zoom"]
                        resizeMarker(zoom);
                      },500);
                    });
                  }
                }
              })
            }
          }
        })
      }

      //住所から緯度経度取得
      function getLatLangFromPlaceName(place){
        let result=''
        let geocoder = new google.maps.Geocoder();      // geocoderのコンストラクタ
        result=geocoder.geocode({address: place}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            let bounds = new google.maps.LatLngBounds();
            if (results[0].geometry) {
              // 緯度経度を取得
              result = results[0].geometry.location;
            }
          }
          return result;
        })
      }
      
      function resizeMarker(zoom){
        if(zoom > 19){
          if(device=='PC'){
            markerSize=64;
            markerIcon='./img/64×64.png';
          }else if(device=='SP'){
            markerSize=89;
            markerIcon='./img/80×80.png';
          }
        }else if(zoom > 16){
          if(device=='PC'){
            markerSize=48;
            markerIcon='./img/48×48.png';
          }else if(device=='SP'){
            markerSize=64;
            markerIcon='./img/64×64.png';
          }
        }else if(zoom > 12) {
          if(device=='PC'){
            markerSize=32;
            markerIcon='./img/32×32.png';
          }else if(device=='SP'){
            markerSize=48;
            markerIcon='./img/48×48.png';
          }
        }else if(zoom > 9){
          if(device=='PC'){
            markerSize=16;
            markerIcon='./img/16×16.png';
          }else if(device=='SP'){
            markerSize=32;
            markerIcon='./img/32×32.png';
          }
        }else{
          if(device=='PC'){
            markerSize=12;
            markerIcon='./img/12×12.png';
          }else if(device=='SP'){
            markerSize=16;
            markerIcon='./img/16×16.png';
          }
        }
        db.collection('task').get().then(function(querySnapshot){
          setMarker(querySnapshot);
        });
      }
    </script>
  </body>
</html>