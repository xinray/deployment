#!/usr/bin/env bash
srcdir=$1
targethost=$2
targetdir=$3

jumperHost=192.168.30.99
tempdir=/home/wwwroot/todeploy/tmp/

echo ">move to jumper"
sudo -u www-data rsync -avp --delete ${srcdir}/ ${jumperHost}:${tempdir}/

echo ">move to host"
sudo -u www-data ssh ${jumperHost} "rsync -apz ${tempdir}/ ${targethost}:${targetdir}/"