#!/bin/bash

echo "deployment started"

cd /usr/share/nginx/html/vumi

sudo chmod -R 777 /usr/share/nginx/html/vumi/moodledata

git stash

git pull origin stage_release

# docker-compose down

# rm /home/ec2-user/stage_deploy/vumi/frontend/src/environments/environment.ts

# cp /home/ec2-user/stage_deploy/vumi/frontend/src/environments/environment_server.ts /home/ec2-user/stage_deploy/vumi/frontend/src/environments/environment.ts

# docker-compose build --no-cache

# docker-compose up -d