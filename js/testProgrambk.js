function doTest(){
  testFlag='on'
  writeWithOriginPoint()
  // saveOriginPosition()
  setInterval(function(){
    if(testFlag=='on'){
      moveNextPoint()
    }
  },10000);
  $("#testStart").prop("disabled",true)
  $("#testStop").prop("disabled",false)
}

function stopTest(){
  testFlag='off'
  writeWithOriginPoint()
  $("#testStart").prop("disabled",false)
  $("#testStop").prop("disabled",true)
}

function writeWithOriginPoint(){
  db.collection('task').get().then(function(querySnapshot){
    querySnapshot.docs.forEach(function(doc){
      originLatLng=doc.data().originLatLng
      theDoc=db.collection('task').doc(doc.id)
      theDoc.update({
        latlng:originLatLng
      })
    })
  })
}



function moveNextPoint(){
  db.collection('task').get().then(function(querySnapshot){
    querySnapshot.docs.forEach(function(doc){
      data=doc.data();
      latitude=data.latlng['Ic'];
     
      longitude=data.latlng['wc'];
      LatLng = new google.maps.LatLng(latitude, longitude);
      pick_up_address=data.pick_up_address
      shipping_address=data.shipping_address
      getNextPoint(LatLng,pick_up_address,shipping_address,doc)
    })
  })
}

function getNextPoint(LatLng,pick_up_address,shipping_address,doc){
  let geocoder = new google.maps.Geocoder();      // geocoderのコンストラクタ
  geocoder.geocode({address: shipping_address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
     
      if (results[0].geometry) {
        // 緯度経度を取得
        shippingLatLng = results[0].geometry.location;
        let geocoder2 = new google.maps.Geocoder();
        geocoder2.geocode({address: pick_up_address}, function(results, status) {
         
          if (status == google.maps.GeocoderStatus.OK) {
            console.log("a")
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
                latitude=result.routes[0].overview_path[1].lat()
                longitude=result.routes[0].overview_path[1].lng()
                previousLat=LatLng.lat()
                previousLng=LatLng.lng()
                
               
                // if((latitude-previousLat) > 0.001 || (Math.sign(latitude-previousLat)==-1 && (latitude-previousLat)< -0.001)){
                //   fixedLat=previousLat+((latitude-previousLat)/2)
                // }else{
                //   fixedLat=latitude
                // }
                // if((longitude-previousLng) > 0.001 || (Math.sign(longitude-previousLng)==-1 && (longitude-previousLng)< -0.001)){
                //   fixedLng=previousLng+((longitude-previousLng)/2)
                // }else{
                //   fixedLng=longitude
                // }
                // console.log(fixedLng)
                // console.log(fixedLng)
                // LatLng = new firebase.firestore.GeoPoint(fixedLat, fixedLng);

                
                LatLng = new firebase.firestore.GeoPoint(latitude, longitude);
                rewriteWithNextPoint(LatLng,doc)
              });
            }
          }
        })
      }
    }
  })
}

function rewriteWithNextPoint(LatLng,doc){
  // /console.log(doc.id)
  //console.log(LatLng)
  theDoc=db.collection('task').doc(doc.id)
  theDoc.update({
    latlng:LatLng
  })
}


// function saveOriginPosition(){
//   db.collection('task').get().then(function(querySnapshot){
//     querySnapshot.docs.forEach(function(doc){
//       // console.log(doc.id)
//       data=doc.data();
//       latitude=data.latlng['Ic'];
//       longitude=data.latlng['wc'];
//       LatLng = new firebase.firestore.GeoPoint(latitude, longitude);
//       theDoc=db.collection('task').doc(doc.id)
//       theDoc.update({
//         originLatLng:LatLng
//       })
//     })
//   })
// }



$(window).on('beforeunload', function(event) {
  stopTest()
  return 'jquery beforeunload';
});