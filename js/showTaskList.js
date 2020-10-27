showTaskList()

function showTaskList(){
  db.collection('task').where("user_id","==",user_id).get().then(function(querySnapshot){
    output='';
    output+="<input id='add' type='button' value='新規追加' onclick=prepareAddTask()>"
    output+="<table id='task-data-list'>"
    output+="<tr id='tb-head'><th>集荷先</th><th>配送先</th><th>集荷時間</th><th>配送時間</th></tr>"
    querySnapshot.docs.forEach(function(doc){
      const data=doc.data();
      if(data.user_id==user_id){
        //const data=doc.data();
        
        output+="<tr>"
        output+="<td>"+data.pick_up_address+"</td>";
        output+="<td>"+data.shipping_address+"</td>";
        output+="<td>"+data.pick_up_time+"</td>";
        output+="<td>"+data.shipping_time+"</td>";
        output+="<td class='"+data.track_img+" hover-triger '>"+data.track_number+"</td>"
        output+="<td>";
        output+="<form action='sendPoint.php' method='POST'>"
        output+="<input type='hidden' name='doc_id' value='"+doc.id+"'>"
        output+="<input type='submit' value='出発する'>";
        output+="</form>"
        output+="</td>";
        output+="</tr>"
      }
    });
    output+="</table>"
    console.log(output)
    $(".content-area").html(output);
  });
}

//登録準備
function prepareAddTask(){
  options=''
  db.collection('track').where("user_id","==",user_id).get().then(function(querySnapshot){
    querySnapshot.docs.forEach(function(doc){
      data=doc.data()
      track_number=data.track_number
      imgName=data.img_name
      options+='<li class="'+imgName+' hover-triger">'+track_number+'</li>'      
    })
    console.log(options)
    output='';
    output+='<tr id="add-area">'
    output+='<td><input type="text" name="pick-up-adress" class="place-input" id="pick-up-address"></td>'
    output+='<td><input type="text" name="shipping-address" class="place-input" id="shipping-address"></td>'
    output+='<td><input type="datetime-local" name="pick-up-time" id="pick-up-time" class="datetimes"></td>'
    output+='<td><input type="datetime-local" name="shipping-time" id="shipping-time" class="datetimes"></td>'
    // output+='<td><select class="track-select">'+options+'</select></td>'
    output+='<td><div id="track-select" class="dropdown">選択してください</div><ul class="dropdwn_menu">'+options+'</ul></td>'
    output+='<td><button id="submit" onclick="doAdd()">登録</button></td>'
    output+='</tr>'
    $("#tb-head").after(output)
    $("#add").prop("disabled",true)
  })
}


//登録処理
function doAdd(){
  let shipping_address=$("#shipping-address").val();
  let pick_up_address=$("#pick-up-address").val();
  let shipping_time=$("#shipping-time").val();
  let pick_up_time=$("#pick-up-time").val();
 // console.log($("#track-select").find("li").text())
  let track_number=$("#track-select li").text()
  let track_img=$("#track-select li").attr("class").replace('hover-triger','')
  

  // let user_id="<?=$user_id?>"
  if(shipping_address=='' || pick_up_address=='' || shipping_time=='' || pick_up_time==''){
    $("#msg").text('必須項目を入力してください');
    return false;
  }else{
    db.collection('user').where("user_id","==",user_id).get().then(function(querySnapshot){
      doc=querySnapshot.docs[0]
      data=doc.data()
      tel=data.tel
      tel=data.url
      db.collection('task').add({
        shipping_address:shipping_address,
        pick_up_address:pick_up_address,
        shipping_time:shipping_time,
        pick_up_time:pick_up_time,
        user_id:user_id,
        track_number:track_number,
        track_img:track_img,
        tel:tel,
        url:url,
        running_status:'off',
      });
      //setTimeout(function(){location.href="./mypage.php"},500);
      showTaskList()
    })
  }
}

//トラックのセレクトボックスホバー時写真出す
// $(document).on('mouseenter','select:focus option:checked',function(e){
//   console.log(e)
//   let $target = $(e); 
//   if($target.is('option')){
//     console.log('ほばー')
//     alert($target.text());//Will alert the text of the option
//   }
// })

//自作セレクトボックス
$(document).on('click','.dropdown',function(){
  $("ul.dropdwn_menu").show()
});

//トラックのセレクトボックスホバー時写真出す
$(document).on('mouseenter','.hover-triger',function(){
  console.log($(this).attr('class'))
  src=$(this).attr('class').replace('hover-triger','')
  imgTag="<img src='./uploads/"+src+"'>"
  $(".track-pic").html(imgTag)
})

//トラックのセレクトオプションカーソルアウト時写真消す
$(document).on('mouseleave','.hover-triger',function(){
  $(".track-pic").html("")
})

//トラックのセレクト選択時ボックスに選択した値を入れる
$(document).on('click','.dropdwn_menu li',function(){
  thiss=$(this).clone()
  $(".dropdown").html(thiss)
  $("ul.dropdwn_menu").hide()
})