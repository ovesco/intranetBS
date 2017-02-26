#!/bin/bash 

function checkDirectory {

	if [ -d $1 ]; then
		# Control will enter here if $DIRECTORY exists.
		echo "$1-->already exist"
	else
		sudo mkdir $1
		echo "$1-->created"
	fi
}
export -f checkDirectory

function message {
	RED='\033[0;31m'
	NC='\033[0m' # No Color
	echo -e "${RED}$1${NC}"
}
export -f message

function package_exists {
    dpkg -s $1 &> /dev/null
}
export -f package_exists

function enable_package {
	# do not use [] if using function in conditional test
	if ! package_exists $1 ; then
    	message "Install <$1>..."
		sudo apt-get -y install $1
	else
		message "Package: <$1> already installed..."
	fi
}
export -f enable_package


