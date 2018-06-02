/*
 *
 * 沿用jQuery框架
 * xiaojh@ 9:20 2009-9-11
 * xiaojh@ 15:48 2010-5-30 
 * xiaojh@ 14:45 2013/2/22
 *
 */
var _login_datas = '<ul style="margin: 0px; padding: 8px 0px 0px; font-family: Verdana; font-style: normal; clear: both;">\
		<li style="line-height: 20px; margin: 0px auto;width: 90%; padding-left: 5px; background-color: rgb(247, 255, 239); border: 1px solid rgb(234, 234, 234); font-size: 12px; font-weight: normal;">Didn\'t you account?<br><a onclick="showRegister();" style="text-decoration: underline; font-size: 14px; cursor: pointer; color: rgb(0, 102, 204); font-weight: 600;"> 现在就加入Dofound.net</a>	<img style="display: none;" src="/resources/ajax_load.gif" id="log_AJ"></li>\
		<li style="margin-left: 15px; margin-top: 5px; line-height: 30px; height: 30px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;你的名字：<input type="text" style="width: 180px; height: 16px; display: inline; margin-top: 7px;" id="loginUserName"></li>\
		<li style="margin-left: 15px; line-height: 30px; height: 30px; margin-top: 10px; margin-bottom: 10px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;你的密码：<input type="password" style="width: 180px; height: 16px; display: inline; margin-top: 7px;" id="loginPassWord"></li>\
		<!-- <li style="list-style: none outside none; line-height: 20px; font-weight: normal; width: 90%; margin: 0px auto; font-size: 12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" checked="" value="1" id="remember_login" name="remember_login">&nbsp;&nbsp;记住密码,这台机器下次将自动登陆.</li> -->\
		<li style="vertical-align: middle; font-weight: 100; color: red; overflow: hidden; text-align: left; padding-left: 20px; display: none;" id="log_err_tp"><div style="width: 90%;background:#99FFCC;" id="log_err_show"></div></li>\
		<li style="line-height: 40px; height: 40px;text-align: center;"><span style="width: 115px; float: right; font-weight: 100; font-size: 10px; height: 40px; line-height: 40px;"></span><span style="width: 110px; float: right; font-weight: 100; font-size: 10px; height: 40px; line-height: 40px;"><a style="color: rgb(0, 102, 204);" target="_blank" href="?act=member.passwordforget">忘记密码</a></span><input type="button" onclick="goLogin();_df.maskResize();" id="reg_send_btn" value="登 录" style="margin: 10px 0px 0px 10px;" class="send"></li>\
		<li style="list-style: none outside none; margin-left: 15px; line-height: 5px; height: 5px; margin-top: 1px; margin-bottom: 5px;"></li>\
	</ul>';
var _register_datas = '<ul style="margin: 0px; padding: 8px 0px 0px; font-family: Verdana; font-style: normal; clear: both;" id="reg_ok_ul">\
		<li style="line-height: 20px; margin: 0px auto;width: 90%; padding-left: 5px; background-color: rgb(247, 255, 239); border: 1px solid rgb(234, 234, 234); font-size: 12px; font-weight: normal;"><div id="reg_to_login_but">已经是Dofound中的一员?<a onclick="showLogin();" style="text-decoration: underline; margin-left: 5px; font-size: 14px; cursor: pointer; color: rgb(0, 102, 204); font-weight: 600;">登录</a><br></div>仍然不是成员? 填写下面的信息，成为新的一员.<img style="display: none;" src="/resources/ajax_load.gif" id="reg_AJ"></li>\
		<li style="margin-left: 15px; margin-top: 5px; line-height: 30px; height: 30px;"><label style="width: 80px; float: left; display: inline;">你的邮箱:</label><input type="text" style="width: 160px; height: 16px; display: inline; margin-top: 5px;" id="regUserName"> <font color=#999999>真实的Email地址</font></li>\
		<li style="margin-left: 15px; line-height: 30px; height: 30px; margin-top: 10px;"><label style="width: 80px; float: left; display: inline;">你的名字:</label><input type="text" style="width: 160px; height: 16px; display: inline; margin-top: 5px;" id="regUser"> <font color=#999999>显示和登陆需要使用</font></li>		<li style="list-style: none outside none; margin-left: 15px; line-height: 15px; height: 15px; font-size: 12px; font-weight: 100;"><label style="width: 225px; float: right; text-align: left;color:#999999">最少 4 个字符.</label></li>\
		<li style="margin-left: 15px; line-height: 30px; height: 30px; margin-top: 10px;"><label style="width: 80px; float: left; display: inline;">设置密码:</label><input type="password" style="width: 160px; height: 16px; display: inline; margin-top: 5px;" id="regPassWord"></li>\
		<li style="list-style: none outside none; margin-left: 15px; line-height: 15px; height: 15px; font-size: 12px; font-weight: 100;"><div style="width: 225px; float: right; text-align: left;color:#999999">至少 6 个字符.</div></li>\
		<li style="margin-left: 15px; line-height: 30px; height: 30px; margin-top: 10px;"><label style="width: 80px; float: left; display: inline;">重复密码:</label>		  <input type="password" style="width: 160px; height: 16px; display: inline; margin-top: 5px;" id="confirmPwd"></li>\
		<li style="line-height: 20px; font-weight: normal; width: 90%; margin: 0px auto; font-size: 12px;"><input type="checkbox" checked="" value="1" id="term_agree" name="term_agree">&nbsp;&nbsp;我同意Dofound的 <a style="color: rgb(0, 102, 204);" target="_blank" href="/info/terms/">用户条款</a>.</li>		<li style="margin-top:8px;list-style: none outside none;vertical-align: middle; font-weight: 100; color: red; overflow: hidden; text-align: left; padding-left: 20px; display: none;" id="is_error_reg"><div style="width: 90%;background:#99FFCC;" id="reg_err_show"></div></li>		<li style="list-style: none outside none; line-height: 40px; height: 40px; vertical-align: middle; text-align: center;"><span style="width: 195px; float: right; font-weight: 100; font-size: 10px; height: 40px; line-height: 40px; text-align: left; padding-left: 30px;"></span><input type="button" onclick="goReg();_df.maskResize();" value="提交注册" style="margin-top: 10px; margin-left: 10px;" id="reg_send_btn" class="send"></li>\
		<!-- <li style="list-style: none outside none; margin-left: 15px; line-height: 20px; height: 20px; margin-top: 1px; margin-bottom: 10px;">如果你是一名作者, 请到这<a style="text-decoration: underline; font-size: 14px; cursor: pointer; color: rgb(0, 102, 204); font-weight: 600;" href="?act=Register.reg&amp;figure=author">注册</a> to register.</li> -->\
	</ul>';
	
function goLogin(){
	var userid=$("#loginUserName").val();
	var dpass=$("#loginPassWord").val();
	var erro_msg='';
	$("#log_err_tp").fadeIn("fast");
	if(valid.isNull(userid)){
		erro_msg='提示：你的名字不能为空';
		$("#log_err_show").html(erro_msg);
		$("#loginUserName").focus();
		return;
	}else if(valid.isNull(dpass)){
		erro_msg='提示：你的密码不能为空';
		$("#log_err_show").html(erro_msg);
		$("#loginPassWord").focus();
		return;
	}else{

		$("#log_err_tp").toggle(false);
		//$("#log_AJ").toggle();
		$("#reg_send_btn")[0].disabled=true;
		//$("#reg_send_btn")[0].src="http://img.dofound.com/images/unsubmit.gif";

		var thrdata='ispos=yes&name='+encodeURIComponent(userid)+'&pass='+encodeURIComponent(dpass)+'&rdm='+Math.random();
		_df.Ajax('/login/index.html',thrdata,function(){$("#log_AJ").toggle();},function(){$("#log_AJ").toggle();},op_login_datas);

	}
};

function goReg(){
	var email=$("#regUserName").val();
	var userid=$("#regUser").val();
	var dpass=$("#regPassWord").val();
	var rdpass=$("#confirmPwd").val();
	var erro_msg='';
	//$("#is_error_reg").toggle(true);
	$("#is_error_reg").fadeIn("fast");
	if(!valid.isEmail(email)){
		erro_msg='提示:你的Email地址不正确.使用真实的Email。';
		$("#regUserName").focus();
		$("#reg_err_show").html(erro_msg);
		return;
	}else if(valid.isNull(userid)){
		erro_msg='提示:你的名字不能为空';
		$("#regUser").focus();
		$("#reg_err_show").html(erro_msg);
		return;
	}else if(valid.isNull(dpass) || valid.isNull(rdpass)){
		erro_msg='提示:请正确填写密码，不能为空';
		$("#reg_err_show").html(erro_msg);
		return;
	}else if(dpass.length<6 || rdpass.length<6){
		erro_msg='提示:密码至少6个字符';
		$("#reg_err_show").html(erro_msg);
		return;
	}else{
		if(dpass.length!=rdpass.length){
			erro_msg='提示:两次输入的密码不正确';
			$("#reg_err_show").html(erro_msg);
			return;
		}else if(userid.length<4){
			erro_msg='提示:用户名至少4个字符';
			$("#regUser").focus();
			$("#reg_err_show").html(erro_msg);
			return;
		}else if(!valid.isChinaOrNumbOrLett(userid)){
			erro_msg='提示:用户名只由汉字、字母、数字组成';
			$("#regUser").focus();
			$("#reg_err_show").html(erro_msg);
			return;
		}
		//alert(userid.length);
		$("#is_error_reg").toggle(false);
		$("#reg_send_btn")[0].disabled=true;
		//$("#reg_send_btn")[0].src="http://img.dofound.com/images/unsubmit.gif";
		
		var thrdata='ispos=yes&email='+encodeURIComponent(email)+'&name='+encodeURIComponent(userid)+'&pass='+encodeURIComponent(dpass)+'&rdm='+Math.random();
		_df.Ajax('/reg',thrdata,function(){$("#reg_AJ").toggle();},function(){$("#reg_AJ").toggle();},op_regs_datas);
	}

};
function op_login_datas(datas){
	var ddatas=op_ajax_datas(datas);
	if(ddatas.s>0){
		_df.Lock('用户登陆','<div style="margin:20px;background:#FFFFCC;text-align:center;border:1px dotted #cccccc;font-size:18px;padding:20px;color:red">登陆成功. <span style="font-size:12px;color:green">正在跳转...</span><script>setTimeout("_df.delLock(1);",2000);</script></div>');
	}else{
		$("#reg_send_btn")[0].disabled=false;
		//$("#reg_send_btn")[0].src="http://img.dofound.com/images/submit.gif";
		$("#log_err_tp").toggle();
		$("#log_err_show").html(ddatas.msg);
		_df.maskResize();
	}
}
function op_regs_datas(datas){
	var ddatas=op_ajax_datas(datas);
	if(ddatas.s>0){
		_df.Lock('用户注册','<div style="margin:20px;background:#FFFFCC;text-align:center;border:1px dotted #cccccc;font-size:18px;padding:20px;color:red">恭喜，注册成功. <span style="font-size:12px;color:green">正在跳转...</span><script>setTimeout("_df.delLock(1);",2000);</script></div>');
	}else{
		$("#reg_send_btn")[0].disabled=false;
		//$("#reg_send_btn")[0].src="http://img.dofound.com/images/submit.gif";
		$("#is_error_reg").toggle();
		$("#reg_err_show").html(ddatas.msg);
		_df.maskResize();
	}
}
function op_ajax_datas(datas){
	//var tmpdata=eval("("+datas+")");
	var tmpdata = new Function("return "+datas)();
	return tmpdata;
};

function showLogin(){_df.Lock('The login window',_login_datas,1);};
function showRegister(){_df.Lock('Register window',_register_datas,1);};
