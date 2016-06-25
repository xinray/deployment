#!/usr/bin/env bash

GUID=$1
testtype=$2
jobname=$3
buildid=$4
mysqlConnectionString=$5
buildtag="jenkins-${jobname}-${buildid}"

echo "=====GUID:   "${GUID}"========"
if [ -z "${GUID}" ]; then
echo "=====No GUID passed in, no need to update artifactory DB.========"
exit 0
fi

jenkinsapi="http://ci.mars.changbaops.com/job/${jobname}/${buildid}/api/json?tree=result"
job_status=`curl -s "${jenkinsapi}" -u artifactory:artifactory | grep "\"result\":\"SUCCESS\""`

echo "jobstatus: ${job_status}"
if [ -n "${job_status}" ]; then
    testresult=1;
else
    testresult=0;
fi
echo "testresult: ${testresult}"

if [ ${testtype} = "smoke" ]; then
    ${mysqlConnectionString} -e "INSERT INTO artifactory.testresults (guid, smoketestresult, smoketestbuildtag)VALUES (${GUID},${testresult},\"${buildtag}\" ) ON DUPLICATE KEY UPDATE  smoketestresult=${testresult}, smoketestbuildtag=\"${buildtag}\"" -N;
elif [ ${testtype} = "uat" ]; then
    ${mysqlConnectionString} -e "INSERT INTO artifactory.testresults (guid, uatresult, uatbuildtag)VALUES (${GUID},${testresult},\"${buildtag}\" ) ON DUPLICATE KEY UPDATE  uatresult=${testresult}, uatbuildtag=\"${buildtag}\"" -N;
fi
