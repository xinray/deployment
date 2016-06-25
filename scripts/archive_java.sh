#!/usr/bin/env bash
#constant variable

productid=$1
BUILDID=$2
svnhost=$3
artifactoryhost=$4
artifactorydir=$5
src_dir=$6
mysqlConnectionString=$7

targetdir=${artifactorydir}/${productid}/${BUILDID}/

echo "=====src dir:   "${src_dir}"========"
if [ -z "${src_dir}" ]; then
echo "=====Source directory of build is required========"
exit 1
fi

echo "=====Archive infomation =========="
echo "=====Product:     "${productid}"======"
echo "=====Build:       "${BUILDID}"========"
echo "=====SVN host:    "${svnhost}"========"
echo "=====Artifactory: "${artifactoryhost}"========"
echo "=====Directory:   "${targetdir}"========"
echo "=====Src Dir:     "${src_dir}"========"

echo "=====Archiving build "${BUILDID}"============================"
sudo mkdir -p ${targetdir}
sudo rsync -av ${src_dir}/* ${targetdir} --exclude=".svn" --exclude=".git" --delete
#checksumvalue=`sudo find ${targetdir} -type f -exec md5sum {} \; | sort -k 2 | md5sum | cut -d' ' -f1`
#sudo find ${targetdir} -type f -exec md5sum {} \; | sort -k 2
curdir=`pwd`
cd ${targetdir}
checksumvalue=`sudo find . -type f -exec md5sum {} \; | sort -k 2 | grep -v svninfo.log | md5sum | cut -d' ' -f1`
cd ${curdir}
svnrevision=`cat ${targetdir}/svninfo.log | grep "版本: " | sed '1!d' | cut -d':' -f 2 | sed -e 's/^[ \t]*//'`
sudo echo "checksum: ${checksumvalue}" >> ${targetdir}/svninfo.log
cat ${targetdir}/svninfo.log

echo "=====Updating build metadata ====================="
sudo echo 'insert into artifactory.buildinfo (productid, buildid, svnhost, svnrevision, checksum, artifactoryhost, artifactorydir, status) values ("'${productid}'", "'${BUILDID}'", "'${svnhost}'", "'${svnrevision}'", "'${checksumvalue}'", "'${artifactoryhost}'", "'${artifactorydir}'", "archived");' | ${mysqlConnectionString}
GUID=`${mysqlConnectionString} -e "select id from artifactory.buildinfo where productid=\"${productid}\" AND buildid=\"${BUILDID}\"" -N`

sudo echo "GUID: ${GUID}" >> ${targetdir}/svninfo.log
sudo echo "productid: ${productid}" >> ${targetdir}/svninfo.log
sudo echo "buildid: ${BUILDID}" >> ${targetdir}/svninfo.log
echo GUID=${GUID} > variable.properties

echo "=====GUID: "${GUID}"========"


