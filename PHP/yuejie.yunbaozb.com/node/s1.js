
//引入http模块
var socketio = require('socket.io'),
	fs 	= require('fs'),
	https     = require('http'),
	domain   = require('domain'),
	redis    = require('redis'),
    redisio  = require('socket.io-redis'),
    request  = require('request'),
    md5  = require('md5-node'),
    config   = require('./config.js');

var d = domain.create();
d.on("error", function(err) {
	//console.log(err);
});
 // var options = {
 //     key: fs.readFileSync('/usr/local/nginx/conf/ssl/livenewss.yunbaozb.com.key'),
 //     cert: fs.readFileSync('/usr/local/nginx/conf/ssl/livenewss.yunbaozb.com.crt')
   // }; 
//var numscount=0;// 在线人数统计
var sockets = {};
var chat_history={};
var chat_interval={};

var turnTalk_interval={};
var turnTalk_now_uid={};
var turnTalk_now_sitid={};


// redis 链接
var clientRedis  = redis.createClient(config['REDISPORT'],config['REDISHOST']);
clientRedis.auth(config['REDISPASS']);
//var server = https.createServer(options,function(req, res) {
var server = https.createServer(function(req, res) {
	res.writeHead(200, {
		'Content-type': 'text/html;charset=utf-8'
	});
   //res.write("人数: " + numscount );
	res.end();
}).listen(config['socket_port'], function() {
	////console.log('服务开启19965');
});

var io = socketio.listen(server,{
	pingTimeout: 60000,
  	pingInterval: 25000
});
/* var pub = redis.createClient(config['REDISPORT'], config['REDISHOST'], { auth_pass: config['REDISPASS'] });
 var sub = redis.createClient(config['REDISPORT'], config['REDISHOST'], { auth_pass: config['REDISPASS'] });
 io.adapter(redisio({ pubClient: pub, subClient: sub })); */
//setInterval(function(){
  //global.gc();
  ////console.log('GC done')
//}, 1000*30); 

io.on('connection', function(socket) {
	//console.log('连接成功');
	//numscount++;
							
	var interval;

	//进入房间
	socket.on('conn', function(data) {
		
		if(!data || !data.uid || !data.token || !data.roomnum || !data.stream ){
            socket.emit('conn',['no']);
            return !1;
		}
		
		userid=data.uid;
		old_socket = sockets[userid];
		if (old_socket && old_socket != socket) {
			
			if(data.uid != data.roomnum && data.uid==old_socket.roomnum){
                /* 进房间 但旧链接是 主播 */
                var data_str='{"retmsg":"ok","retcode":"000000","msg":[{"_method_":"StartEndLive","action":"19","ct":"直播关闭"}]}';
				old_socket.emit('broadcastingListen',[data_str]);
			}else if(data.uid== data.roomnum && data.stream==old_socket.stream){
                /* 主播重连 */
				old_socket.reusing = 1;
				//console.log("重用");
			}else if(data.uid== data.roomnum && data.stream!=old_socket.stream){
                /* 主播多端开播 */
				var data_str='{"retmsg":"ok","retcode":"000000","msg":[{"_method_":"StartEndLive","action":"19","ct":"直播关闭"}]}';
				old_socket.emit('broadcastingListen',[data_str]);
			}
			old_socket.disconnect()
		}
		
		clientRedis.get(data.token,function(error,res){
			if(error){
				return;
			}else if(res==null){
				//console.log("[获取token失败]"+data.uid);
			}else{
				if(res != null){
					
					var userInfo = evalJson(res);
					if(userInfo['id'] == data.uid ){
						//console.log("[初始化验证成功]--"+data.uid+"---"+data.roomnum+'---'+data.stream);
						//获取验证token
						socket.token   = data.token; 
						socket.roomnum = data.roomnum;
						socket.stream = data.stream;
						socket.nicename = userInfo['user_nickname'];
						socket.avatar = userInfo['avatar'];
						socket.sex = userInfo['sex'];
						socket.age = userInfo['age'];
						socket.livetype = userInfo['livetype'];
						socket.sign = Number(userInfo['sign']);
						socket.usertype   = Number(userInfo['usertype']);
						socket.uid     = data.uid;
						socket.reusing = 0;
						
						socket.join(data.roomnum);
						sockets[userid] = socket;
						socket.emit('conn',['ok']);
                        
						if( socket.roomnum!=socket.uid && socket.uid >0 ){
							var data_obj={
                                            "msg":[
                                                {
                                                    "_method_":"enter",
                                                    "action":"0",
                                                    "ct":'',
                                                    "uid":''+userInfo['id'],
                                                    "uname":''+userInfo['user_nickname'],
                                                    "avatar":userInfo['avatar'],
                                                    "sex":userInfo['sex'],
                                                    "age":userInfo['age']
                                                }
                                            ],
                                            "retcode":"000000",
                                            "retmsg":"OK"
                                        };
							process_msg(io,socket.roomnum,JSON.stringify(data_obj));
							if(socket.stream){
								clientRedis.zadd('user_'+socket.stream,socket.sign,userInfo['id']);	
                                var sign_data={uid:socket.uid,token:socket.token,liveuid:socket.roomnum,stream:socket.stream,type:1};
                                var sign=setSign(sign_data);
                                
                                request(config['WEBADDRESS']+"?service=Live.upnums&uid="+socket.uid + "&token=" + socket.token+ "&liveuid=" + socket.roomnum+ "&type=1" + "&stream=" + socket.stream+ "&sign=" + sign,function(error, response, body){
                                    // console.log('upnums');
                                    // console.log(body);
                                });
							}
						}						
						 
						sendSystemMsg(socket,"直播内容包含任何低俗、暴露和涉黄内容，账号会被封禁；安全部门会24小时巡查哦～");
						return;
					}else{
						socket.disconnect();
					}
				}
			}
			
			socket.emit('conn',['no']);
		});
        
		
	});

	socket.on('broadcast',function(data){
            // console.log(data);
		    if(socket.token != undefined){
		    	var dataObj  = typeof data == 'object'?data:evalJson(data);
			    var msg      = dataObj['msg'][0]; 
				var method   = msg['_method_'];
			    var action   = msg['action'];
			    var data_str =  typeof data == 'object'?JSON.stringify(data):data;
			    switch(method){
			    	case 'SendMsg':{     //聊天     
                        process_msg(io,socket.roomnum,data_str);
						
			    		break;
			    	}
			    	case 'SendGift':{    //送礼物
						var gifToken = msg['ct'];
                        // console.log('送礼物');
                        // console.log(gifToken)
			    		clientRedis.get(gifToken,function(error,res){
                            // console.log(res);
			    			if(!error&&res != null){
			    				var resObj = evalJson(res);
                                
                                //console.log('dataObj');
                                //console.log(dataObj);
                                
                                var len=resObj.length;
                                // console.log(len);
                                for(var i=0;i<len;i++){
                                    dataObj['msg'][0]['ct']=resObj[i];
                                    // console.log(dataObj);
                                    io.sockets.in(socket.roomnum).emit('broadcastingListen',[JSON.stringify(dataObj)]);
                                }
			    				//dataObj['msg'][0]['ct'] = resObj;
                                
                                //console.log('dataObj');
                                //console.log(dataObj);

								//io.sockets.in(socket.roomnum).emit('broadcastingListen',[JSON.stringify(dataObj)]);
                                
			    				clientRedis.del(gifToken);
			    			}
			    		});
			    		break;
			    	}

					case 'updateVotes' :{//更新映票
						process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}
                    case 'dispatch' :{//派单
                        if(socket.usertype == 50 ){
                            process_msg(io,socket.roomnum,data_str);
                        }
	                    break;
			    	}
			    	case 'CloseLive' :{//关闭直播
			    		if(socket.usertype == 50 ){
							process_msg(io,socket.roomnum,data_str);
			    	    }
	                    break;
			    	}
                    
                    case 'controlmic' :{//控麦  action 1开麦2闭麦
			    		if(socket.usertype == 50 ){
							process_msg(io,socket.roomnum,data_str);
			    	    }
	                    break;
			    	}
                    
                    case 'talk' :{//是否语音  action 0否1是
                        process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}
                    
                    case 'linkmic' :{//聊天室连麦相关
						/* 
						派单： action:  1老板申请  2老板取消申请 3控制老板上麦 4控制下麦 5主动下麦 6上麦抢单 7拒绝老板上麦
						交友： action:  1申请 2取消申请3控制上麦4控制下麦5主动下麦 7拒绝
						闲谈：action:  1申请2取消申请3控制上麦4控制下麦5主动下麦  7拒绝
						点歌：action:  1老板申请2老板取消申请3控制老板上麦4控制下麦5主动下麦   7拒绝老板  8歌手申请 9 拒绝歌手 10 控制歌手上麦 
						*/
						// console.log('linkmic');
						// console.log(data_str);
						var livetype=socket.livetype;
                        if(action==1){
                            process_msg(io,socket.roomnum,data_str);
                        }
                        
                        if(action==2){
                            process_msg(io,socket.roomnum,data_str);
                        }
                        
                        if(action==3 && socket.usertype == 50){
                            process_msg(io,socket.roomnum,data_str);
                        }
                        
                        if(action==4 && socket.usertype == 50){
                            // console.log('控制下麦');
                            var touid=msg['touid'];
                            var toname=msg['toname'];
                            downMic(io,socket.roomnum,touid,toname,livetype);
                            
                        }
                        
                        if(action==5){
							// console.log('下麦 5');
                            downMic(io,socket.roomnum,socket.uid,socket.nicename,livetype);
                        }
                        
                        if(action==6){
							if(livetype==1){
								process_msg(io,socket.roomnum,data_str);
							}
                            
                        }
                        
                        if(action==7  && socket.usertype == 50){
							
							if(livetype==1 || livetype==4){
								/* 老板位 */
								var touid=msg['touid'];
								clientRedis.zrem('boss_'+socket.roomnum,touid,function(error,res){
									process_msg(io,socket.roomnum,data_str);
								})
							}else if(livetype==2 ){
								/* 交友申请 */
								var touid=msg['touid'];
								clientRedis.zrem('jy_'+socket.roomnum+'_1',touid,function(error,res){
									process_msg(io,socket.roomnum,data_str);
								})
								
								clientRedis.zrem('jy_'+socket.roomnum+'_2',touid,function(error,res){
									process_msg(io,socket.roomnum,data_str);
								})
								
							}else if(livetype==3 ){
								/* 闲谈申请 */
								var touid=msg['touid'];
								clientRedis.zrem('chat_'+socket.roomnum,touid,function(error,res){
									process_msg(io,socket.roomnum,data_str);
								})
								
								
							}
                            
                        }
						
						if(action==8){
                            process_msg(io,socket.roomnum,data_str);
                        }
                        
                        if(action==9 && socket.usertype == 50){
                            process_msg(io,socket.roomnum,data_str);
                        }
						
						if(action==10 && socket.usertype == 50){
                            process_msg(io,socket.roomnum,data_str);
                        }
						
						
                        //process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}
                    
                    case 'turnTalk' :{//轮流发言  action  1开始2取消3发言4跳过5完毕
                        if(action==1 && socket.usertype == 50){
                            // console.log('turnTalk-1-data_str');
                            // console.log(data_str);
                            io.sockets.in(socket.roomnum).emit("broadcastingListen", [data_str]);
                            //process_msg(io,socket.roomnum,data_str);
                            
                            turnTalk_now_sitid[socket.roomnum]=0;
                            turnTalk_now_uid[socket.roomnum]=0;
                            turnTalk(io,socket.roomnum);
                        }
                        
                        if(action==2 && socket.usertype == 50){
                            io.sockets.in(socket.roomnum).emit("broadcastingListen", [data_str]);
                            //process_msg(io,socket.roomnum,data_str);
                            
                            if(turnTalk_interval[socket.roomnum]){
                                clearTimeout(turnTalk_interval[socket.roomnum]);
                                turnTalk_interval[socket.roomnum]=null;
                            }
                        }
                        
                        if(action==4){
                            var now_uid=turnTalk_now_uid[socket.roomnum]?turnTalk_now_uid[socket.roomnum]:0;
                            if(now_uid==socket.uid){
                                turnTalk(io,socket.roomnum);
                            }
                            
                        }
                        //process_msg(io,socket.roomnum,data_str);
	                    break;
			    	}
					
					case 'controlLink' :{//交友-环节控制  action   2心动选择 3 公布心动  4下一场
						var livetype=socket.livetype;
						var usertype=socket.usertype;
						var roomnum=socket.roomnum;
						if(livetype!=2 || usertype!=50){
							return !1;
						}
                        if(action==1){
							clientRedis.hset('jy_step',roomnum,1);
                            process_msg(io,roomnum,data_str);
                        }
                        
                        if(action==2){
							clientRedis.hset('jy_step',roomnum,2);
							var time=Math.floor(Date.parse(new Date())/1000);
							clientRedis.hset('jy_step_time',roomnum,time);
                            process_msg(io,roomnum,data_str);
                        }
                        
                        if(action==3){
							clientRedis.hset('jy_step',roomnum,3);
							request(config['WEBADDRESS']+"?service=Linkmic.getHeart&uid="+socket.uid + "&token=" + socket.token,function(error, response, body){
								if(error) return;
								var res = evalJson(body);
								if(response.statusCode != 200 && res.data.code != 0){
									return;
								}
								
								var data_obj={
									"msg":[
										{
											"_method_":"controlLink",
											"action":"3",
											"ct":'',
											"heart":res.data.info[0].heart,
											"hand":res.data.info[0].hand
										}
									],
									"retcode":"000000",
									"retmsg":"OK"
								};
								process_msg(io,roomnum,JSON.stringify(data_obj));
							});
                            //process_msg(io,roomnum,data_str);
                        }
                        
                        if(action==4){
							clearSit(roomnum,livetype);
							clientRedis.hset('jy_step',roomnum,1);
                            process_msg(io,roomnum,data_str);
                            
                        }

	                    break;
			    	}
					
			    	case 'StartEndLive':{
			    		if(socket.usertype == 50 ){
			    		   socket.broadcast.to(roomnum).emit('broadcastingListen',[data_str]);
			    	    }
			    	    break;

			    	}
			    	
						
			    }
		    }
		    
	});
	
	socket.on('superadminaction',function(data){
    	if(data['token'] == config['TOKEN']){
            io.sockets.in(data['roomnum']).emit("broadcastingListen", ['stopplay']);
    	}
    });
	/* 系统信息 */
	socket.on('systemadmin',function(data){
    	if(data['token'] == config['TOKEN']){
            var data_obj={
                            "msg":[
                                {
                                    "_method_":"SystemNot",
                                    "action":"1",
                                    "ct":''+ data.content
                                }
                            ],
                            "retcode":"000000",
                            "retmsg":"OK"
                        };
    		io.emit('broadcastingListen',[JSON.stringify(data_obj)]);
    	}
    });
	/* 后台：删除关闭聊天室 */
	socket.on('systemcloselive',function(data){
		if(data['token'] == config['TOKEN']){
			request(config['WEBADDRESS']+"?service=Live.stop&uid="+data['uid'] + "&token=" + data['token']+ "&stream=" + data['stream'],function(error, response, body){
				var data_obj={
							"retmsg":"ok",
							"retcode":"000000",
							"msg":[
								{
									"_method_":"StartEndLive",
									"action":"18",
									"ct":"直播关闭"
								}
							]
						};
				io.sockets.in(data['uid']).emit("broadcastingListen", [JSON.stringify(data_obj)]);
				clientRedis.del('user_'+data['stream']);
				socket.leave(data['stream']);
				delete io.sockets.sockets[socket.id];
				sockets[data['uid']] = null;
				delete sockets[data['uid']];
			});
		}
    });
	
    //资源释放
	socket.on('disconnect', function() { 
			/* numscount--; 
            if(numscount<0){
				numscount=0;
			}   */
            // console.log('disconnect');
            // console.log(socket.uid);
            // console.log(socket.roomnum);
            // console.log(socket.token);
            // console.log(socket.stream);
			if(socket.roomnum ==null || socket.token==null || socket.uid <=0){
				return !1;
			}
			// console.log('disconnect2');	
			d.run(function() {
                
				if(socket.roomnum==socket.uid){
					/* 主播 */ 
                    // console.log('socket.reusing');
                    // console.log(socket.reusing);
					if(socket.reusing==0){
						request(config['WEBADDRESS']+"?service=Live.stop&uid="+socket.uid + "&token=" + socket.token+ "&stream=" + socket.stream,function(error, response, body){
                            // console.log(body);
                            var data_obj={
                                        "retmsg":"ok",
                                        "retcode":"000000",
                                        "msg":[
                                            {
                                                "_method_":"StartEndLive",
                                                "action":"18",
                                                "ct":"直播关闭"
                                            }
                                        ]
                                    };
                            process_msg(io,socket.roomnum,JSON.stringify(data_obj));
                            
                            clientRedis.del('user_'+socket.stream);
                            
                            // console.log('关播');
                            // console.log(FormatNowDate());
                            // console.log('uid---'+socket.uid);
                        });
					}
                    
                    
                    
				}else{
					/* 观众 */
                    /* 检测是否下麦 */
                    downMic(io,socket.roomnum,socket.uid,socket.nicename,socket.livetype);
                    cancelApply(io,socket.roomnum,socket.livetype,socket.uid);
                
                    clientRedis.zrem('user_'+socket.stream,socket.uid,function(error,res){
                        // console.log('离开')
						if(error) return;
						if(res){
                            var data_obj={
                                            "msg":[
                                                {
                                                    "_method_":"enter",
                                                    "action":"1",
                                                    "ct":'',
                                                    "uid":''+socket.uid,
                                                    "uname":''+socket.nicename,
                                                    "avatar":''+socket.avatar,
                                                    "sex":''+socket.sex,
                                                    "age":''+socket.age
                                                }
                                            ],
                                            "retcode":"000000",
                                            "retmsg":"OK"
                                        };
							process_msg(io,socket.roomnum,JSON.stringify(data_obj));
                            
                            var sign_data={uid:socket.uid,token:socket.token,liveuid:socket.roomnum,stream:socket.stream,type:0};
                            var sign=setSign(sign_data);
                                
                            request(config['WEBADDRESS']+"?service=Live.upnums&uid="+socket.uid + "&token=" + socket.token+ "&liveuid=" + socket.roomnum+ "&type=0" + "&stream=" + socket.stream+ "&sign=" + sign,function(error, response, body){});
						}
						
					});
					
				}
				//console.log(socket.roomnum+"==="+socket.token+"===="+socket.uid+"======"+socket.stream);
				
				socket.leave(socket.roomnum);
				delete io.sockets.sockets[socket.id];
				sockets[socket.uid] = null;
				delete sockets[socket.uid];

			});
	});

});
function sendSystemMsg(socket,msg){
    var data_obj={
                    "msg":[
                        {
                            "_method_":"SystemNot",
                            "action":"1",
                            "ct":""+ msg,
                        }
                    ],
                    "retcode":"000000",
                    "retmsg":"OK"
                };
	socket.emit('broadcastingListen',[JSON.stringify(data_obj)]);
						
}
function evalJson(data){
	return eval("("+data+")");
}

function process_msg(io,roomnum,data){
	if(!chat_history[roomnum]){
		chat_history[roomnum]=[];
	}
	chat_history[roomnum].push(data);
	chat_interval[roomnum] || (chat_interval[roomnum]=setInterval(function(){
		if(chat_history[roomnum].length>0){
			send_msg(io,roomnum);
		}else{
			clearInterval(chat_interval[roomnum]);
			chat_interval[roomnum]=null;
		}
	},200));
}

function send_msg(io,roomnum){
	var data=chat_history[roomnum].splice(0,chat_history[roomnum].length);
    io.sockets.in(roomnum).emit("broadcastingListen", data);
}

/* 移除申请 */
function cancelApply(io,roomnum,livetype,uid){
	/* 老板位申请 */
	if(livetype==1 || livetype==4){
		clientRedis.zrem('boss_'+roomnum,uid,function(error,res){
			// console.log('离开')
			if(error) return;
			if(res){
				var data_obj={
								"msg":[
									{
										"_method_":"linkmic",
										"action":"2",
										"uid":''+uid
									}
								],
								"retcode":"000000",
								"retmsg":"OK"
							};
				io.sockets.in(roomnum).emit("broadcastingListen", [JSON.stringify(data_obj)]);
				
			}
			
		});
	}
	/* 交友 */
	if(livetype==2){
		clientRedis.zrem('jy_'+roomnum+'_1',uid,function(error,res){
			// console.log('离开')
			if(error) return;
			if(res){
				var data_obj={
								"msg":[
									{
										"_method_":"linkmic",
										"action":"2",
										"uid":''+uid
									}
								],
								"retcode":"000000",
								"retmsg":"OK"
							};
				io.sockets.in(roomnum).emit("broadcastingListen", [JSON.stringify(data_obj)]);
				
			}
			
		});
		clientRedis.zrem('jy_'+roomnum+'_2',uid,function(error,res){
			// console.log('离开')
			if(error) return;
			if(res){
				var data_obj={
								"msg":[
									{
										"_method_":"linkmic",
										"action":"2",
										"uid":''+uid
									}
								],
								"retcode":"000000",
								"retmsg":"OK"
							};
				io.sockets.in(roomnum).emit("broadcastingListen", [JSON.stringify(data_obj)]);
				
			}
			
		});
	}
	
	/* 闲谈 */
	if(livetype==3){
		clientRedis.zrem('chat_'+roomnum,uid,function(error,res){
			// console.log('离开')
			if(error) return;
			if(res){
				var data_obj={
								"msg":[
									{
										"_method_":"linkmic",
										"action":"2",
										"uid":''+uid
									}
								],
								"retcode":"000000",
								"retmsg":"OK"
							};
				io.sockets.in(roomnum).emit("broadcastingListen", [JSON.stringify(data_obj)]);
				
			}
			
		});
	}
}

/* 下麦 */
function downMic(io,roomnum,touid,toname,livetype){
    // console.log('downMic');
    // console.log(touid);
    // console.log(toname);
    // console.log(roomnum);
    // console.log(livetype);
    clientRedis.hgetall('sitting_'+roomnum,function(error,res){
        if(error){
            return !1;
        }
        if(!res){
            return !1;
        }
		// console.log(res);
        for(var k in res){
            if(res[k]==touid){
                // console.log('下麦-');
                clientRedis.hset('sitting_'+roomnum,k,0);
                if(k==8){
                    /* 老板下麦 */
					if(livetype==1){
						clearSit(roomnum,livetype);
					}
                }

                var data_obj={
                                "msg":[
                                    {
                                        "_method_":"linkmic",
                                        "action":"5",
                                        "uid":""+touid,
                                        "uname":""+toname,
                                        "touid":""+touid,
                                        "toname":""+toname,
                                        "sitid":""+k
                                    }
                                ],
                                "retcode":"000000",
                                "retmsg":"OK"
                            };
                io.sockets.in(roomnum).emit("broadcastingListen", [JSON.stringify(data_obj)]);
                    
                
				if(livetype==1){
					/* 派单 */
					/* 离开时在发言 等同 跳过 */
					var now_uid=turnTalk_now_uid[roomnum]?turnTalk_now_uid[roomnum]:0;
					if(now_uid==touid){
						turnTalk(io,roomnum);
					}
                }
                
            }
        }
         
    });
}

/* 清空坐席 */
function clearSit(roomnum,livetype){
    var sit={
        1:0,
        2:0,
        3:0,
        4:0,
        5:0,
        6:0,
        7:0,
        8:0,
    }
    
    clientRedis.hmset('sitting_'+roomnum,sit);
    
	if(livetype==2){
		clientRedis.del('heart_'+roomnum);
	}
	if(livetype==1){
		/* 派单--修改订单状态 */
		var sign_data={roomnum:roomnum};
		var sign=setSign(sign_data);
		
		request(config['WEBADDRESS']+"?service=Dispatch.upStatus&roomnum="+roomnum + "&sign=" + sign,function(error, response, body){});
	}
}
/* 轮流发言-发言 */
function turnTalk(io,roomnum){
    
    if(turnTalk_interval[roomnum]){
        clearTimeout(turnTalk_interval[roomnum]);
        turnTalk_interval[roomnum]=null;
    }
                
    var now_sitid=turnTalk_now_sitid[roomnum]?turnTalk_now_sitid[roomnum]:0;
    
    clientRedis.hgetall('sitting_'+roomnum,function(error,res){
        if(error){
            return !1;
        }
        if(!res){
            return !1;
        }
        var i=0;
        for(var k in res){
            /* 第8个位置是老板 */
            if(k<8 && k>now_sitid && res[k]>0){
                var touid=res[k];
                turnTalk_now_sitid[roomnum]=k;
                turnTalk_now_uid[roomnum]=touid;
                var data_obj={
                                "msg":[
                                    {
                                        "_method_":"turnTalk",
                                        "action":"3",
                                        "touid":""+touid,
                                        "sitid":""+k,
                                    }
                                ],
                                "retcode":"000000",
                                "retmsg":"OK"
                            };
                io.sockets.in(roomnum).emit("broadcastingListen", [JSON.stringify(data_obj)]);
                //process_msg(io,roomnum,JSON.stringify(data_obj));
                
                turnTalk_interval[roomnum]=setTimeout(function(){
                    turnTalk(io,roomnum);
                },30*1000)
                break;
            }
            i++;
        }
        
        if(i>=7){
            /* 发言完毕 */
            var data_obj={
                            "msg":[
                                {
                                    "_method_":"turnTalk",
                                    "action":"5",
                                }
                            ],
                            "retcode":"000000",
                            "retmsg":"OK"
                        };
            io.sockets.in(roomnum).emit("broadcastingListen", [JSON.stringify(data_obj)]);
                
            if(turnTalk_interval[roomnum]){
                clearTimeout(turnTalk_interval[roomnum]);
                turnTalk_interval[roomnum]=null;
            }
            
            turnTalk_now_sitid[roomnum]=0;
            turnTalk_now_uid[roomnum]=0;
        }
         
    });
                    
    //io.sockets.in(roomnum).emit("broadcastingListen", data);
}

//时间格式化
function FormatNowDate(){
	var mDate = new Date();
	var Y = mDate.getFullYear();
	var M = mDate.getMonth()+1;
	var D = mDate.getDate();
	var H = mDate.getHours();
	var i = mDate.getMinutes();
	var s = mDate.getSeconds();
	return Y +'-' + M + '-' + D + ' ' + H + ':' + i + ':' + s;
}

/* sign加密 */
function setSign(obj) {//排序的函数
    var str='';
    var newkey = Object.keys(obj).sort();
//先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
    var newObj = {};//创建一个新的对象，用于存放排好序的键值对
    for (var i = 0; i < newkey.length; i++) {//遍历newkey数组
        //newObj[newkey[i]] = obj[newkey[i]];//向新创建的对象中按照排好的顺序依次增加键值对
        str+=newkey[i]+'='+obj[newkey[i]]+'&';
    }
    str+=config['sign_key'];
    
    var sign=md5(str);
    return sign;
}
