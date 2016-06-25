<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use App\Http\Responses\API;
use Log;

class DeployHistory extends Model
{
    protected $table = 'deployhistory';

    protected $guarded = ['id'];

    protected $perpage = 100;

    // 允许更新的字段
    protected $fillable = [
        'guid', 'hostsdeployed', '0tag', '0result', '1tag', '1result', '2tag', '2result', '3tag', '3result',
        '4tag', '4result', '5tag', '5result', '6tag', '6result', '7tag', '7result', '8tag', '8result', '9tag', '9result',
    ];

    // 关闭时间戳
    public $timestamps = false;

    public function items()
    {
        return $this->belongsTo(Items::class, 'guid');
    }

    public static function getHistoryItems()
    {
        $num = request('num');
        $type = request('type');

        if (empty($type)) {
            return redirect('/auto/history/reslult?type=marsapi');
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

    public static function getHostItem($host)
    {
        //Self::where
    }

    public function getItemChangeAttribute()
    {
        $item = array();
        $type = request('type');

        foreach(config('data')['product'] as $product){
            if($product['productid'] == $type) {
                $jenkinsjob = $product['tasks'];
            }
        }

        for ($i = 0; $i < count($jenkinsjob); $i++) {
            $tag = $i.'tag';
            $result = $i.'result';

            $item_pic = $this->getResultpic($this->attributes[$result]);
            $item_jobid = $this->getTagId($this->attributes[$tag]);
            $item_jobname = $this->getTagJob($this->attributes[$tag]);

            $item[$i] = array(
                'result'      =>  $item_pic,
                'jobid'       =>  $item_jobid,
                'jobname'     =>  $item_jobname,
            );
        }
        return $item;
    }

    public function getStage($stage)
    {
        if (isset($this->itemChange[$stage])) {
            return $this->itemChange[$stage];
        } else {
            return null;
        }
    }

    public function getResultpic($stage)
    {
        if($stage == 1) {
            return '/static/img/pass.svg';
        } elseif($stage == NULL) {
            return null;
        } elseif($stage == 0) {
            return '/static/img/fail.svg';
        } elseif($stage == 2)  {
            return '/static/img/running.svg';
        }
    }

    public function getTagId($stage)
    {
        if($stage == null) {
            return null;
        } else {
            $result = explode('-', $stage);
            $num = count($result);
            $tagId = $result[$num-1];
            return $tagId;
        }
    }

    public function getTagJob($stage)
    {
        if($stage == null) {
            return null;
        } else {
            $result = explode('-', $stage);
            $tagJob = $result[1];
            for($x=2; $x<count($result)-1 ; $x++){
                $tagJob .= '-'.$result[$x];
            }
            return $tagJob;
        }
    }
}