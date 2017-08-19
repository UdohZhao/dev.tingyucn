$(function(){
    var idNumberLen=$(".idNumber").length;
    for(var i=0;i<idNumberLen;i++){
        if($(".idNumber").eq(i).val()==''){
            $(".idNumber").eq(i).siblings(".age").html("未认证");
        }else{
            var dateStrA = $(".idNumber").eq(i).val();
            var year = dateStrA.substring(6,10);
            var month = Number(dateStrA.substring(10,12));
            var date = Number(dateStrA.substring(12,14));
            var oDate = new Date(); //实例一个时间对象；
            var nowyear=oDate.getFullYear();   //获取系统的年；
            var nowmonth=oDate.getMonth()+1;   //获取系统月份，由于月份是从0开始计算，所以要加1
            var nowdate=oDate.getDate(); // 获取系统日，
            if(nowyear == year){  
                $(".idNumber").eq(i).siblings(".age").html("0");//同年 则为0岁  
            }else{  
                var ageDiff = nowyear - year ; //年之差  
                if(ageDiff > 0){  
                    if(nowmonth == month){  
                        var dayDiff = nowdate - date;//日之差  
                        if(dayDiff < 0){  
                            var returnAge = ageDiff - 1;
                            $(".idNumber").eq(i).siblings(".age").html(returnAge);
                        }else{  
                            var returnAge = ageDiff ;
                            $(".idNumber").eq(i).siblings(".age").html(returnAge);
                        }  
                    }else{  
                        var monthDiff = nowmonth - month;//月之差  
                        if(monthDiff < 0){  
                            var returnAge = ageDiff - 1;
                            $(".idNumber").eq(i).siblings(".age").html(returnAge);
                        }else{  
                            var returnAge = ageDiff ;
                            $(".idNumber").eq(i).siblings(".age").html(returnAge);
                        }  
                    }  
                }else{  
                    $(".idNumber").eq(i).siblings(".age").html("");//返回-1 表示出生日期输入错误 晚于今天  
                }  
            }
        }
    }
})