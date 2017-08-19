$(function(){
	var col9H=$(".rightBtn").siblings(".col-md-9").height();
	$(".rightBtn").css("height",col9H);

	// 评价五星
	// var len=$(".evaluationNum").length;
	// for(var i=0;i<len;i++){
	// 	var fen=$(".evaluationNum").eq(i).children("span").html();
	// 	var fenW=fen*10;
	// 	$(".starImg").eq(i).css("width",fenW+'px');
	// }

	var starBoxLen=$(".starBox").children("span").length;
	console.log(starBoxLen);
    for(var i=0;i<starBoxLen;i++){
        var starBoxHtml=$(".starBox").children("span").eq(i).html();
        if(starBoxHtml=="好评"){
            $(".starBox").children("span").eq(i).css("color","red");
        }else if(starBoxHtml=="中评"){
                $(".starBox").children("span").eq(i).css("color","#f88e4b");
        }else{
            $(".starBox").children("span").eq(i).css("color","#ccc");
        }
    };

    $(".blackBtn").click(function(){
    	$(this).addClass("actived");
    });
    $(".careBtn").click(function(){
    	$(this).addClass("actived");
    });
    var Media = document.getElementById("baby_audio");
    if(Media!=null){
        var miao=Media.duration;
        if(isNaN(miao)){
            Media.addEventListener("canplay", function(){
                var miao=Media.duration;
                var miaoZ=Math.round(miao);
                $(".audio_img i span").html(miaoZ);
                $(".audio_box").click(function(){
                  Media.play();
                  $(".stop_img").css("display","none");
                  setTimeout(function(){
                    $(".stop_img").css("display","block");
                  },miaoZ*1000);
                })  
            });
        }else{
            var miao=Media.duration;
            var miaoZ=Math.round(miao);
            $(".audio_img i span").html(miaoZ);
            $(".audio_box").click(function(){
              Media.play();
              $(".stop_img").css("display","none");
              setTimeout(function(){
                $(".stop_img").css("display","block");
              },miaoZ*1000);
            })
            
        }
    }
})