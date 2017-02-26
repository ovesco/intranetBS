#!/bin/bash 
. ./functions.sh


#arguments
image_name=$1
image_tag=$2

#check if docker hierachy is already done, if not => do it.
bash setup_docker_dir.sh

image_dir="/home/docker/images/$image_name"
#script_dir="$image_dir/script"

checkDirectory $image_dir
#checkDirectory $script_dir

#sudo cp ./start_image.sh $script_dir/start_image.sh
#sudo chmod +x $script_dir/start_image.sh

echo "Setup image: $image_name"

docker build --tag="$image_name:$image_tag" .

exit 0;

