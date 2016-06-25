<?php

namespace App\Model;

use App\Model\DeployHistory;

class Task
{
    public $guid;
    public $host;
    public $targetDir;
    public $jenkinsJobName;
    public $jenkinsBuildID;
    public $requireTrigger;
    public $sequence;
    public $executionTimes;
    public $result;
    public $triggerCurlCommend;
    public $getStatusCurlCommend;
    public $buildTag;

    //function __construct($guid, $host, $index, $targetDir, $jenkinsBuildID, $taskInformation) {
    function __construct($guid, $host, $index, $targetDir, $jenkinsBuildID, $taskInformation) {
        $this->guid = $guid;
        $this->host = $host;
        $this->targetDir = $targetDir;
        $this->jenkinsBuildID = $jenkinsBuildID + $index +1;
        $this->jenkinsJobName = $taskInformation['JenkinsJobNameEn'];
        $this->requireTrigger = $taskInformation['RequireTrigger'];
        $this->sequence = $taskInformation['Sequence'];
        $this->executionTimes = $taskInformation['ExecutionTimes'];

        $parameter = '{"name":"' . $taskInformation['parameters'][0]['name'] . '", "value":"' . $this->guid . '"}';
        if(count($taskInformation['parameters']) >=1) {
            for($i = 1; $i<count($taskInformation['parameters']); $i++) {
                if($taskInformation['parameters'][$i]['name'] == 'targethosts') {
                    $parameter = $parameter . ',{"name":"' . $taskInformation['parameters'][$i]['name'] . '", "value":"' . $this->host . '"}';
                } elseif($taskInformation['parameters'][$i]['name'] == 'targetdirectory') {
                    $parameter = $parameter . ',{"name":"' . $taskInformation['parameters'][$i]['name'] . '", "value":"' . $this->targetDir . '"}';
                } else {
                    $parameter = $parameter . ',{"name":"' . $taskInformation['parameters'][$i]['name'] . '", "value":"' . $taskInformation['parameters'][$i]['value'] . '"}';
                }
            }
        }
        $this->triggerCurlCommend = 'curl -X POST "http://ci.mars.changbaops.com/job/' . $this->jenkinsJobName . '/build" -u artifactory:artifactory --data-urlencode json=\'{"parameter": [' . $parameter .']}\'';
        $this->getStatusCurlCommend = 'curl http://ci.mars.changbaops.com/job/' . $this->jenkinsJobName . '/' . $this->jenkinsBuildID .'/api/json?tree=result -u artifactory:artifactory';
        $this->buildTag = 'jenkins-' . $this->jenkinsJobName . '-' . $this->jenkinsBuildID;
    }

    function __get($property_name) {
        return isset($this->$property_name) ? $this->$property_name : null;
    }

    public function createHistoryDB() {
        $id = DeployHistory::create([
                'guid' => $this->guid,
                'hostsdeployed' => $this->host,
            ]);
        return $id->id;
    }

    public function run() {
        $deploy = exec($this->triggerCurlCommend);
        return $deploy;
    }

    public function getresult() {
        //$test = 'curl http://ci.mars.changbaops.com/job/0-deploy-job/14/api/json?tree=result -u artifactory:artifactory';
        $jobresult = exec($this->getStatusCurlCommend);
        //$jobresult = exec($test);
        $result = json_decode($jobresult, true);
        return $result;
    }
    
    public function resultToInt($result) {
        if($result == 'SUCCESS') {
            return intval('1');
        } elseif($result == 'FAILURE') {
            return intval('0');
        } elseif($result == null) {
            return intval('2');
        } else {
            return null;
        }
    }

    public function modifyHistoryDB($historyid, $jobid, $result) {
        $resultInt = $this->resultToInt($result);
        $this->result = $resultInt;
        $guid = DeployHistory::where('id', $historyid)->update([ $jobid . 'result'  => $this->result, $jobid . 'tag'  => $this->buildTag]);
        return $guid;
    }

    public function modifyBuildinfoStatus() {
        $guid = Items::where('id', $this->guid)->update(['status' => 'released']);
        return $guid;
    }
}