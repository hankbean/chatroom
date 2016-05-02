/**
* 引入檔案
*/
var config = require('./config.json');
var User = require('./user.js'); 
var xss = require('xss');
var mysql = require('mysql');

/**
*Mysql 建立連線
**/
var connection = mysql.createConnection({
    host: config.host,
    user: config.user,
    password: config.password,
    database: config.database
});

/**
* 全域變數
*/
var user = [];

/*
* sokcet 基本設定
*
*/
var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);

server.listen(config.port);
console.log('server port \''+ config.port +'\' has been Start!');

app.get('/', function (req, res) {
  res.sendfile(__dirname + '/index.html');
});

/**
* 連線可以執行的函式
*/

io.on('connection', function (socket) {

  socket.emit('news', { hello: 'world' });
 
  socket.on('my other event', function (data) {
    console.log(data);
  });

  socket.on('new user', function(_newuser){
    try{
        //不存在此使用者才給加入
        if(!checkUserExist(_newuser.id)){
           addUser(_newuser.id,_newuser.name,socket);
           updateOnlineUser(_newuser.id,socket);
        }
    }
    catch(e){
      console.log('new user create error');
    }
  });

  socket.on('chat message', function(_message){
    try{
       sendMessage(_message.id ,_message.recv , xss(_message.msg) , socket);
    }
    catch(e){
      console.log('chat message send error');
    }
   });

  socket.on('chat onlineUser', function(_onlineUser){
    try{
       updateOnlineUser(socket);
    }
    catch(e){
      console.log('chat message send error');
    }
   });

  socket.on('disconnect', function () {
      //離線清空該使用者
      cleanUser(getUserIndexFormSokcet(socket));
      //傳遞更新目前人數到其他上線的使用者上
      updateOnlineUser('NULL',socket);

      io.emit('user disconnected');
  });
});

/**
* 增加user使用者
*/
function addUser(_userId,_userName,_socket){
  var obj = new User(); 
  obj.setId(_userId); 
  obj.setName(_userName);
  obj.setSocket(_socket);
  user.push(obj);
  console.log("[Login]ID:"+obj.getId()+"- Name:"+obj.getName());
}


/**
* 傳送訊息
*/
function sendMessage(_id,_recv,_msg,_mainSocket){

 var sendIndex = getUserIndex(_id);

  if(_recv === "1"){
      var obj = new Object();
      obj.id = user[sendIndex].getId();
      obj.name = user[sendIndex].getName();
      obj.recv= _recv;
      obj.msg = _msg;
      obj.time = getTime();
      //傳給每個人除了自己之外
      _mainSocket.broadcast.emit('chat message',obj);
      //加入資料到mysql
      mysqldataMsgInsert(obj.id,obj.recv,obj.msg,obj.time);
      console.log(obj);
  }
  else{
      var recvIndex = getUserIndex(_recv);
      //傳給特定的人
      var obj = new Object();
      obj.id = user[sendIndex].getId();
      obj.name = user[sendIndex].getName();
      obj.recv= _recv;
      obj.msg = _msg;
      obj.time = getTime();
      user[recvIndex].getSocket().emit('chat message',obj);
      //加入資料到mysql
      mysqldataMsgInsert(obj.id,obj.recv,obj.msg,obj.time);
      console.log(obj);
  }

}

/**
* 給id回傳user index
*/
function getUserIndex(_id){

  for(var i=0;i<user.length;i++){
    if(user[i].getId() === _id)
      return i;
  }

  return 'getUserIndex:error';
}

/**
* 給socket回傳user index
*/
function getUserIndexFormSokcet(_socket){

  for(var i=0;i<user.length;i++){
    if(user[i].getSocket() === _socket)
      return i;
  }

  return 'getUserIndexFormSokcet:error';
}
/**
* 取得當前時間
*/

function getTime(){
  var timeDate= new Date();
  var tMonth = (timeDate.getMonth()+1) > 9 ? (timeDate.getMonth()+1) : '0'+(timeDate.getMonth()+1);
  var tDate = timeDate.getDate() > 9 ? timeDate.getDate() : '0'+timeDate.getDate();
  var tHours = timeDate.getHours() > 9 ? timeDate.getHours() : '0'+timeDate.getHours();
  var tMinutes = timeDate.getMinutes() > 9 ? timeDate.getMinutes() : '0'+timeDate.getMinutes();
  var tSeconds = timeDate.getSeconds() > 9 ? timeDate.getSeconds() : '0'+timeDate.getSeconds();
  return timeDate= timeDate.getFullYear()+'-'+ tMonth +'-'+ tDate +' '+ tHours +':'+ tMinutes +':'+ tSeconds;
}



/**
* 更新在線的使用者列表
*/
function updateOnlineUser(_id,_mainSocket){

  if(_id === 'NULL'){
    //離線
    var objUser = [];

      for(var i=0;i<user.length;i++){
        try{
          if(user[i].getId()!=='NULL'){
            var obj = new Object();
            obj.id = user[i].getId();
            obj.name = user[i].getName();
            objUser.push(obj);
          }
        }
        catch(e){
            console.log("append error");
        }
      }
      //廣播給除了自己以外的人
      _mainSocket.broadcast.emit('chat onlineUser',objUser);
      console.log("目前線上人數:"+objUser.length);
  }
  else
  {
    //上線
      var objUser = [];

      for(var i=0;i<user.length;i++){
        try{
          if(user[i].getId()!== 'NULL' && user[i].getName()!=='NULL'){
            var obj = new Object();
            obj.id = user[i].getId();
            obj.name = user[i].getName();
            objUser.push(obj);
          }
        }
        catch(e){
            console.log("append error");
        }
      }
      //廣播給自己
      user[getUserIndex(_id)].getSocket().emit('chat onlineUser',objUser);
      //廣播給除了自己以外的人
      _mainSocket.broadcast.emit('chat onlineUser',objUser);
        console.log("目前線上人數:"+objUser.length);
  }
}

/**
* 清除離線使用者
*/
function cleanUser(_index){
  try
  {
    console.log("[Offline]ID:"+user[_index].getId()+"- Name:"+user[_index].getName());
    user[_index].setId('NULL');
    user[_index].setName('NULL');
    user[_index].setSocket('NULL');
  }
  catch(e){

  }
}

/**
* 檢查是否有此使用者
*/
function checkUserExist(_id){
  if(user.length == 0){
    return false; 
  }
  try
  {
     for(var i=0;i<user.length;i++){
        if(user[i].getId() === _id){
          return true;
        }
     }
  }
  catch(e){
    return true;
  }
  return false;
}


/**
* 將資料insert mysql資料庫
*/
function mysqldataMsgInsert(_id,_recv,_msg,_time){
  var post  = {from_id: _id, to_id: _recv,msg: _msg,time:_time};
  var query = connection.query('INSERT INTO chat SET ?', post, function(err, result) {
      //假如要顯示insert結果在server 可以拿掉下面兩行
      //console.log(err);
      //console.log(result);
  });
}
