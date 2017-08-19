$(function(){
　　// validate
   $("#loginForm").validate({
      submitHandler : function(form) {  //验证通过后的执行方法
        //当前的form通过ajax方式提交（用到jQuery.Form文件）
        $(form).ajaxSubmit({
            dataType:"json",
            success:function( msg ){
                if( !msg ){
                  swal("# 用户名或则密码错误", "", "error");
                }else{
                  window.location.href = msg;
                }
              }
            });
      },
      focusInvalid : true,
      rules: {
        username: "required",
        password: "required",
        code: {
          required: true,
          remote: {
              url: "verifyCode",     //后台处理程序
              type: "post",               //数据发送方式
              dataType: "json",           //接受数据格式
              data: {                     //要传递的数据
                  code: function() {
                      return $("#code").val();
                  }
              }
          }
        }
      },
      messages: {
        username: "<span style='color:red;'>用户名不能为空！</span>",
        password: "<span style='color:red;'>密码不能为空！</span>",
        code: {
          required: "<span style='color:red;'>验证码不能为空！</span>",
          remote: "<span style='color:red;'>验证码错误！</span>"
        }
      }
   });
});