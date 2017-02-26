#!/bin/bash 
#This script is used to automatize the docker installation (not for windows or mac)
#Launch this scrit the very first time you want to install docker.

#load functions
. ./functions.sh

#update package
sudo apt-get update




enable_package curl
enable_package linux-image-extra-$(uname -r)
enable_package linux-image-extra-virtual
enable_package apt-transport-https
enable_package ca-certificates

curl -fsSL https://yum.dockerproject.org/gpg | sudo apt-key add -

apt-key fingerprint 58118E89F3A912897C070ADBF76221572C52609D

read  -n 1 -p "Is there any key from docker.com (y/n):" input

if [ $input = "y" ]; then
	echo #new line
	message "Ok perfect..."
else
	echo #new line
	message "Error: you should have a key from docker.com"
	exit 1;
fi

enable_package software-properties-common

sudo add-apt-repository \
       "deb https://apt.dockerproject.org/repo/ \
       ubuntu-$(lsb_release -cs) \
       main"

sudo apt-get update

enable_package docker-engine

sudo docker run hello-world

exit 0;

