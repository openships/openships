function doTest(){
  testFlag='on'
  writeWithOriginPoint()
  getDividedPoints()
  j=1
  intervalId=setInterval(function(){
    if(testFlag=='on'){
      rewriteWithNextPoint(j)
      j+=1
    }
  },2000)
  $("#testStart").prop("disabled",true)
  $("#testStop").prop("disabled",false)
}

function stopTest(){
  testFlag='off'
  writeWithOriginPoint()
  $("#testStart").prop("disabled",false)
  $("#testStop").prop("disabled",true)
  clearInterval(intervalId);
}

function writeWithOriginPoint(){
  db.collection('task').get().then(function(querySnapshot){
    querySnapshot.docs.forEach(function(doc){
      if(!(typeof doc.data().latlng=='undefined')){
        originLatLng=doc.data().originLatLng
        theDoc=db.collection('task').doc(doc.id)
        theDoc.update({
          latlng:originLatLng
        })
      }
    })
  })
}



function moveNextPoint(){
  db.collection('task').get().then(function(querySnapshot){
    querySnapshot.docs.forEach(function(doc){
      data=doc.data();
      if(!(typeof doc.data().latlng=='undefined')){
        latitude=data.latlng['Ic'];
        longitude=data.latlng['wc'];
        LatLng = new google.maps.LatLng(latitude, longitude);
        pick_up_address=data.pick_up_address
        shipping_address=data.shipping_address
        getNextPoint(LatLng,pick_up_address,shipping_address,doc)
      }
    })
  })
}


/*----------目的地までの分割された位置情報を取得-----------------------

1.緯度経度でルート検索し分割された位置情報を取得
2.taskのドキュメントIDをキーに分割された位置情報配列を保存→ローカルストレージ
-------------------------------------------------------------------*/

function getDividedPoints(){
  return new Promise((resolve, reject) => {
    setTimeout(() => {
      docIDList=[]
      pickLatLngList=[]
      shippingLatLngList=[]
      LatLngList=[]
      db.collection('task').get().then(function(querySnapshot){''
        querySnapshot.docs.forEach(function(doc){
          data=doc.data() 
          if(!(typeof doc.data().latlng=='undefined')){
            docID=doc.id
            docIDList.push(docID)
            lat=data.pick_up_latlng['Ic']
            lng=data.pick_up_latlng['wc']
            pickLatLng=new google.maps.LatLng(lat,lng);

            pickLatLngList.push(pickLatLng)

            lat=data.shipping_latlng['Ic']
            lng=data.shipping_latlng['wc']
            shippingLatLng = new google.maps.LatLng(lat,lng);

            shippingLatLngList.push(shippingLatLng)
          
            lat=data.latlng["Ic"]
            lng=data.latlng["wc"]
            LatLng = new google.maps.LatLng(lat,lng);

            LatLngList.push(LatLng)
          }
        })
      })
      data={"docIDList":docIDList,"LatLngList":LatLngList,"shippingLatLngList":shippingLatLngList,"pickLatLngList":pickLatLngList}
      resolve(data);
    }, 1000);
  }).then((data) => {
    return new Promise((resolve, reject) => {
      setTimeout(() => {
        // console.log(data)
        docIDList=data["docIDList"]
        LatLngList=data["LatLngList"]
        shippingLatLngList=data["shippingLatLngList"]
        pickLatLngList=data["pickLatLngList"]
        i=0
        docIDList.forEach(function(docID){
          let request = {
            origin:LatLngList[i],           //現在地
            destination:shippingLatLngList[i],//目的地
            waypoints: [ // 経由地点(指定なしでも可)
              { location: pickLatLngList[i] }
            ],
            travelMode: google.maps.DirectionsTravelMode.DRIVING, //ルートの種類
          }
          directionsService.route(request,function(result, status){
            localStorage.setItem(docID,JSON.stringify(result.routes[0]))
          })
          i+=1
        })
      },1000)
    })
  })
}

function rewriteWithNextPoint(j){
  docIDList.forEach(function(docID){
    data=JSON.parse(localStorage.getItem(docID))
    latitude=data["overview_path"][j]["lat"]
    longitude=data["overview_path"][j]["lng"]
    LatLng = new firebase.firestore.GeoPoint(latitude, longitude);
    theDoc=db.collection('task').doc(docID)
    theDoc.update({
       latlng:LatLng
    })
  })
}


// function getDividedPoints(){
//   return new Promise((resolve, reject) => {
//     keyList=[]
//     setTimeout(() => {
//       console.log("最初の処理（1秒後に実行）");
//       db.collection('task').get().then(function(querySnapshot){
//         querySnapshot.docs.forEach(function(doc){
//           data=doc.data();
//           docID=doc.id
//           latitude=data.latlng['Ic'];
//           longitude=data.latlng['wc'];
//           LatLng = new google.maps.LatLng(latitude, longitude);
//           pick_up_address=data.pick_up_address
//           shipping_address=data.shipping_address
//           dataList={
//             "集荷先住所":pick_up_address,
//             "配送先住所":shipping_address,
//             "トラック位置":LatLng,
//             // "トラック経度"
//           }
//           keyList.push(doc.id)
//           localStorage.setItem(docID,JSON.stringify(dataList));
//         })
//       })
//       resolve(keyList);
//     }, 1000);
//   }).then((keyList) => {
//     return new Promise((resolve, reject) => {
//      setTimeout(() => {
//       console.log("2番目の処理（2秒後に実行）");
//       //console.log(keyList)
//       keyList.forEach(function(key){
//         jsonData=localStorage.getItem(key);
//         data=JSON.parse(jsonData);
//         pick_up_address=data['集荷先住所']
//         let geocoder = new google.maps.Geocoder();      // geocoderのコンストラクタ
//         geocoder.geocode({address: pick_up_address}, function(results, status) {    
//           if (status == google.maps.GeocoderStatus.OK) {
//             if (results[0].geometry) {
//               pickupLatLng = results[0].geometry.location;
//               dataList = JSON.parse(localStorage.getItem(key));
//               dataList["集荷先位置"] =  pickupLatLng;
//               localStorage.setItem(key, JSON.stringify(dataList));
//             }
//           }
//         })
//       })
//       resolve(keyList)
//      }, 1000);
//     })
    
//    }).then((keyList) => {
//     return new Promise((resolve, reject) => {
//      setTimeout(() => {
//       console.log("3番目の処理（3秒後に実行）");
//       keyList.forEach(function(key){
//         jsonData=localStorage.getItem(key);
//         data=JSON.parse(jsonData);
//         shipping_address=data['配送先住所']
//         let geocoder2 = new google.maps.Geocoder();      // geocoderのコンストラクタ
//         geocoder2.geocode({address: shipping_address}, function(results, status) {
//           console.log(status)
//           console.log(results)
//           if (status == google.maps.GeocoderStatus.OK) {
//             if (results[0].geometry) {
//               shippingLatLng = results[0].geometry.location;
//               dataList = JSON.parse(localStorage.getItem(key));
//               dataList["配送先位置"] =  shippingLatLng;
//               localStorage.setItem(key, JSON.stringify(dataList));
//             }
//           }
//         })
//       })
//       resolve();
//      },10000);
//     })
//    })
//   }

//   let geocoder = new google.maps.Geocoder();      // geocoderのコンストラクタ
//   geocoder.geocode({address: shipping_address}, function(results, status) {
//     if (status == google.maps.GeocoderStatus.OK) {
//       if (results[0].geometry) {
//         // 緯度経度を取得
//         shippingLatLng = results[0].geometry.location;
//         let geocoder2 = new google.maps.Geocoder();
//         geocoder2.geocode({address: pick_up_address}, function(results, status) {
//           if (status == google.maps.GeocoderStatus.OK) {
//             console.log("a")
//             if (results[0].geometry) {
//               // 緯度経度を取得
//               pickLatLng=results[0].geometry.location;
//               let request = {
//                 origin:LatLng,           //現在地
//                 destination:shippingLatLng,//目的地
//                 waypoints: [ // 経由地点(指定なしでも可)
//                   { location: pickLatLng }
//                 ],
//                 travelMode: google.maps.DirectionsTravelMode.DRIVING, //ルートの種類
//               }
//               directionsService.route(request,function(result, status){
//                 latitude=result.routes[0].overview_path[1].lat()
//                 longitude=result.routes[0].overview_path[1].lng()
//                 previousLat=LatLng.lat()
//                 previousLng=LatLng.lng()
//                 // if((latitude-previousLat) > 0.001 || (Math.sign(latitude-previousLat)==-1 && (latitude-previousLat)< -0.001)){
//                 //   fixedLat=previousLat+((latitude-previousLat)/2)
//                 // }else{
//                 //   fixedLat=latitude
//                 // }
//                 // if((longitude-previousLng) > 0.001 || (Math.sign(longitude-previousLng)==-1 && (longitude-previousLng)< -0.001)){
//                 //   fixedLng=previousLng+((longitude-previousLng)/2)
//                 // }else{
//                 //   fixedLng=longitude
//                 // }
//                 // console.log(fixedLng)
//                 // console.log(fixedLng)
//                 // LatLng = new firebase.firestore.GeoPoint(fixedLat, fixedLng);
//                 LatLng = new firebase.firestore.GeoPoint(latitude, longitude);
//                 rewriteWithNextPoint(LatLng,doc)
//               });
//             }
//           }
//         })
//       }
//     }
//   })
// }

// function rewriteWithNextPoint(LatLng,doc){
//   // /console.log(doc.id)
//   //console.log(LatLng)
//   theDoc=db.collection('task').doc(doc.id)
//   theDoc.update({
//     latlng:LatLng
//   })
// }


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