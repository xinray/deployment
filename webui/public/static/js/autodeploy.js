// 配置API的base_url
var base_url = '';

/**
 * 获取完整API的URL
 */
function url(uri) {
    return base_url + uri;
}

//获得config 文件
var configArray = new Array();


$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    var config = $('.test-result').attr('data-config');
    var jsonarray= $.parseJSON(config);
    $.each(jsonarray, function (i, n)
    {
        configArray[i] = n;
    });

    $('.deploy').on('click', deploy);

    getSvnDiff();

});

//获取svndiff ajax
function getSvnDiff()
{
    var href = window.location.pathname;
    var svnUpdate = 'svn' + href;
    $.ajax({
        url         : svnUpdate,
        type        : 'get',
        dataType    : 'json',
        success     : function(response) {
            if (response.code == 0) {
                var data = response.data;
                $(".distinguish").html(data);
            }
        },
        error    : function(response){
            var data = response.responseText;
            $(".distinguish").html(data);
        }
    });
}

function deploy()
{
    var hosts = $('.hostsname').val();
    var host = hosts.split(',');
    var deploynum = 0;
    var html = '';
    var jobnum = 0;
    $.each(host, function( index, value ) {
        html +='<tr><td class="result-host">' + value + '</td>';
        $.each(configArray, function (i, n)
        {
            html += '<td class="result-pic' + index + i +'">未开始</td>';
            jobnum = (+i)+(+1);
        });
        html +='</tr>';
        deploynum++;
    });

    $('.resultpic').html(html);

    
    var data = {
        targetdirectory      : $('.targetdirectory').val(),
        guid                 : $('.hostsname').attr("guid"),
        hosts                : hosts,
        productid            : $('.productid').attr('productid'),
    };

    var ura = '/auto/lasthistoryDBId';
    $.ajax({
        url: ura,
        type: 'get',
        data: data,
        dataType: 'json',
        success: function (response) {
            if (response.code == 0) {
                var data = response.data;
                $('.test-result').attr('historyLastBuildid', data);
            }
        }
    });
    
    
    var uri = '/auto/deploy';
    $.ajax({
        url: uri,
        type: 'get',
        data: data,
        dataType: 'json',
        success: function (response) {
            if (response.code == 0) {
            }
        }
    });

    var total = deploynum*jobnum;

    var timeout = setInterval(function()
    {
        sendrequest(deploynum, total);
    },1500 );
    $(".test-result").attr("timernum", timeout);
}

function sendrequest(num, total)
{
    var uri = '/auto/getresultstatus';
    var id = $('.test-result').attr('historyLastBuildid');

    var finishnum = 0;

    for(var i=0; i<num ; i++) {
        var data = {
            id            : (+id)+(+i)+(+1),
            hostid        : i,
            finishnum     : finishnum,
        };

        $.ajax({
            url: uri,
            type: 'get',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.code == 0) {
                    var hostid = response.data.hostid;
                    var finishnum = response.data.finishnum;
                    var resultitem = response.data.resultitem;
                    $.each(configArray, function (index, value)
                    {
                        var result = index + 'result';
                        var tag = index + 'tag';
                        if(resultitem[0][result] == 1 || resultitem[0][result] == 0 || resultitem[0][result] == 2) {
                            var jenkinsBuildId = getTagId(resultitem[0][tag]);
                            changePicture(resultitem[0][result], hostid, index, value['JenkinsJobNameEn'], jenkinsBuildId);
                            if(resultitem[0][result] == 1 || resultitem[0][result] == 0) {
                                finishnum++;
                            }
                        };
                    });
                    if(finishnum == total) {
                        var timeout = $(".test-result").attr("timernum");
                        clearInterval(timeout);
                    }
                }
            }
        });
    }


}

function getTagId(result)
{
    var resultArray = result.split("-");
    var leng = resultArray.length;
    var jenkinsBuildId = resultArray[leng-1];
    return jenkinsBuildId;
}

//更换图片
function changePicture(result, num, jobnum, jobname, id)
{
    var html = '';
    if(result == 1) {
        html += "<a href='http://ci.mars.changbaops.com/job/" + jobname + "/" + id + "'><img src='/static/img/pass.svg'></a>";
    } else if(result == 0) {
        html += "<a href='http://ci.mars.changbaops.com/job/" + jobname + "/" + id + "'><img src='/static/img/fail.svg'></a>";
    } else  {
        html += "<a href='http://ci.mars.changbaops.com/job/" + jobname + "/" + id + "'><img src='/static/img/running.svg'></a>";
    }
    $('.result-pic' + num + jobnum).html(html);
}