#!/bin/bash 


echo "Start images on docker: $PWD" 

tree .

#echo "service nginx start"
#service nginx start
echo "service mysql start" 
service mysql start

#start nginx service
#to run in background
echo "nginx -g 'daemon off;'"
nginx -g 'daemon off;'

