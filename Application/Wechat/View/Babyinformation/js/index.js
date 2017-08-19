$(function(){
    var starBoxLen=$(".starBox").children("span").length;
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
    //视频播放
    $(".video_box").click(function(){
        $(".video_play").css("display","block");
        if($(".video_play").find('video').attr("src")==''){
            $(".noUpload").html("未上传");
            $(".video_play").find('video').attr("hidden","true");
            $(".close_vedio").click(function(){
                $(".video_play").css("display","none");
            });
        }else{
            var myVideo = document.getElementsByTagName('video')[1];
            myVideo.play();
            $(".close_vedio").click(function(){
                myVideo.pause();
                $(".video_play").css("display","none");
            })
        }
    })
    // var audio=$("audio").duration;
    // console.log(audio);
})