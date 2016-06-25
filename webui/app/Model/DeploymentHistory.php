<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Http\Responses\API;
use Log;

class DeploymentHistory extends Model
{
    protected $table = 'deploymenthistory';

    protected $guarded = ['id'];

    protected $perpage = 100;

    // 允许更新的字段
    protected $fillable = [
        'guid', 'hostsdeployed', 'filedistribution', 'filedistributionbuildtag', 'hostverification',
        'hostverificationbuildtag', 'apitest', 'apitestbuildtag', 'lastupdate',
    ];

    // 关闭时间戳
    public $timestamps = false;

    public function items()
    {
        return $this->belongsTo(Items::class, 'guid');
    }

    public static function getHistoryPage()
    {
        $num = request('num');
        $type = request('type');
        if (empty($type)) {
            $items = Self::orderBy('id', 'desc');
        } else {
            $items = self::whereHas('items', function($query) use($type)
            {
                // 增加状态为进行中的才显示
                $query->where('productid', $type);
            })->orderBy('lastupdate', 'desc');
        }

        if(empty($num)) {
            $perpage = 1000;
        } else {
            $perpage = $num;
        }

        return $items->paginate($perpage);
    }

    public static function createItems($id)
    {
        $job0 = '0-Deploy-Mars-Production-Environment';
        $job1 = '1-Mars-Deployment-HostVerify';
        $job2 = '2-Mars-ProductionTest-API';
        $job0id = request('job0id');
        $job1id = request('job1id');
        $job2id = request('job2id');
        $job2result = request('job2result');
        $resultArray = request('resultArray');
        $num = request('num');
        $hosts = request('host');
        $host = explode(",",$hosts);
        for($i=0 ; $i<$num ; $i++) {
            $id0 = $job0id+$i+1;
            $id1 = $job1id+$i+1;
            $id2 = $job2id+1;
            $tag1 = 'jenkins-' . $job0 . '-' . $id0;
            $tag2 = 'jenkins-' . $job1 . '-' . $id1;
            $tag3 = 'jenkins-' . $job2 . '-' . $id2;
            $tof1 = Self::successOrFail($resultArray[$i][0]);
            $tof2 = Self::successOrFail($resultArray[$i][1]);
            $tof3 = Self::successOrFail($job2result);
            $item = self::create([
                'guid'                      => $id,
                'hostsdeployed'             => $host[$i],
                'filedistribution'          => $tof1,
                'filedistributionbuildtag'  => $tag1,
                'hostverification'          => $tof2,
                'hostverificationbuildtag'  => $tag2,
                'apitest'                   => $tof3,
                'apitestbuildtag'           => $tag3,
                'lastupdate'                => date('Y-m-d H:i:s'),
            ]);
            $item->save();
        }
    }

    public static function createItem($id)
    {
        $job1 = '0-Deploy-Mars-Production-Environment';
        $job2 = '1-Mars-Deployment-HostVerify';
        $job3 = '2-Mars-ProductionTest-API';
        $result1 = request('result0');
        $id1 = request('id0');
        $result2 = request('result1');
        $id2 = request('id1');
        $result3 = request('result2');
        $id3 = request('id2');
        Log::info('拼接字符串');
        $tag1 = 'jenkins-' . $job1 . '-' . $id1;
        $tag2 = 'jenkins-' . $job2 . '-' . $id2;
        $tag3 = 'jenkins-' . $job3 . '-' . $id3;
        Log::info($tag1);
        Log::info($tag2);
        Log::info($tag3);
        Log::info('转换结果-begin');
        $tof1 = Self::successOrFail($result1);
        $tof2 = Self::successOrFail($result2);
        $tof3 = Self::successOrFail($result3);
        Log::info($tof1);
        Log::info($tof2);
        Log::info($tof3);
        Log::info('转换结果-finish');
        Log::info('储存结果-begin');

        $item = self::create([
            'guid'                      => $id,
            'hostsdeployed'             => request('hostsdeployed'),
            'filedistribution'          => $tof1,
            'filedistributionbuildtag'  => $tag1,
            'hostverification'          => $tof2,
            'hostverificationbuildtag'  => $tag2,
            'apitest'                   => $tof3,
            'apitestbuildtag'           => $tag3,
            'lastupdate'                => date('Y-m-d H:i:s'),
        ]);
        $item->save();
        Log::info('储存结果-finish');

        return API::success($item);
    }

    public static function successOrFail($result)
    {
        if($result == 'SUCCESS') {
            return intval('1');
        }elseif($result == 'FAILURE') {
            return intval('0');
        }
    }

    public function getFiledistributionResultAttribute()
    {
        if($this->filedistribution == 1) {
            return 'static/img/pass.svg';
        } elseif($this->filedistribution == 0) {
            return 'static/img/fail.svg';
        } else {
            return null;
        }
    }

    public function getHostverificationResultAttribute()
    {
        if($this->hostverification == 1) {
            return '/static/img/pass.svg';
        } elseif($this->hostverification == 0) {
            return '/static/img/fail.svg';
        } else {
            return null;
        }
    }

    public function getApitestResultAttribute()
    {
        $result = 'apite'.'st';
        if($this->$result == 1) {
            return '/static/img/pass.svg';
        } elseif($this->$result == 0) {
            return '/static/img/fail.svg';
        } else {
            return null;
        }
    }

    public function getFiledistributionJobAttribute()
    {
        if($this->filedistributionbuildtag == null) {
            return null;
        } else {
            $result = explode('-', $this->filedistributionbuildtag);
            $filedistributionbuildtag = $result[1];
            for($x=2; $x<count($result)-1 ; $x++){
                $filedistributionbuildtag .= '-'.$result[$x];
            }
            return $filedistributionbuildtag;
        }
    }

    public function getHostverificationJobAttribute()
    {
        if($this->hostverificationbuildtag == null) {
            return null;
        } else {
            $result = explode('-', $this->hostverificationbuildtag);
            $hostverificationbuildtag = $result[1];
            for($x=2; $x<count($result)-1 ; $x++){
                $hostverificationbuildtag .= '-'.$result[$x];
            }
            return $hostverificationbuildtag;
        }
    }

    public function getApitestJobAttribute()
    {
        if($this->apitestbuildtag == null) {
            return null;
        } else {
            $result = explode('-', $this->apitestbuildtag);
            $apitestbuildtag = $result[1];
            for($x=2; $x<count($result)-1 ; $x++){
                $apitestbuildtag .= '-'.$result[$x];
            }
            return $apitestbuildtag;
        }
    }

    public function getFiledistributionIdAttribute()
    {
        if($this->filedistributionbuildtag == null) {
            return null;
        } else {
            $result = explode('-', $this->filedistributionbuildtag);
            $num = count($result);
            $filedistributionbuildid = $result[$num-1];
            return $filedistributionbuildid;
        }
    }

    public function getHostverificationIdAttribute()
    {
        if($this->hostverificationbuildtag == null) {
            return null;
        } else {
            $result = explode('-', $this->hostverificationbuildtag);
            $num = count($result);
            $hostverificationbuildid = $result[$num-1];
            return $hostverificationbuildid;
        }
    }

    public function getApitestIdAttribute()
    {
        if($this->apitestbuildtag == null) {
            return null;
        } else {
            $result = explode('-', $this->apitestbuildtag);
            $num = count($result);
            $apitestbuildid = $result[$num-1];
            return $apitestbuildid;
        }
    }
}