<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Log;

class Items extends Model
{

    protected $table = 'buildinfo';

    protected $guarded = ['id'];

    protected $perpage = 100;

    public $timestamps = false;

    public function test_result()
    {
        return $this->hasOne(TestResults::class, 'guid');
    }

    public function result_history()
    {
        return $this->hasMany(DeploymentHistory::class, 'guid')->take(1);
    }

    public function deployhistory()
    {
        return $this->hasMany(DeployHistory::class, 'guid')->take(1);
    }

    public static function getPage()
    {
    	$num = request('num');
    	$stage = request('stage');
        $type = request('type');

        if (empty($stage)) {
            $items = Items::orderby('id', 'desc');
        } else {
            $items = Items::where('status', $stage)->orderby('id', 'desc');
        }
        
        if (empty($type)) {
        } else {
            $items->where('productid', $type);
        }
        
        if(empty($num)) {
            $perpage = 10000;
        } else {
            $perpage = $num;
        }

		return $items->paginate($perpage);
	}
    
    public static function deploy()
    {
        Log::info('deploy-begin');
        $job = '0-Deploy-Mars-Production-Environment';
        $host = request('host');
        Log::info('host');
        Log::info($host);
        $guid = request('guid');
        Log::info('guid');
        Log::info($guid);
        $targetdirectory = request('targetdirectory');
        Log::info('targetdirectory');
        Log::info($targetdirectory);
        $mysqlConnectionString = 'mysql -h '. $host .' -uroot -p701701';
        Log::info($mysqlConnectionString);
        $deploy = 'curl -X POST "http://ci.mars.changbaops.com/job/' . $job . '/build" -u artifactory:artifactory --data-urlencode json=\'{"parameter": [{"name":"GUID", "value":"' . $guid . '"}, {"name":"targethosts", "value":"'. $host .'"}, {"name":"targetdirectory", "value":"' . $targetdirectory . '"}, {"name":"mysqlConnectionString", "value":"' . $mysqlConnectionString . '"}]}\'';
        $deployresult = exec($deploy);
        Log::info($deploy);
        Log::info('deployResult');
        Log::info($deployresult);
        Log::info(date('Y-m-d H:i:s'));
        return $deploy;
    }

    public static function deployHosts()
    {
        Log::info('deploy-begin');
        $job = '0-Deploy-Mars-Production-Environment';
        $hosts = request('hosts');
        Log::info('hosts');
        Log::info($hosts);
        $guid = request('guid');
        Log::info('guid');
        Log::info($guid);
        $targetdirectory = request('targetdirectory');
        Log::info('targetdirectory');
        Log::info($targetdirectory);
        $host = explode(",",$hosts);
        $i = 0;
        foreach($host as $host) {
            $mysqlConnectionString = 'mysql -h 192.168.32.101 -uroot -p701701';
            Log::info($mysqlConnectionString);
            $deploy = 'curl -X POST "http://ci.mars.changbaops.com/job/' . $job . '/build" -u artifactory:artifactory --data-urlencode json=\'{"parameter": [{"name":"GUID", "value":"' . $guid . '"}, {"name":"targethosts", "value":"'. $host .'"}, {"name":"targetdirectory", "value":"' . $targetdirectory . '"}, {"name":"mysqlConnectionString", "value":"' . $mysqlConnectionString . '"}]}\'';
            $deployresult = exec($deploy);
            Log::info('第' .$i. '次部署');$i++;
            Log::info($deploy);
            Log::info('deployResult');
            Log::info($deployresult);
            Log::info(date('Y-m-d H:i:s'));
        }
        return $deploy;
    }

    public static function modifyResultStage($id)
    {
        $guid = Self::where('id', $id)->update(['status' => 'released']);

        return $guid;
    }

    public static function updateResultStage($id)
    {
        $guid = Self::where('id', $id)->update(['status' => 'released']);

        return $guid;
    }

    public function getStatusNameAttribute()
    {
        if($this->status == 'archived') {
            return '已存档';
        } elseif($this->status == 'released') {
            return '已上线';
        } else {
            return '已删除';
        }
    }

    public function getStatusBackgroundAttribute()
    {
        if($this->status == 'archived') {
            return 'label label-warning';
        } elseif($this->status == 'released') {
            return 'label label-success';
        } else {
            return 'label label-primary';
        }
    }

    public static function getStaticCount($json)
    {
        $statistics_array  = json_decode($json,true);
        $actions  = json_decode(json_encode($statistics_array));
        for($x=0; $x<=count($actions->actions); $x++ ) {
            $count = $actions->actions[$x];
            //dd($actions->actions[8]);
            if(array_key_exists("totalCount", $actions->actions[$x]) && array_key_exists("failCount", $actions->actions[$x]) && array_key_exists("skipCount", $actions->actions[$x])) {
                return $count;
            } else {
                return null;
            }
        }
    }
}