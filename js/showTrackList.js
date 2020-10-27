showTrackList()

//一覧
function showTrackList(){
  output='';
  output+="<input id='add' type='button' value='新規追加' onclick=prepareAddTrack()>"
  output+="<ul id='track-data-list' class='track-lists'>"
  db.collection('track').where("user_id","==",user_id).get().then(function(querySnapshot){
    querySnapshot.docs.forEach(function(doc){
      const data=doc.data();
      output+="<li>"
      output+="<img src='./uploads/"+data.img_name+"' class='track-img'>"
      output+="<table class='track-info'>"
      output+="<tr><td>ナンバー:</td><td>"+data.track_number+"</tr>"
      output+="<tr><td>車種:</td><td>"+data.track_type+"</td></tr>"
      output+="<tr><td>最大積載:</td><td>"+data.max_weight+"t</td></tr>"
      output+="<tr><td>荷台長さ:</td><td>"+data.max_length+"m</td></tr>"
      output+="</table>"
      output+="</li>"
    })
    output+="</ul>"
    //console.log(output)
    $(".content-area").html(output);
  })
}

//登録準備
function prepareAddTrack(){
  output='';
  output+="<li class='track-add-area'>"
  output+="<div class='preview-area'>"
  output+="<img id='preview' class='preview'>"
  output+="<input type='file' id='trackImage'>"
  output+="</div>"
  output+="<table class='track-info'>"
  output+="<tr><td>ナンバー:</td><td><input type='text' id='track_number'></td></tr>"
  output+="<tr><td>車種:</td><td><select id='track_type'><option>平車</option><option>箱車</option><option>ユニック</option></select></td></tr>"
  output+="<tr><td>最大積載:</td><td><input type='text' id='max_weight'>t</td></tr>"
  output+="<tr><td>荷台長さ:</td><td><input type='text' id='max_length'>m</td></tr>"
  output+="<tr><td colspan=2><input type='submit' onclick='doAdd()' value='登録'></td></tr>"
  output+="</table>"
  output+="</li>"
  $("#track-data-list").prepend(output)
  $("#add").prop("disabled",true)
}

function doAdd(){
  let imgData=$('#preview').attr('src');
  let data={"imgData":imgData}
  $.ajax({
    type:"POST",
    url:"./controller/uploadImage.php",
    data:data,
    success:function(imgName){
      let track_number=$("#track_number").val();
      let track_type=$("#track_type").val();
      let max_weight=$("#max_weight").val();
      let max_length=$("#max_length").val();
      if(track_number=='' || track_type=='' || max_weight=='' || max_length==''){
        $("#msg").text('必須項目を入力してください');
        return false;
      }else{
        db.collection('track').add({
          track_number:track_number,
          track_type:track_type,
          max_weight:max_weight,
          max_length:max_length,
          img_name:imgName,
          user_id:user_id,
        });
        showTrackList()
      }
    },
    error:function(XMLHttpRequest,textStatus,errorThrown){
      alert(errorThrown);
    }
  });
}

//アップロードした画像を表示
$(document).on('change', '#trackImage',function (e) {
  var reader = new FileReader();
  reader.onload = function (e) {
    $("#preview").attr('src', e.target.result);
  }
  reader.readAsDataURL(e.target.files[0]);
})

//再選択かつキャンセル時バグ回避
$(document).on('click', '#trackImage',function (e) {
  $("#preview").attr('src', '');
})