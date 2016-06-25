<?php

namespace App\Http\Controllers;

use App\Model\DeployHistory;
use App\Model\HostInfo;
use App\Model\Items;
use App\Model\DeploymentHistory;
use App\Http\Responses\API;
use App\Lib\SvnPeer;
use App\Model\TestResults;
use Log;


class HomeController extends Controller
{
    public function index()
    {
        return redirect('/item/list?num=50');
    }

    public function itemList()
    {
        $items = Items::getPage();
        return view('welcome')->withItems($items);
    }

    public function getHistoryItems()
    {
        $items = DeploymentHistory::getHistoryPage();
        return view('history')->withItems($items);
    }

	public function itemDetail($id)
    {
    	$item = Items::with( ['result_history' => function ($query) {
            $query->orderBy('id', 'desc')->first();
        }])->find($id);
        $tag = TestResults::getRestultTableBuildIdResult($id);
        $smoke = $tag['smokeResult'];
        $uat = $tag['uatResult'];
        $lastrelease = Items::where('status', 'released')->where('productid', $item->productid)->orderby('id', 'desc')->first();
        if($lastrelease != null) {
            $lastupdate = DeploymentHistory::where('guid' ,$lastrelease->id)->orderby('id', 'desc')->first();
        } else {
            $lastupdate = null;
        }
        $hostinfo = HostInfo::where('productid', $item->productid)->where('env', 'production')->where('status', 'enabled')->first();
        Log::info('获取最新发布的数据');
        return view('detail')->withItem($item)->withSmoke($smoke)->withUat($uat)->withLastrelease($lastrelease)->withLastupdate($lastupdate)->withHostinfo($hostinfo);
    }

    public function getSvnDiff($id)
    {
        $item = Items::find($id);
        $lastrelease = Items::where('status', 'released')->where('productid', $item->productid)->orderby('id', 'desc')->first();
        if($lastrelease == null) {
            $html = '没有最新发布无法获取svn diff';
        } else {
            Log::info('svn-exec-begin');
            Log::info(date('Y-m-d h:i:s'));
            Log::info('svnexecbefore');
	        $svn = 'svn diff -r ' . $item->svnrevision . ':' . $lastrelease->svnrevision . ' ' . $item->svnhost . ' --username niuboyan --password boyan.niu@changba.com 2>&1';
            //$svn = 'svn diff /Users/ray/Desktop/work/ktv_test/';
            exec($svn, $res, $retval);
            Log::info($svn);
            Log::info('svn-exec-after');
            Log::info(date('Y-m-d H:i:s'));
            //Log::info(date('Y-m-d h:i:s'));
            $result = array_unique($res);
            $html = '';
            foreach($result as $l){
                $html .= $l.'<br />';
            }
            Log::info('svn-html拼接');
            Log::info($html);
            Log::info(date('Y-m-d H:i:s'));
            $html = '<div>'.$html.'</div>';
        }
        return API::success($html);
    }

    public function test()
    {
        return view('test');
    }

    public function deployStep($step)
    {
        $job1 = '0-Deploy-Mars-Production-Environment';
        $job2 = '1-Mars-Deployment-HostVerify';
        $job3 = '2-Mars-ProductionTest-API';
        $host = Items::deploy();
        Log::info('deploy-finish');
        Log::info('get-last3Build-jobresult-begin');
        $exec1 = 'curl http://ci.mars.changbaops.com/job/' . $job1 . '/lastBuild/api/json?tree=id -u artifactory:artifactory';
        $exec2 = 'curl http://ci.mars.changbaops.com/job/' . $job2 . '/lastBuild/api/json?tree=id -u artifactory:artifactory';
        $exec3 = 'curl http://ci.mars.changbaops.com/job/' . $job3 . '/lastBuild/api/json?tree=id -u artifactory:artifactory';
        $id1 = json_decode(exec($exec1));
        $id2 = json_decode(exec($exec2));
        $id3 = json_decode(exec($exec3));
        Log::info('get-last3Build-jobresult-finish');
        $id = [
            'id1'        => $id1,
            'id2'        => $id2,
            'id3'        => $id3,
        ];
        Log::info('deploy-after');
        Log::info(date('Y-m-d H:i:s'));
        return API::success($id);
    }

    public function deployHosts()
    {
        $job1 = '0-Deploy-Mars-Production-Environment';
        $job2 = '1-Mars-Deployment-HostVerify';
        $job3 = '2-Mars-ProductionTest-API';
        $host = Items::deployHosts();
        Log::info('deploy-finish');
        Log::info('get-last3Build-jobresult-begin');
        $exec1 = 'curl http://ci.mars.changbaops.com/job/' . $job1 . '/lastBuild/api/json?tree=id -u artifactory:artifactory';
        $exec2 = 'curl http://ci.mars.changbaops.com/job/' . $job2 . '/lastBuild/api/json?tree=id -u artifactory:artifactory';
        $exec3 = 'curl http://ci.mars.changbaops.com/job/' . $job3 . '/lastBuild/api/json?tree=id -u artifactory:artifactory';
        $id1 = json_decode(exec($exec1));
        $id2 = json_decode(exec($exec2));
        $id3 = json_decode(exec($exec3));
        Log::info('get-last3Build-jobresult-finish');
        $id = [
            'id1'        => $id1,
            'id2'        => $id2,
            'id3'        => $id3,
        ];
        Log::info('deploy-after');
        Log::info(date('Y-m-d H:i:s'));
        return API::success($id);
    }

    public function stepResult($step)
    {
        if($step == 'step0') {
            $job = '0-Deploy-Mars-Production-Environment';
        } elseif($step == 'step1') {
            $job = '1-Mars-Deployment-HostVerify';
        } else {
            $job = '2-Mars-ProductionTest-API';
        }
        Log::info('get-lastBuild-jobresult'. $job .'-begin');
        $exec = 'curl http://ci.mars.changbaops.com/job/' . $job . '/lastBuild/api/json?tree=result,id -u artifactory:artifactory';
        $jobresult = exec($exec);
        Log::info($exec);
        Log::info($jobresult);
        Log::info('get-lastBuild-jobresult'. $job .'-begin');
        $jobresult = json_decode($jobresult);
        $result = [
            'result'        => $jobresult,
        ];
        return API::success($result);
    }

    public function hostResult($hostnum)
    {
        $jobid0 = request('jobid0');
        $jobid1 = request('jobid1');
        $job0 = '0-Deploy-Mars-Production-Environment';
        $job1 = '1-Mars-Deployment-HostVerify';
        //$job = '2-Mars-ProductionTest-API';
        Log::info('get-lastBuild-jobresult-begin');
        $exec0 = 'curl http://ci.mars.changbaops.com/job/' . $job0 . '/' . $jobid0 .'/api/json?tree=result,id -u artifactory:artifactory';
        $exec1 = 'curl http://ci.mars.changbaops.com/job/' . $job1 . '/' . $jobid1 .'/api/json?tree=result,id -u artifactory:artifactory';
        $jobresult0 = exec($exec0);
        $jobresult1 = exec($exec1);
        Log::info($exec0);
        Log::info($exec1);
        Log::info($jobresult0);
        Log::info($jobresult1);
        $jobresult0 = json_decode($jobresult0);
        $jobresult1 = json_decode($jobresult1);

        if($jobresult0 == null ) {
            $exec0 = 'curl http://ci.mars.changbaops.com/job/' . $job0 . '/lastBuild/api/json?tree=result,id -u artifactory:artifactory';
            $jobresult0 = exec($exec0);
            $jobresult0 = json_decode($jobresult0);
        }
        if($jobresult1 == null ) {
            $exec1 = 'curl http://ci.mars.changbaops.com/job/' . $job1 . '/lastBuild/api/json?tree=result,id -u artifactory:artifactory';
            $jobresult1 = exec($exec1);
            $jobresult1 = json_decode($jobresult1);
        }
        $result = [
            'result0'        => $jobresult0,
            'result1'        => $jobresult1,
            'hostnum'        => $hostnum,
        ];
        return API::success($result);
    }

    public function deployJobLast()
    {
        $exec = 'curl -X POST "http://ci.mars.changbaops.com/job/2-Mars-ProductionTest-API/build" -u artifactory:artifactory';
        $execresult = exec($exec);
        return API::success($execresult);
    }

    public function jobLastResult()
    {
        $lastjobid = request('lastjobid');
        $job = '2-Mars-ProductionTest-API';
        $exec = 'curl http://ci.mars.changbaops.com/job/' . $job . '/' . $lastjobid .'/api/json?tree=result,id -u artifactory:artifactory';
        $jobresult = exec($exec);
        $jobresult = json_decode($jobresult);
        if($jobresult == null ) {
            $exec = 'curl http://ci.mars.changbaops.com/job/' . $job . '/lastBuild/api/json?tree=result,id -u artifactory:artifactory';
            $jobresult = exec($exec);
            $jobresult = json_decode($jobresult);
        }
        $result = [
            'lastjobid'        => $lastjobid,
            'result'        => $jobresult,
        ];
        return API::success($result);
    }

    public function createHistoryResult($id)
    {
        $result = DeploymentHistory::createItems($id);

        return API::success($result);
    }

    public function postHistoryResult($id)
    {
        $result = DeploymentHistory::createItem($id);

        return API::success($result);
    }

    public function modifyResultStage($id)
    {
        $result = Items::modifyResultStage($id);

        return API::success($result);
    }

    public function updateResultStage($id)
    {
        $result = Items::updateResultStage($id);

        return API::success($result);
    }

    public function getStaticCount($id)
    {
        $result = Items::modifyResultStage($id);

        return API::success($result);
    }

}
