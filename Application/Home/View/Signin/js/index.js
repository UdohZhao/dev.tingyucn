$(function(){
	//注册
	var verifyCode = new GVerify("v_container");
	$('#Sbt').click(function(){
		var res = verifyCode.validate(document.getElementById("code_input").value);

		var pobj = document.getElementById('Snumber');
		var phone=$(pobj).val()
		var pwd=$('#Spassword').val();                  //密码
		var verify=$('#Syanzheng').val();
		if(checkPhone()){ //验证正确
			if(!res){
				layer.msg('图片验证码不正确');
				return false;
			}
			$.ajax({
				url:'save',
				data:'username='+phone+'&password='+pwd+'&verify='+verify,
				dataType:'json',
				type:'post',
				success:function(re){
					if(re.info==2){  //短信验证码不正确
						// alert(re.msg)
						layer.msg(re.msg);
					}else if(re===true){
						// alert('注册成功');
						layer.alert('注册成功',function(){
							window.location.href='../Index/index';
						});

					}else if(re.info==3){
						// alert(re.msg);
						layer.alert(re.msg);
					}
				}
			})
		}
	})
	//获取验证码
	$('#getverify').click(function(){

		var phone = document.getElementById('Snumber').value;  //手机号码
		if(!(/^1[34578]\d{9}$/.test(phone))){

			layer.alert('手机号码有误，请重填');
			//$.toast("手机号码有误，请重填", "cancel");
			return false;
		}

		$.ajax({
			url:'getVerify',
			data:'phone='+phone,
			dataType:'json',
			type:'post',
			success:function(re){

				if(re===true){
					//alert('验证码发送成功,注意接收短信');
					layer.alert('验证码发送成功,注意接收短信');
					//$.toast("验证码发送成功,注意接收短信");
				}else if(re.info==3){
					layer.alert(re.msg)
					//$.toast(re.msg, "cancel");
				}else{
					layer.alert('验证码发送失败');
					//$.toast("验证码发送失败", "cancel");
				}
			}
		})
		return false
	})

	function checkPhone(){ //验证手机号码正则函数  ，以及密码长度 ，确认密码是否一致
		var pobj = document.getElementById('Snumber');
		var phone=$(pobj).val()  //手机号码
		if(!(/^1[34578]\d{9}$/.test(phone))){
			// alert("手机号码有误，请重填");
			layer.alert("手机号码有误，请重填");
			return false;
		}
		//验证密码
		var pattern=/^(?![a-zA-z]+$)(?!\d+$)(?![!@#$%^&*]+$)[a-zA-Z\d!@#$%^&*]+$/;
		var pwd=$('#Spassword').val();                  //密码
		if(!pattern.test(pwd)){
			// alert('密码格式不正确，格式：字母+数字或者字符')
			layer.alert("密码格式不正确，格式：字母+数字或者字符");
			return false;
		}else if(pwd.length<6 || pwd.length>20){
			// alert('密码位数在6到20位之间');
			layer.alert("密码位数在6到20位之间");
			return false;
		}
		var conpwd=$('#Spasswordagain').val();
		if(pwd!==conpwd){
			// alert('两次密码不一致')
			layer.alert("两次密码不一致");
			return false;
		}
		return true;
	}
})