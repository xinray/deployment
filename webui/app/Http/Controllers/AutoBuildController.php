<?php

namespace App\Http\Controllers;

use App\Model\HostInfo;
use App\Model\Items;
use App\Model\DeploymentHistory;
use App\Model\DeployHistory;
use App\Http\Responses\API;
use App\Lib\SvnPeer;
use App\Model\TestResults;
use Log;
use Queue;
use App\Jobs\Deploy;


class AutoBuildController extends Controller
{
    public function index()
    {
        return redirect('/auto/item/list?num=50');
    }

    public function itemList()
    {
        $items = Items::getPage();
        //$items = Items::find(293);
        //dd($items->deployhistory->toArray());
        return view('welcome')->withItems($items);
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
            //$lastupdate = DeploymentHistory::where('guid' ,$lastrelease->id)->orderby('id', 'desc')->first();
            $lastupdate = DeployHistory::where('guid' ,$lastrelease->id)->orderby('id', 'desc')->first();
            //dd($lastupdate);
        } else {
            $lastupdate = null;
        }
        $hostinfo = HostInfo::where('productid', $item->productid)->where('env', 'production')->where('status', 'enabled')->first();
        Log::info('获取最新发布的数据');

        foreach(config('data')['product'] as $product){
            if($product['productid'] == $item->productid) {
                $jenkinsjob = $product['tasks'];
            }
        }

        $config = json_encode($jenkinsjob);
        return view('autodeploy', ['item' => $item, 'smoke' => $smoke, 'uat' => $uat, 'lastrelease' => $lastrelease, 'lastupdate' => $lastupdate, 'hostinfo' => $hostinfo, 'jenkinsjob' => $jenkinsjob, 'config' => $config]);
    }

    public function autoDeploy()
    {
        $hosts = request('hosts');
        $guid = request('guid');
        $productid = request('productid');
        $targetdirectory = request('targetdirectory');

        foreach(config('data')['product'] as $product){
            if($product['productid'] == $productid) {
                $jenkinsjob = $product['tasks'];
            }
        }
        $i = 0;
        foreach($jenkinsjob as $job) {
            $exec = 'curl http://ci.mars.changbaops.com/job/' . $job['JenkinsJobNameEn'] . '/lastBuild/api/json?tree=id -u artifactory:artifactory';
            $result[$i] = json_decode(exec($exec), true);
            $i++;
        }
        $res = [
            'hosts' => $hosts,
            'guid' => $guid,
            'targetdirectory' => $targetdirectory,
            'jenkinsjob' => $jenkinsjob,
            'result'    => $result,
        ];
        Queue::later(6, new Deploy($res));

        return API::success($result);
    }

    public function getLastHistoryDBId()
    {
        $id = DeployHistory::orderby('id', 'desc')->first();
        if($id == null){
            return API::success(0);
        } else {
            return API::success($id->id);
        }
    }

    public function getResultStatus()
    {
        $id = request('id');
        $hostid = request('hostid');
        $finishnum = request('finishnum');
        $resultitem = DeployHistory::where('id', $id)->get()->toArray();
        $data = [
            'resultitem'    => $resultitem,
            'hostid'        => $hostid,
            'finishnum'     => $finishnum,
        ];
        return API::success($data);
    }

    public function getHistoryItems()
    {
        $items = DeployHistory::getHistoryItems();
        $itemfirst = DeployHistory::first();

        if($itemfirst != null) {
            foreach(config('data')['product'] as $product){
                if($product['productid'] == $itemfirst->items->productid) {
                    $jenkinsjob = $product['tasks'];
                }
            }
        } else {
            $jenkinsjob = null;
        }
        
        return view('autohistory', ['items' => $items, 'jenkinsjob' => $jenkinsjob]);
    }



    public function displayDashboard()
    {
        $i = 0;
        foreach(config('data')['product'] as $product){
            $j = 0;
            $product_name[$i] = $product['productid'];
            $monitor = $product['monitor'];
            $monitor_url[$i] = 'http://ci.mars.changbaops.com/view/Mars-Monitor/job/'. $monitor .'/badge/icon';
            $deploy_hosts[$i] = HostInfo::getdeployhosts($product_name[$i]);
            if($deploy_hosts[$i] != null) {
                foreach($deploy_hosts[$i] as $host) {
                    $items[$i][$j] = DeployHistory::getHostItem($host);
                    $j++;
                }
            } else {
                $items[$i] = null;
            }
            $i++;
        }
        //dd($items);

        return view('dashboard', ['items' => $items, 'product_name' => $product_name, 'monitor_url' => $monitor_url]);
    }

}