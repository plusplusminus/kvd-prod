#!/bin/bash -e

WHICH=$1
if [ -z "$WHICH" ]; then
  echo "please specify 'staging' or 'prod' to choose which environment to deploy to."
  exit 1
fi
cd $(dirname $0) # change into the dir this script lives in
ansible-playbook ./api.yml -i ./hosts.ini --limit $WHICH
echo "done"
