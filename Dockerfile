#
# DOCKERFILE
# used to create images for this app
# /!\ should be located on root of the app!!!
#


#image de bse
FROM debian

#update for last version
RUN apt-get update

#some usefull command
#diplay file hierachy in console...very usefull
RUN apt-get install -y tree
RUN apt-get install -y curl
#provide: arp, ifconfig, netstat, rarp, nameif and route
RUN apt-get install -y net-tools
RUN apt-get install -y lsof

#install server nginx
RUN apt-get install -y nginx
#setup custom conf for nginx
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf


#install server mysql-server
#setup password "root" for mysql with no user prompt during install
RUN echo "mysql-server-5.5 mysql-server/root_password password root" | debconf-set-selections
RUN echo "mysql-server-5.5 mysql-server/root_password_again password root" | debconf-set-selections
RUN apt-get install -y mysql-server

#install phpMyAdmin
#setup some parameters to avoide user prompt during intall
RUN echo "phpmyadmin phpmyadmin/internal/skip-preseed boolean true" | debconf-set-selections
RUN echo "phpmyadmin phpmyadmin/reconfigure-webserver multiselect" | debconf-set-selections
RUN echo "phpmyadmin phpmyadmin/dbconfig-install boolean false" | debconf-set-selections
RUN apt-get install -y phpmyadmin

#setup link for phpmyadmin on db_admin :localhost/db_admin => phpmyadmin
RUN ln -s /usr/share/phpmyadmin /usr/share/nginx/html/db_admin
#RUN php5enmod mcrypt
#RUN service php5-fpm restart

# 80:nginy 3306:mysql
#EXPOSE 80 #3306

WORKDIR /home/docker/images/test

#add file to the image filesystem WORKDIR/src
ADD . src/



#si on veut garder les fichier dans le container
#COPY content /usr/share/nginx/html
#COPY conf /etc/nginx
VOLUME /usr/share/nginx/html
#VOLUME /etc/nginx

#run all the needed script to get ready the image
RUN chmod 744 src/docker/service_start.sh


#the script to lunch the image
ENTRYPOINT ["src/docker/service_start.sh"]


#not used anymore...keep to remember that is not good way
#to use in service_start.sh
####CMD ["nginx", "-g", "daemon off;"]
