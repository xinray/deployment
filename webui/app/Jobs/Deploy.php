<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Model\Task;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class Deploy extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $res;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($res)
    {
        $this->res = $res;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $res = $this->res;
        $host = explode(",",$res['hosts']);
        $guid = $res['guid'];
        $targetDir = $res['targetdirectory'];
        $jenkinsBuildID = $res['result'];
        $taskInformation = $res['jenkinsjob'];

        $hostnum = 0;

        foreach($host as $hostip) {
            $jobnum = 0;
            foreach($taskInformation as $taskInfor) {
                if($taskInfor['ExecutionTimes'] == '1') {
                    $task[$hostnum][$jobnum] = new Task($guid, $hostip, 0, $targetDir, $jenkinsBuildID[$jobnum]['id'], $taskInfor);
                } else {
                    $task[$hostnum][$jobnum] = new Task($guid, $hostip, $hostnum, $targetDir, $jenkinsBuildID[$jobnum]['id'], $taskInfor);
                }
                $jobnum++;
            }
            $hostnum++;
        }
        
        for($k = 0; $k<$hostnum; $k++) {
            $historyDBid[$k] = $task[$k][0]->createHistoryDB();
        }

        for($j=0; $j<$jobnum; $j++) {
            for($i=0; $i<$hostnum; $i++) {
                if($task[$i][$j]->requireTrigger == 1) {
                    if($task[$i][$j]->executionTimes == 'N') {
                        $task[$i][$j]->run();
                    }
                }
            }
            if($task[$i-1][$j]->requireTrigger == 1) {
                if($task[$i-1][$j]->executionTimes == 1) {
                    $task[$i-1][$j]->run();
                }
            }
            while(1) {
                $countjob = 0;
                for($i=0; $i<$hostnum; $i++) {
                    $result[$i] = $task[$i][$j]->getresult();
                    $task[$i][$j]->modifyHistoryDB($historyDBid[$i], $j, $result[$i]['result']);
                    if($result[$i]['result'] == 'SUCCESS' || $result[$i]['result'] == 'FAILURE') {
                        $countjob++;
                    }
                }
                if($countjob == $hostnum) {
                    break;
                }
            }
        }
        $task[0][0]->modifyBuildinfoStatus();
    }
}