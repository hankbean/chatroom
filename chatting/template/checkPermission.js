  /*
  * 檢查權限
  **/

function checkPermission(){
    var username = $.cookie("username");
    var token = $.cookie("token");
    var params = "username="+username+"&token="+token;
    $.ajax({
            url: './api/getPermission.php',
            type:"post",
            data: params,
            success: function(data){
               var data = JSON.parse(data);
               if(data.status != "success"){
                  alert('請登入!');
                  window.location.href = "./login.html";
               }
          }
    });
}

checkPermission();