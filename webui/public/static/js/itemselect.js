 //删除特定url参数
function delQueStr(url, ref) //删除参数值
{
    var str = "";

    if (url.indexOf('?') != -1)
        str = url.substr(url.indexOf('?') + 1);
    else
        return url;
    var arr = "";
    var returnurl = "";
    var setparam = "";
    if (str.indexOf('&') != -1) {
        arr = str.split('&');
        for (i in arr) {
            if (arr[i].split('=')[0] != ref) {
                returnurl = returnurl + arr[i].split('=')[0] + "=" + arr[i].split('=')[1] + "&";
            }
        }
        return url.substr(0, url.indexOf('?')) + "?" + returnurl.substr(0, returnurl.length - 1);
    }
    else {
        arr = str.split('=');
        if (arr[0] == ref)
            return url.substr(0, url.indexOf('?'));
        else
            return url;
    }
}

//设置参数值
function setQueStr(url, ref, value) 
{
    var str = "";
    if (url.indexOf('?') != -1)
        str = url.substr(url.indexOf('?') + 1);
    else
        return url + "?" + ref + "=" + value;
    var returnurl = "";
    var setparam = "";
    var arr;
    var modify = "0";

    if (str.indexOf('&') != -1) {
        arr = str.split('&');

        for (i in arr) {
            if (arr[i].split('=')[0] == ref) {
                setparam = value;
                modify = "1";
            }
            else {
                setparam = arr[i].split('=')[1];
            }
            returnurl = returnurl + arr[i].split('=')[0] + "=" + setparam + "&";
        }

        returnurl = returnurl.substr(0, returnurl.length - 1);

        if (modify == "0")
            if (returnurl == str)
                returnurl = returnurl + "&" + ref + "=" + value;
    }
    else {
        if (str.indexOf('=') != -1) {
            arr = str.split('=');

            if (arr[0] == ref) {
                setparam = value;
                modify = "1";
            }
            else {
                setparam = arr[1];
            }
            returnurl = arr[0] + "=" + setparam;
            if (modify == "0")
                if (returnurl == str)
                    returnurl = returnurl + "&" + ref + "=" + value;
        }
        else
            returnurl = ref + "=" + value;
    }
    return url.substr(0, url.indexOf('?')) + "?" + returnurl;
}

// 配置API的base_url
var base_url = '';

/**
 * 获取完整API的URL
 */
function url(uri) {
    return base_url + uri;
}

 // 类型时间月份 的发布信息画图
function select_stage(value){
	//删除就参数设置新参数
	var urldel = delQueStr(window.location.href, 'stage');
	var urlset = setQueStr(urldel, 'stage', value);
	window.location.href = urlset;
}
function select_type(value){
 //删除就参数设置新参数
 var urldel = delQueStr(window.location.href, 'type');
 var urlset = setQueStr(urldel, 'type', value);
 window.location.href = urlset;
}