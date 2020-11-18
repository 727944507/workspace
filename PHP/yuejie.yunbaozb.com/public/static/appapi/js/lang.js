/**
语言包替换
key string 需要翻译的文本（语言包中的键值）
params object 需要替换的参数（动态变量的键对值）
**/
function Lang(key,params) {
	//lang=typeof(lang)=='object'?lang:JSON.parse(lang);
	var rs = language_pack && language_pack[key] ? language_pack[key] : key;

	for (var k in params){
		var r = new RegExp('{'+k+'}', "ig");
		var re=params[k];
		rs=rs.replace(r, re);
	}
	return  rs;
}

