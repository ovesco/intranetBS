# Installation

For linux machine run the following command to install docker
```bash
sh ./docker/install_docker.sh
```
For MacOSX install docker with docker.app


## The mydocker.sh command
All the interaction with docker are passing by the command mydocker located in file 
./docker/mydocker.sh

```bash
#run this to learn more about this command
sh ./docker/mydocker.sh --help
```

##Building your first image

All your image should be identified by name and tag...this is a docker standard, I don't know why...
```bash
#a Dockerfile should be located form where you launch the command, in this case: ./Dockerfile
sh ./docker/mydocker.sh image:build <image name> <image_tag>
```

##Check your first image

If every things goes well, the image you juste created should be in the list of image.
```bash
sh ./docker/mydocker.sh image:list
```
The "Debian" image is present because the created image is build on Debian image.

##Create your first container

A container is based on a image. Create your container with the following command: 
```bash
#<container_name> will be the name of your future container.
sh ./docker/mydocker.sh container:create image_name:image_tag container_name
```
The command will prompt you about the port mapping between your machine and the container.


##Check your first container
If every things goes well, the container you juste created should be in the list of container.
```bash
sh ./docker/mydocker.sh container:list
```
##Start your first container
Very simple:
```bash
sh ./docker/mydocker.sh container:start container_name
```
Note that each time you launch a container, the script "docker/service_start.sh" is executed in the container.

##Check if you container is running
```bash
sh ./docker/mydocker.sh container:list
```
Look at the STATUS column if the your container is "up". If not...bad news...
If your container is up, you can try to address "localhost:8080" (if you chose 8080 in port mapping) in your web browser. 

##Attach console to your container
Ok now you want do some stuff in your container...a console will be usefull:
```bash
sh ./docker/mydocker.sh container:attach:console container_name
```
Then you are in your container. To exit form this container and keep the container running:
```bash
#just lauch exit command:
exit
```

##Stop a container
```bash
sh ./docker/mydocker.sh container:stop container_name
```

##Remvoe container or images

Remove container:
```bash
sh ./docker/mydocker.sh container:remove container_name
#or (id from list)
sh ./docker/mydocker.sh container:remove container_id
```

Remove image:
```bash
sh ./docker/mydocker.sh image:remove image_name
#or (id from list)
sh ./docker/mydocker.sh image:remove image_id
```