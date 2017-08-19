$(function(){
	var len=$(".bottom_pic").length;
	for(var i=0;i<len;i++){
		if($(".bottom_pic").eq(i).attr('value')==0){
			$(".bottom_pic").eq(i).attr("xlink:href","#icon-daiqueren");
		}else if($(".bottom_pic").eq(i).attr('value')==1){
			$(".bottom_pic").eq(i).attr("xlink:href","#icon-qian");
		}else if($(".bottom_pic").eq(i).attr('value')==2){
			$(".bottom_pic").eq(i).attr("xlink:href","#icon-jinxingzhong");
		}else if($(".bottom_pic").eq(i).attr('value')==3){
			$(".bottom_pic").eq(i).attr("xlink:href","#icon-yiwancheng1");
		}else if($(".bottom_pic").eq(i).attr('value')==4){
			$(".bottom_pic").eq(i).attr("xlink:href","#icon-yiquxiao");
		}
	}
})