$(function(){
	var liLen=$(".my_order").find("li").length;
	$(".my_order").find("li").css("width",100/liLen+'%');
})