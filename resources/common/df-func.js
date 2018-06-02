function redirect(url)
{
	if(url.lastIndexOf('/.') > 0)
	{
		url = url.replace(/\/(\.[a-zA-Z]+)([0-9]+)$/g, "/$2$1");
	}
	else if(url.match(/\/([a-z\-]+).html([0-9]+)$/)) {
		url = url.replace(/\/([a-z\-]+).html([0-9]+)$/, "/$1/page-$2.html");
	}
	else if(url.match(/\/([a-z]+).html([0-9]+)$/)) {
		url = url.replace(/\/([a-z]+).html([0-9]+)$/, "/$1-$2.html");
	}
	//if(url.indexOf('://') == -1 && url.substr(0, 1) != '/' && url.substr(0, 1) != '?') url = $('base').attr('href')+url;
	location.href = url;
}

String.prototype.trim=function(){
	return this.replace(/(^\s*)|(\s*$)/g, "");
}

/*
 * * 方法:Array.remove(dx)
 * * 功能:删除数组元素. 
 * * 参数:dx删除元素的下标. 
 * * 返回:在原数组上修改数组
 */
Array.prototype.remove = function(dx)
{
	if(isNaN(dx)||dx>this.length){return false;}
	for(var i=0,n=0;i<this.length;i++)
	{
		if(this[i]!=this[dx])
		{
			this[n++]=this[i]
		}
	}
	this.length-=1;
};

var valid = {};
/*
用途：检查输入字符串是否为空或者全部都是空格
输入：str
返回：
如果全是空返回true,否则返回false
*/ 
valid.isNull = function( str ){
	if ( str == "" ) return true;
	var regu = "^[ ]+$";
	var re = new RegExp(regu);
	return re.test(str);
} 
/*
用途：检查输入字符串是否符合正整数格式
输入：
s：字符串
返回：
如果通过验证返回true,否则返回false
*/
valid.isNumber = function isNumber( s ){
	var regu = "^[0-9]+$";
	var re = new RegExp(regu);
	if (s.search(re) != -1) {
		return true;
	} else {
		return false;
	}
}
/*
用途：检查输入对象的值是否符合E-Mail格式
输入：str 输入的字符串
返回：如果通过验证返回true,否则返回false
*/
valid.isEmail = function isEmail( str ){
	var myReg = /^[-_A-Za-z0-9]+@([_A-Za-z0-9]+\.)+[A-Za-z0-9]{2,3}$/;
	if(myReg.test(str)) return true;
	return false;
}
/*
用途：检查输入字符串是否符合金额格式
格式定义为带小数的正数，小数点后最多三位
输入：
s：字符串
返回：
如果通过验证返回true,否则返回false
*/
valid.isMoney = function isMoney( s ){
	var regu = "^[0-9]+[\.][0-9]{0,3}$";
	var re = new RegExp(regu);
	if (re.test(s)) {
	return true;
	} else {
	return false;
}
} 
/*
用途：检查输入字符串是否只由英文字母和数字和下划线组成
输入：
s：字符串
返回：
如果通过验证返回true,否则返回false
*/
valid.isNumberOrLetter = function isNumberOr_Letter( s ){//判断是否是数字或字母
	var regu = "^[0-9a-zA-Z\_]+$";
	var re = new RegExp(regu);
	if (re.test(s)) {
	return true;
	}else{
	return false;
	}
}  
/*
用途：检查输入字符串是否只由汉字、字母、数字组成
输入：
value：字符串
返回：
如果通过验证返回true,否则返回false
*/
valid.isChinaOrNumbOrLett = function isChinaOrNumbOrLett( s ){//判断是否是汉字、字母、数字组成
	var regu = "^[0-9a-zA-Z\u4e00-\u9fa5]+$";
	var re = new RegExp(regu);
	if (re.test(s)) {
	return true;
	}else{
	return false;
	}
} 
//检查日期是否是YYYY-MM-DD格式
valid.checkDate = function checkDate(theDate) {
	var reg = /^\d{4}-((0{0,1}[1-9]{1})|(1[0-2]{1}))-((0{0,1}[1-9]{1})|([1-2]{1}[0-9]{1})|(3[0-1]{1}))$/;
	var result = true;
	if (!reg.test(theDate))
		result = false;
	else {
		var arr_hd = theDate.split("-");
		var dateTmp;
		dateTmp = new Date(arr_hd[0], parseFloat(arr_hd[1]) - 1,
				parseFloat(arr_hd[2]));
		if (dateTmp.getFullYear() != parseFloat(arr_hd[0])
				|| dateTmp.getMonth() != parseFloat(arr_hd[1]) - 1
				|| dateTmp.getDate() != parseFloat(arr_hd[2])) {
			result = false
		}
	}
	return result;
}
/*
用途：检查输入的起止日期是否正确，规则为两个日期的格式正确，
且结束如期>=起始日期
输入：
startDate：起始日期，字符串
endDate：结束如期，字符串
返回：
如果通过验证返回true,否则返回false
*/ 
valid.checkTwoDate = function checkTwoDate( startDate,endDate ) {
	    //最小日期 
	    var minDate = startDate; 
	    //最大日期 
	    var maxDate = endDate; 

        //验证日期格式正则表达式,格式为 yyyy-MM-dd 
        var reg = /^(\d{4})-(\d{2})-(\d{2})$/;
        if(minDate != ""){
	        //校验日期格式 
	        if (!valid.checkDate(minDate)) { 
	            alert("最小日期格式错误,格式应为yyyy-MM-dd"); 
	            return false; 
	        }
        }
        if(maxDate != ""){
        	//校验日期格式 
        	if (!valid.checkDate(maxDate)) { 
        		alert("最大日期格式错误,格式应为yyyy-MM-dd"); 
        		return false; 
        	}
        }
        if(minDate != "" && maxDate != ""){
	        // 用 - 分隔符将日期分开 
	        var maxDateSplit = maxDate.split("-"); 
	        var minDateSplit = minDate.split("-"); 
	        // 创建 Date 对象 
	        var maxDateValue = new Date(maxDateSplit[0], maxDateSplit[1], maxDateSplit[2]); 
	        var minDateValue = new Date(minDateSplit[0], minDateSplit[1], minDateSplit[2]); 
	        if (minDate >= maxDate) { 
	            alert("你输入的最小日期大于或等于了最大日期!!!"); 
	            return false; 
	        }
        }
	    return true; 

}
/*
用途：检查输入的Email信箱格式是否正确
输入：
strEmail：字符串
返回：
如果通过验证返回true,否则返回false
*/
valid.checkEmail = function checkEmail(strEmail) {
	//var emailReg = /^[_a-z0-9]+@([_a-z0-9]+\.)+[a-z0-9]{2,3}$/;
	var emailReg = /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/;
	if( emailReg.test(strEmail) ){
	return true;
	}else{
	alert("您输入的Email地址格式不正确！");
	return false;
	}
}