var site='http://yuejie.yunbaozb.com';  //站点域名
var schedule = require("node-schedule");
var request  = require('request');

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

//定时减曝光值
var rule = new schedule.RecurrenceRule();

var lastid=0;

rule.second = [0,10,20,30,40,50];
// console.log(times);

var j = schedule.scheduleJob(rule, function(){

    // var time=FormatNowDate();
    //console.log("执行任务:"+time);
    setVal(lastid);


});



function setVal(lastid){
    var time=FormatNowDate();
    // console.log("执行任务setVal"+lastid+'--'+time);
    request(site+"/Appapi/Uporders/Uporder?lastid="+lastid,function(error, response, body){
		// console.log('=========订单超时=========-'+time);
    	//console.log(error);
        if(error) return;
        if(!body) return;
        //console.log('setVal-body-'+lastid+'--'+time);
        //console.log(body);
        if(body!='NO'){
            var strs=[];
            strs=body.split("-");
            
            //console.log(strs);
            if(strs[0]=='OK' && strs[1]!='0'){
                setVal(strs[1]);
            }
            
        }
    });
    
}


//定时处理滴滴订单超时
var rule2 = new schedule.RecurrenceRule();

rule2.second = [0,5,10,15,20,25,30,35,40,45,50,55];
// console.log(times);

var j2 = schedule.scheduleJob(rule2, function(){

    // var time=FormatNowDate();
    //console.log("执行任务:"+time);
    upDrip();


});



function upDrip(){
    // var time=FormatNowDate();
    // console.log("执行任务setVal"+lastid+'--'+time);
    request(site+"/Appapi/updrip/up",function(error, response, body){
    	//console.log(error);
        if(error) return;
        if(!body) return;
        //console.log('upDrip-body-'+lastid+'--'+time);
        //console.log(body);

    });
    
}

//定时发送滴滴订单
var rule3 = new schedule.RecurrenceRule();

var lastid3=0;

rule3.second = [0,10,20,30,40,50];
// console.log(times);

var j3 = schedule.scheduleJob(rule3, function(){

    // var time=FormatNowDate();
    //console.log("执行任务3:"+time);
    sendDrip(lastid3);

});



function sendDrip(lastid){
    // var time=FormatNowDate();
    // console.log("执行任务setVal"+lastid+'--'+time);
    request(site+"/Appapi/updrip/send?lastid="+lastid,function(error, response, body){
    	//console.log(error);
        if(error) return;
        if(!body) return;
        //console.log('sendDrip-body-'+lastid+'--'+time);
        //console.log(body);
        if(body!='NO'){
            var strs=[];
            strs=body.split("-");
            
            //console.log(strs);
            if(strs[0]=='OK' && strs[1]!='0'){
                sendDrip(strs[1]);
            }
            
        }
    });
    
}

//定时发送派单信息
var rule4 = new schedule.RecurrenceRule();

var lastid4=0;

rule4.second = [0,5,10,15,20,25,30,35,40,45,50,55];
// console.log(times);

var j4 = schedule.scheduleJob(rule4, function(){

    // var time=FormatNowDate();
    //console.log("执行任务3:"+time);
    sendDispatch(lastid4);

});



function sendDispatch(lastid){
    // var time=FormatNowDate();
    // console.log("执行任务setVal"+lastid+'--'+time);
    request(site+"/Appapi/Updispatch/send?lastid="+lastid,function(error, response, body){
    	//console.log(error);
        if(error) return;
        if(!body) return;
        //console.log('sendDispatch-body-'+lastid+'--'+time);
        //console.log(body);
        if(body!='NO'){
            var strs=[];
            strs=body.split("-");
            
            //console.log(strs);
            if(strs[0]=='OK' && strs[1]!='0'){
                sendDispatch(strs[1]);
            }
            
        }
    });
    
}

var rule5 = new schedule.RecurrenceRule();

rule5.second = [0,5,10,15,20,25,30,35,40,45,50,55];
// console.log(times);


//定时处理：订单开始前10分钟发送订单开始提醒信息
var laststartid=0;
var j5 = schedule.scheduleJob(rule5, function(){
    orderStart(laststartid);
});

function orderStart(laststartid){
	var time=Date.parse(new Date())/1000;
	// console.log("订单开始前10分钟发送订单开始提醒信息==j5===="+laststartid+'--'+time);
    request(site+"/Appapi/Uporders/orderStart?lastpetid=&nowtime="+time+"&lastpetid="+laststartid,function(error, response, body){
        if(error) return;
        if(!body) return;
    });
}
//订单开始通知
var laststartingid=0;
var j6 = schedule.scheduleJob(rule5, function(){
    orderStarting(laststartingid);
});
function orderStarting(laststartingid){
	var time=Date.parse(new Date())/1000;
    request(site+"/Appapi/Uporders/orderStarting&nowtime="+time+"&lastpetid="+laststartingid,function(error, response, body){
        if(error) return;
        if(!body) return;
    });
}

//订单结束前五分钟通知
var lastendid=0;
var j7 = schedule.scheduleJob(rule5, function(){
    orderEnd(lastendid);
});
function orderEnd(lastendid){
	var time=Date.parse(new Date())/1000;
	// console.log("结束前五分钟==j7===="+lastendid+'--'+time);
    request(site+"/Appapi/Uporders/orderEnd&nowtime="+time+"&lastpetid="+lastendid,function(error, response, body){
    	
        if(error) return;
        if(!body) return;
     
    });
    
}

//退款通知：15分钟未处理：自动处理拒绝退款
var lastrefundid=0;
var j7 = schedule.scheduleJob(rule5, function(){
    orderRefund(lastrefundid);
});
function orderRefund(lastrefundid){
	var time=Date.parse(new Date())/1000;
    // console.log("退款通知：15分钟未处理：自动处理拒绝退款==j7===="+lastendid+'--'+time);
    request(site+"/Appapi/Uporders/orderRefund&nowtime="+time+"&lastpetid="+lastrefundid,function(error, response, body){
    	
        if(error) return;
        if(!body) return;
     
    });
}

//定时处理：自动完成订单
var lastcompleteid=0;
var j7 = schedule.scheduleJob(rule5, function(){
    orderComplete(lastcompleteid);
});
function orderComplete(lastcompleteid){
	var time=Date.parse(new Date())/1000;
	// console.log("定时处理：自动完成订单==j7===="+lastendid+'--'+time);
    request(site+"/Appapi/Uporders/orderComplete&nowtime="+time+"&lastpetid="+lastcompleteid,function(error, response, body){
    	
        if(error) return;
        if(!body) return;
     
    });
}

