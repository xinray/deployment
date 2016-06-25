#!/usr/bin/env b#!/usr/bin/env bash

GUID=$1
type=$2
buildtag=$3
hostdeployed=$4
mysqlConnectionString=$5
#buildtag="jenkins-${jobname}-${buildid}"

echo "=====GUID:   "${GUID}"========"
if [ -z "${GUID}" ]; then
echo "=====No GUID passed in, no need to update artifactory DB.========"
exit 0
fi

if [ ${type} = "distribution" ]; then
    ${mysqlConnectionString} -e "INSERT INTO artifactory.deploymenthistory (guid, filedistributionresult, filedistributionbuildtag)VALUES (${GUID},${testresult},\"${buildtag}\" ) ON DUPLICATE KEY UPDATE  smoketestresult=${testresult}, smoketestbuildtag=\"${buildtag}\"" -N;
elif [ ${type} = "hostverify" ]; then
    ${mysqlConnectionString} -e "INSERT INTO artifactory.deploymenthistory (guid, uatresult, uatbuildtag)VALUES (${GUID},${testresult},\"${buildtag}\" ) ON DUPLICATE KEY UPDATE  uatresult=${testresult}, uatbuildtag=\"${buildtag}\"" -N;
elif [ ${type} = "apitest" ]; then
    ${mysqlConnectionString} -e "INSERT INTO artifactory.deploymenthistory (guid, uatresult, uatbuildtag)VALUES (${GUID},${testresult},\"${buildtag}\" ) ON DUPLICATE KEY UPDATE  uatresult=${testresult}, uatbuildtag=\"${buildtag}\"" -N;
fi
