$(function(){
	$(".weui-tabbar__item").click(function(){
	    $(this).css("text-decoration","none");
	  });
	  $(".weui-btn_primary").click(function(){
	    $(this).css("background-color","#fd908f");
	  });
	  $("#mylogin").click(function(){
	    $(".tanchu").css("display","block");
	    $("#myModal").css("display","block");
	    $("#forgetthe_psw").click(function(){
	      $("#myModal").css("display","none");
	      $("#forgetPsw").css("display","block");
	    });
	    $(".shanchu").click(function(){
	      $(".tanchu").css("display","none");
	      $("#myModal").css("display","none");
	      $("#forgetPsw").css("display","none");
	    })
	  })

	
	$(".serviceList_top").eq(0).css("background-color","#ee8f3b");
	$(".serviceList_top").eq(1).css("background-color","#0c7eba");
	$(".serviceList_top").eq(2).css("background-color","#ee8f3b");
	$(".serviceList_top").eq(3).css("background-color","#0c7eba");
});
		