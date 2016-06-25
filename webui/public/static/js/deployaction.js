// 配置API的base_url
var base_url = '';

/**
 * 获取完整API的URL
 */
function url(uri) {
    return base_url + uri;
}

$(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
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
    startRequests();
}

//定时轮训ajax
function startRequests()
{
    var hosts = $('.hostsname').val();
    var host = hosts.split(',');
    var i = 0;
    var html = '';
    $.each(host, function( index, value ) {
        html +='<tr><td class="result-host">' + value + '</td><td class="result-pic0'+ i +'">未开始</td><td class="result-pic1'+ i +'">未开始</td><td class="result-pic2'+ i +'">未开始</td></tr>';
        i++;
    });

    /**
     * 定义的数组
     */
    var resultArray = new Array();  //先声明一维
    for(var k=0;k<i;k++){    //一维长度为i,i为变量，可以根据实际情况改变
        resultArray[k]=new Array();  //声明二维，每一个一维数组里面的一个元素都是一个数组；
        for(var j=0;j<2;j++){   //一维数组里面每个元素数组可以包含的数量p，p也是一个变量；
            resultArray[k][j]=null;    //这里将变量初始化，我这边统一初始化为空，后面在用所需的值覆盖里面的值
        }
    }

    $('.resultpic').html(html);

    var uri = '/hosts/deployhosts';
    var data = {
        //deploy             : 'deploy',
        targetdirectory      : $('.targetdirectory').val(),
        guid                 : $('.hostsname').attr("guid"),
        hosts                : hosts,
    };

    $.ajax({
        url: uri,
        type: 'get',
        data: data,
        dataType: 'json',
        success: function (response) {
            if (response.code == 0) {
                var data = response.data;
                var id1 = data['id1']['id'];
                var id2 = data['id2']['id'];
                var id3 = data['id3']['id'];
                $(".test-result").attr("bulid_id"+0, id1);
                $(".test-result").attr("bulid_id"+1, id2);
                $(".test-result").attr("bulid_id"+2, id3);
                var timeout = setInterval(function()
                {
                    sendrequest(i,resultArray);
                },1500 );
                $(".test-result").attr("result", timeout);
            }
        }
    });
}

function sendrequest(num,resultArray)
{
    for(var i= 0; i < num; i++) {
        var lastbulid_job0_id = $(".test-result").attr("bulid_id0");
        var lastbulid_job1_id = $(".test-result").attr("bulid_id1");
        var nowbulid_job0_id = (+lastbulid_job0_id)+(+i)+(+1);
        var nowbulid_job1_id = (+lastbulid_job1_id)+(+i)+(+1);
        var uri = '/hosts/deployresult/hostresult/' + i ;
        var data = {
            jobid0                : nowbulid_job0_id,
            jobid1                : nowbulid_job1_id,
        };
        $.ajax({
            url: uri,
            type: 'get',
            data: data,
            dataType: 'json',
            success: function (response) {
                if (response.code == 0) {
                    var data = response.data;
                    var result0 = data.result0.result;
                    var id0 = data.result0.id;
                    var result1 = data.result1.result;
                    var id1 = data.result1.id;
                    var hostnum = data.hostnum;
                    var nowbuild_job_id0 = (+lastbulid_job0_id)+(+hostnum)+(+1);
                    var nowbuild_job_id1 = (+lastbulid_job1_id)+(+hostnum)+(+1);
                    if(id0 == nowbuild_job_id0) {
                        changePicture(result0, hostnum, 0, nowbuild_job_id0);
                        resultArray[hostnum][0] = result0;
                    }
                    if(id1 == nowbuild_job_id1) {
                        changePicture(result1, hostnum, 1, nowbuild_job_id1);
                        resultArray[hostnum][1] = result1;
                    }
                }
            }
        });
    }
    var totalnum = 0;
    for(var i= 0; i < num; i++) {
        for(var j=0; j<2; j++) {
            if(resultArray[i][j] == null) {
                break;
            } else {
                totalnum++;
            }
        }
    }

    if(totalnum == num*2) {
        var timeout = $(".test-result").attr("result");
        clearInterval(timeout);
        deployjob3(num,resultArray);
    }

}

function deployjob3(num,resultArray)
{
    var uri = '/hosts/deployjoblast';
    $.ajax({
        url: uri,
        type: 'get',
        dataType: 'json',
        success: function (response) {
            if (response.code == 0) {
                var timeout = setInterval(function()
                {
                    getlastdeployresult(num,resultArray);
                },1500 );
                $(".test-result").attr("result", timeout);
            }
        }
    });
}
function getlastdeployresult(num,resultArray)
{
    var lastjobid = $(".test-result").attr("bulid_id2");
    var data = {
        guid                 : $('.hostsname').attr("guid"),
        lastjobid            : (+lastjobid)+(+1),
    };
    $.ajax({
        url: '/hosts/jobLast/result',
        type: 'get',
        data: data,
        dataType: 'json',
        success: function (response) {
            if (response.code == 0) {
                var data = response.data;
                var result = data.result;
                if(result.id == (+lastjobid)+(+1)) {
                    for (var i = 0; i < num; i++) {
                        changePicture(result.result, i, 2, data.lastjobid);
                        if (result.result != null) {
                            var timeout = $(".test-result").attr("result");
                            clearInterval(timeout);
                        }
                    }
                    if (result.result != null) {
                        createHistoryResult(num, resultArray, result.result);
                    }
                }
            }
        }
    });
}

function createHistoryResult(num, resultArray , job2result)
{
    var href = window.location.pathname;
    var uri = '/hosts/createhistory/result' + href;
    var host = $('.hostsname').val();
    var job0id = $(".test-result").attr("bulid_id0");
    var job1id = $(".test-result").attr("bulid_id1");
    var job2id = $(".test-result").attr("bulid_id2");
    data = {
        'job0id': job0id,
        'job1id': job1id,
        'job2id': job2id,
        'job2result': job2result,
        'resultArray': resultArray,
        'num': num,
        'host': host,
    };
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
    var uri = '/hosts/modify/resultstage' + href;
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
}

//更换图片
function changePicture(result, num, jobnum, id)
{
    var html = '';
    var step = '0';
    if(jobnum == '0') {
        step = '0-Deploy-Mars-Production-Environment';
    } else if(jobnum == '1') {
        step = '1-Mars-Deployment-HostVerify';
    } else if(jobnum == '2') {
        step = '2-Mars-ProductionTest-API';
    }

    if(result == 'SUCCESS') {
        html += "<a href='http://ci.mars.changbaops.com/job/" + step + "/" + id + "'><img src='/static/img/pass.svg'></a>";
    } else if(result == 'FAILURE') {
        html += "<a href='http://ci.mars.changbaops.com/job/" + step + "/" + id + "'><img src='/static/img/fail.svg'></a>";
    } else  {
        html += "<a href='http://ci.mars.changbaops.com/job/" + step + "/" + id + "'><img src='/static/img/running.svg'></a>";
    }
    $('.result-pic' + jobnum + num).html(html);
}