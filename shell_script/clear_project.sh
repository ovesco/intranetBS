#!/usr/bin/env bash
php app/console cache:clear

#remove the uploaded files
rm -rf uploads