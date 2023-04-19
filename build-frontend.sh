#!/bin/bash

docker-compose exec frontend-build yarn bundle:release

if [[ -d ./source/frontend_web/app/dist ]] 
then
    sudo rm -rf ./public_html/storage/app/web
    sudo cp -rf ./source/frontend_web/app/dist ./public_html/storage/app/web
fi