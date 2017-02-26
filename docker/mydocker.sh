#!/bin/bash 

case "$1" in
        -h|--help)
			echo
            echo "options:"
			echo            
			echo "-h, --help"
			echo "	show brief help"
			echo
			echo "image:list"
			echo "	list all images"
			echo
			echo "image:build <NAME> <TAG>"
			echo "	create image with NAME and TAG"
			echo
            echo "image:remove <image_name:image_tag|image_id>"
			echo "	remove image"
			echo
			echo "container:create <imagename:imagetag> <containername>"
			echo " 	create container from image with the specified name (attach console)"
			echo
			echo "container:list"
			echo " 	list all the containers"
			echo
			echo "container:start <container_name|container_id> [--attach]"
			echo " 	start container"
			echo " 	option --attach: allow to debug but exit the container at the end of script"
			echo
			echo "container:attach:console <container_name>"
			echo " 	attach a console to the container"
			echo
			echo "container:stop <container_name|container_id>"
			echo " 	stop container"
			echo
            exit 0
            ;;

		image:build)
            sudo bash ./docker/setup_image.sh $2 $3
            ;;

        image:remove)
            sudo docker rmi $2
            ;;

		image:remove:all)
            sudo docker rmi $(docker images -aq)
            ;;

		image:list)
            sudo docker images
            ;;

		container:create)
			image=$2
			container_name=$3
			echo "You need to map a port of your machine (host_port) to the port 80 of the container (ex: 8080)"
			read -p "host_port:" host_port
            sudo docker create --tty --interactive --name="$3" --publish $host_port:80 $2
            ;;

		container:start)
		    container_name=$2
		    option=$3
            sudo docker start $container_name $option #--attach
            ;;

		container:stop)
		    container_name=$2
            sudo docker stop $container_name
            ;;

		container:attach:console)
			container_name=$2
            sudo docker exec -it $2 /bin/bash
            ;;

		container:list)
            sudo docker ps -a
            ;;

		container:remove)
            sudo docker rm $2
            ;;

		container:remove:all)
            sudo docker rm $(docker ps -aq)
            ;;


esac

exit 0;

