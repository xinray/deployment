<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Log;

class TestResults extends Model
{

    protected $table = 'testresults';

    public function items()
    {
        return $this->belongsTo(Item::class, 'id');
    }

    public static function getRestultTableBuildIdResult($id)
    {
        $testresult = Self::where('guid', $id)->orderBy('guid', 'desc')->first();
        if($testresult != null) {
            $smokejob = Self::getResultTableBuildName($testresult->smoketestbuildtag);
            $uatjob = Self::getResultTableBuildName($testresult->uatbuildtag);
            $smokeId = Self::getResultTableBuildId($testresult->smoketestbuildtag);
            $uatId = Self::getResultTableBuildId($testresult->uatbuildtag);
            $smokeResult = Self::getResultTableStaticResult($smokejob, $smokeId);
            $uatResult = Self::getResultTableStaticResult($uatjob, $uatId);
        } else {
            $smokeResult = null;
            $uatResult = null;
        }

        $result = [
            'smokeResult'  => $smokeResult,
            'uatResult'  => $uatResult,
        ];
        return $result;
    }

    public function getResultSmokeAttribute()
    {
        if($this->smoketestresult == '1') {
            return 'static/img/pass.svg';
        } elseif($this->smoketestresult == '0') {
            return 'static/img/fail.svg';
        } else {
            return null;
        }
    }

    public function getResultUtaAttribute()
    {
        if($this->uatresult == '1') {
            return '/static/img/pass.svg';
        } elseif($this->uatresult == '0') {
            return '/static/img/fail.svg';
        } else {
            return null;
        }
    }

    public function getSmokeresultJobAttribute()
    {
        if($this->smoketestbuildtag == null) {
            return null;
        } else {
            $result = explode('-', $this->smoketestbuildtag);
            $smoketestbuildtag = $result[1];
            for($x=2; $x<count($result)-1 ; $x++){
                $smoketestbuildtag .= '-'.$result[$x];
            }
            return $smoketestbuildtag;
        }
    }

    public function getUtaresultJobAttribute()
    {
        if($this->uatbuildtag == null) {
            return null;
        } else {
            $result = explode('-', $this->uatbuildtag);
            $uatbuildtag = $result[1];
            for($x=2; $x<count($result)-1 ; $x++){
                $uatbuildtag .= '-'.$result[$x];
            }
            return $uatbuildtag;
        }
    }

    public function getSmokeresultIdAttribute()
    {
        if($this->smoketestbuildtag == null) {
            return null;
        } else {
            $result = explode('-', $this->smoketestbuildtag);
            $num = count($result);
            $smokeid = $result[$num-1];
            return $smokeid;
        }
    }

    public function getUtaresultIdAttribute()
    {
        if($this->uatbuildtag == null) {
            return null;
        } else {
            $result = explode('-', $this->uatbuildtag);
            $num = count($result);
            $uatbuildid = $result[$num-1];
            return $uatbuildid;
        }
    }

    public static function getResultTableBuildId($resulttag)
    {
        if($resulttag == null) {
            return null;
        } else {
            $result = explode('-', $resulttag);
            $num = count($result);
            $id = $result[$num-1];
            return $id;
        }
    }

    public static function getResultTableBuildName($resulttag)
    {
        if($resulttag == null) {
            return null;
        } else {
            $result = explode('-', $resulttag);
            $jobname = $result[1];
            for($x=2; $x<count($result)-1 ; $x++){
                $jobname .= '-'.$result[$x];
            }
            return $jobname;
        }
    }

    public static function getResultTableStaticResult($job, $id)
    {
        if($id == null) {
            return null;
        } else {
            Log::info($job . '-统计json数值获取-开始');
            $json = exec('curl http://ci.mars.changbaops.com/job/'. $job . '/'. $id .'/api/json -u artifactory:artifactory');
            Log::info('curl http://ci.mars.changbaops.com/job/' . $job . '/'. $id .'/api/json -u artifactory:artifactory');
            Log::info($json);
            Log::info($job . '-统计json数值获取-完成');
            $statistics  = json_decode($json,true);
            $actions  = json_decode(json_encode($statistics));

            if($actions != null) {
                for($x=0; $x<=count($actions->actions); $x++ ) {
                    $count = $actions->actions[$x];
                    if(array_key_exists("totalCount", $count) && array_key_exists("failCount", $count) && array_key_exists("skipCount", $count)) {
                        $staticResult = $count;
                        break;
                    } else {
                        $staticResult = null;
                    }
                }
            } else {
                $staticResult = null;
            }
            $staticResult = json_decode(json_encode($staticResult), true);
            return $staticResult;
        }
    }
}