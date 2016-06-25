#!/usr/bin/env bash

GUID=$1
targethosts=$2
targetdirectory=$3
mysqlConnectionString=$4

echo "=====GUID:   "${GUID}"========"
if [ -z "${GUID}" ]; then
echo "=====GUID of build from artifactory is required========"
exit 1
fi

echo "=====GUID:   "${targethosts}"========"
if [ -z "${targethosts}" ]; then
echo "=====Target host of build from artifactory is required========"
exit 1
fi

echo "=====GUID:   "${targetdirectory}"========"
if [ -z "${targetdirectory}" ]; then
echo "=====Target directory of build from artifactory is required========"
exit 1
fi

echo "=====Querying build info by GUID: "${GUID}"========"
#buildinfoindex='productid, buildid, svnhost, svnrevision, chechsum, artifactoryhost, artifactorydir, archivetime, status, deploytime'
buildinfo=`${mysqlConnectionString} -e "select * from artifactory.buildinfo where id=${GUID}" -N -s`

#(not working right now).
#if [ -z "${buildinfo}" ]; then
#echo "=====Querying build info by productid: "${GUID}" and buildid: "${BUILDID}"========"
#buildinfo=`${mysqlConnectionString} -e "select * from artifactory.buildinfo where productid=${productid} and buildid=${BUILDID}" -N -s`
#fi

if [ -z "${buildinfo}" ]; then
echo "=====Build infomation not found========"
exit 1
fi

productid=`echo ${buildinfo} | cut -d' ' -f2`
BUILDID=`echo ${buildinfo} | cut -d' ' -f3`
svnhost=`echo ${buildinfo} | cut -d' ' -f4`
svnrevision=`echo ${buildinfo} | cut -d' ' -f5`
checksum=`echo ${buildinfo} | cut -d' ' -f6`
artifactoryhost=`echo ${buildinfo} | cut -d' ' -f7`
artifactorydir=`echo ${buildinfo} | cut -d' ' -f8`
archivetime=`${mysqlConnectionString} -e "select archivetime from artifactory.buildinfo where id=${GUID}" -N`
status=`echo ${buildinfo} | cut -d' ' -f11`
deploytime=`${mysqlConnectionString} -e "select deploytime from artifactory.buildinfo where id=${GUID}" -N`

#constant variable
#targetdir=${artifactorydir}/${productid}/${BUILDID}/

echo "=====Deployment infomation =========="
echo "=====GUID:        "${GUID}
echo "=====Product:     "${productid}
echo "=====Build:       "${BUILDID}
echo "=====SVN host:    "${svnhost}
echo "=====SVN revision:"${svnrevision}
echo "=====Artifactory: "${artifactoryhost}
echo "=====TargetHost:  "${targethosts}
echo "=====TargetDir:   "${targetdirectory}

echo "=====Poping build from artifactory to Jumper======================"
pathToGoldBits=/home/wwwroot/todeploy/${productid}/
jumperHost=192.168.30.99
sudo -u www-data ssh ${jumperHost} "mkdir -p ${pathToGoldBits}"
sudo -u www-data rsync -avp --delete ${artifactorydir}/${productid}/${BUILDID}/ ${jumperHost}:${pathToGoldBits}

echo "=====Deploying from Jumper to host======================"
iplist=$(echo ${targethosts} | tr "," "\n")
bluedir='/home/wwwroot/bluedir/'${productid}'/'
backupdir='/home/wwwroot/bak/'${productid}'/'
numOfBackupsToKeep=3

for ip in ${iplist}
do
    echo ">Deploying to [${ip}]:[${bluedir}]"
    sudo -u www-data ssh ${jumperHost} "ssh ${ip} "mkdir -p ${bluedir}""
    sudo -u www-data ssh ${jumperHost} "ssh ${ip} "chown www-data:www-data ${bluedir}""
    sudo -u www-data ssh ${jumperHost} "rsync -apz --exclude=".svn" --exclude=".git" --delete ${pathToGoldBits} ${ip}:${bluedir}"

    echo ">Verifying checksum for host: [$ip] ..."
    checksumResultInBluedir=`sudo -u www-data ssh ${jumperHost} "ssh ${ip} 'cd '"${bluedir}/"' && find . -type f -exec md5sum {} \; | sort -k 2 | grep -v svninfo.log | md5sum | cut -d\" \" -f1'"`
    echo ">Expected checksum:[$checksum]"
    echo ">Actual checksum for bluedir:[$checksumResultInBluedir]"
    if [ ${checksumResultInBluedir} = ${checksum} ]; then  echo ">Checksum PASS for bluedir"; else echo ">Checksum FAIL for bluedir"; exit 1; fi

    echo ">Backing up online bits..."
    curbuildid=`sudo -u www-data ssh ${jumperHost} "ssh ${ip} 'cat '"${targetdirectory}"'/svninfo.log'" | grep buildid: | sed '1!d' | cut -d':' -f 2 | sed -e 's/^[ \t]*//'`
    echo ">Online version build id: ${curbuildid}"
    sudo -u www-data ssh ${jumperHost} "ssh ${ip} "mkdir -p ${backupdir}/${curbuildid}""
    sudo -u www-data ssh ${jumperHost} "ssh ${ip} ''rsync -ap --exclude=".svn" --exclude=".git" --delete ${targetdirectory}/ ${backupdir}/${curbuildid}''"

    numOfBackups=`sudo -u www-data ssh ${jumperHost} "ssh ${ip} 'ls '"${backupdir}"' | wc -l'"`
    numOfBackupsToDrop=`expr ${numOfBackups} - ${numOfBackupsToKeep}`
    echo "># of dir to delete: ${numOfBackupsToDrop}"
    #if [ ${numOfBackupsToDrop} -gt 0 ]; then sudo -u www-data ssh ${jumperHost} "ssh ${ip} ''ls -tr ${backupdir} | sed "s:^:`pwd`/: " | head -${numOfBackupsToDrop} | xargs rm -rf {}''"; fi
    #if [ ${numOfBackupsToDrop} -gt 0 ]; then sudo -u www-data ssh ${jumperHost} "ssh ${ip} 'ls -tr '"${backupdir}"' | sed "s:^:`echo ${backupdir}`/: " | head '"-${numOfBackupsToDrop}"' | hostname'"; fi
    #if [ ${numOfBackupsToDrop} -gt 0 ]; then sudo -u www-data ssh ${jumperHost} "ssh ${ip} 'ls -tr '"${backupdir}"' | sed "s:^:`echo ${backupdir}`/: " | head '"-${numOfBackupsToDrop}"' | whoami'"; fi
    sudo -u www-data ssh ${jumperHost} "ssh ${ip} ''ls ${backupdir}''"
    #if [ ${numOfBackupsToDrop} -gt 0 ]; then sudo -u www-data ssh ${jumperHost} "ssh ${ip} 'ls -tr '"${backupdir}"' | sed "s:^:`echo ${backupdir}`/: " | head '"-${numOfBackupsToDrop}"' | xargs rm'"; fi
    if [ ${numOfBackupsToDrop} -gt 0 ]; then sudo -u www-data ssh ${jumperHost} "ssh ${ip} 'ls -tr '"${backupdir}"' | sed "s:^:`echo ${backupdir}`/: " | head '"-${numOfBackupsToDrop}"' | xargs rm -rf'"; fi
    sudo -u www-data ssh ${jumperHost} "ssh ${ip} ''ls ${backupdir}''"

    echo ">Copying from [${bluedir}] to [${targetdirectory}]"
    sudo -u www-data ssh ${jumperHost} "ssh ${ip} ''rsync -ap --exclude=".svn" --exclude=".git" --exclude "/cache/photo/" --delete ${bluedir} ${targetdirectory}/''"

    echo ">Verifying checksum for target directory..."
    checksumResultInTargetdir=`sudo -u www-data ssh ${jumperHost} "ssh ${ip} 'cd '"${targetdirectory}/"' && find . -type f -exec md5sum {} \; | sort -k 2 | grep -v svninfo.log | grep -v .svn | grep -v cache/photo/ | md5sum | cut -d\" \" -f1'"`
	echo ">Actual checksum for target dir:[${checksumResultInTargetdir}]"
    if [ ${checksumResultInTargetdir} = ${checksum} ]; then  echo "Checksum PASS for target dir."; else echo "Checksum FAIL for target dir."; exit 1; fi

done

echo '=====Deployment done======================'