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
        startRequest(index, value, i);
        html +='<tr><td class="result-host">' + value + '</td><td class="result-pic0'+ i +'">未开始</td><td class="result-pic1'+ i +'">未开始</td><td class="result-pic2'+ i +'">未开始</td></tr>';
        i++;
    });
    $('.resultpic').html(html);
}
//定时轮训ajax
function startRequest(index, value, i)
{
    $(".test-result").attr("beginnum" , 0);
    $(".test-result").attr("jobnum" , 0);
    $(".test-result").attr("isdeploy" , 0);
    var timeout = setInterval(function()
    {
        sendrequest(value, i);
    },1500);
    $(".test-result").attr("result" + i , timeout);
}

function sendrequest(value, i)
{
    var beginnum   = $(".test-result").attr("beginnum");
    var jobnum     = $(".test-result").attr("jobnum");
    var isdeploy   = $(".test-result").attr("isdeploy");

    if(beginnum == i) {
        if(isdeploy == '0') {
            var uri = '/hosts/deploy/step' + jobnum;
            var data = {
                deploy              : 'deploy',
                jobnum              : jobnum,
                targetdirectory     : $('.targetdirectory').val(),
                guid                : $('.hostsname').attr("guid"),
                host                : value,
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
                        var timeout = $(".test-result").attr("result" + i);
                        clearInterval(timeout);
                        $(".test-result").attr("isdeploy" , 1);
                        var timeout = setInterval(function()
                        {
                            sendrequest(value, i);
                        },1500);
                        $(".test-result").attr("result" + i , timeout);
                    }
                }
            });
        } else {
            var uri = '/hosts/deploy/jobresult/step' + jobnum;
            $.ajax({
                url: uri,
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    if (response.code == 0) {
                        var data = response.data;
                        var result = data.result.result;
                        var id = data.result.id;
                        var lastbulid_id = $(".test-result").attr("bulid_id"+jobnum);
                        var nowbulid_id = (+lastbulid_id)+(+1);
                        if(id == nowbulid_id) {
                            changePicture(result, jobnum, i, id);
                            if (result == null) {

                            } else {
                                $(".test-result").attr("jobresult"+jobnum , result);
                                $(".test-result").attr("jobid"+jobnum , id);
                                if(jobnum == '2') {
                                    $(".test-result").attr("jobnum" , 0);
                                    var timeout = $(".test-result").attr("result" + i);
                                    $(".test-result").attr("beginnum", i + 1);
                                    var jobresult0 = $(".test-result").attr("jobresult0");
                                    var jobid0     = $(".test-result").attr("jobid0");
                                    var jobresult1 = $(".test-result").attr("jobresult1");
                                    var jobid1     = $(".test-result").attr("jobid1");
                                    var jobresult2 = $(".test-result").attr("jobresult2");
                                    var jobid2     = $(".test-result").attr("jobid2");
                                    var href = window.location.pathname;
                                    if(jobresult0 == null || jobresult1 == null || jobresult2 == null) {
                                    } else {
                                        var uri = '/history/result' + href;
                                        data = {
                                            'id0': jobid0,
                                            'result0': jobresult0,
                                            'id1': jobid1,
                                            'result1': jobresult1,
                                            'id2': jobid2,
                                            'result2': jobresult2,
                                            'hostsdeployed': value,
                                        };
                                        $.ajax({
                                            url: uri,
                                            type: 'post',
                                            data: data,
                                            dataType: 'json',
                                            success: function (response) {
                                                //alert('已存入结果历史,可关闭刷新页面');
                                                $(".test-result").attr("isdeploy" , 0);
                                                $(".test-result").attr("jobresult0", null);
                                                $(".test-result").attr("jobresult1", null);
                                                $(".test-result").attr("jobresult2", null);
                                            }
                                        });
                                    }
                                    if(jobresult0 == 'SUCCESS' || jobresult1 == 'SUCCESS' || jobresult2 == 'SUCCESS') {
                                        var uri = '/modify/resultstage' + href;
                                        data = {
                                            'id0': jobid0,
                                            'result0': jobresult0,
                                            'id1': jobid1,
                                            'result1': jobresult1,
                                            'id2': jobid2,
                                            'result2': jobresult2,
                                            'hostsdeployed': value,
                                        };
                                        $.ajax({
                                            url: uri,
                                            type: 'post',
                                            data: data,
                                            dataType: 'json',
                                            success: function (response) {
                                                //alert('已存入结果历史,可关闭刷新页面');
                                            }
                                        });
                                    }
                                    clearInterval(timeout);
                                } else {
                                    jobnum++;
                                    $(".test-result").attr("jobnum" , jobnum);
                                }
                            }
                        }
                    }
                }
            });

        }
    }
}
//更换图片
function changePicture(result, num, jobnum, id)
{
    var html = '';
    var step = '0';
    if(num == '0') {
        step = '0-Deploy-Mars-Production-Environment';
    } else if(num == '1') {
        step = '1-Mars-Deployment-HostVerify';
    } else if(num == '2') {
        step = '2-Mars-ProductionTest-API';
    }

    if(result == 'SUCCESS') {
        html += "<a href='http://ci.mars.changbaops.com/job/" + step + "/" + id + "'><img src='{{ asset('/static/img/pass.svg') }}'></a>";
    } else if(result == 'FAILURE') {
        html += "<a href='http://ci.mars.changbaops.com/job/" + step + "/" + id + "'><img src='{{ asset('/static/img/fail.svg') }}'></a>";
    } else  {
        html += "<a href='http://ci.mars.changbaops.com/job/" + step + "/" + id + "'><img src='{{ asset('/static/img/running.svg') }}'></a>";
    }
    $('.result-pic' + num + jobnum).html(html);
}

function sleep(n)
{
    var start=new Date().getTime();
    while(true) if(new Date().getTime()-start>n) break;
}