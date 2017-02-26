#!/bin/bash 

#load functions
. ./functions.sh

docker_dir="/home/docker"
docker_images_dir="$docker_dir/images"

case "$1" in
        -h|--help)
            echo "options:"
            echo "-h, --help                show brief help"
            echo "--setup	  		        setup directory for docker"
            echo "--reset     				remove docker directories"
            exit 0
            ;;
        --setup)
            echo "setup"
			checkDirectory $docker_dir
			checkDirectory $docker_images_dir
            ;;
        --reset)
            echo "reset"
			sudo rm -rf $docker_dir
            ;;
esac

exit 0;

