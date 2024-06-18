#!/bin/bash

# Read IPs from the JSON file
SERVER1="3.13.139.91"

# Local and Remote directory paths
LOCAL_DIR1="/var/www/html/wp-content/plugins/aion-chat"
REMOTE_DIR1="/var/www/aion.garden/wp-content/plugins/aion-chat"

LOCAL_DIR2="/var/www/html/wp-content/plugins/aion-mother"
REMOTE_DIR2="/var/www/aion.garden/wp-content/plugins/aion-mother"

# Hardcoded SSH Key location
SSH_KEY="/home/johndee/sportsman.pem"

# Exclusions for rsync
EXCLUDES=(
    --exclude='.idea'
    --exclude='.git'
    --exclude='bin'
    --exclude='node_modules'
    --exclude='src/update-checker'
    --exclude='src/prismjs'
    --exclude='src/action-scheduler'
    --exclude='tests'
    --exclude='vendor'
    --exclude='servers.json'
)

# Function to perform rsync for multiple directories
sync_files () {
    local server=$1
    echo "SYNCING files to $server..."

    rsync -avz -e "ssh -i $SSH_KEY" "${EXCLUDES[@]}" $LOCAL_DIR1/ ubuntu@$server:$REMOTE_DIR1
    echo "Sync complete to $server for $LOCAL_DIR1."

    rsync -avz -e "ssh -i $SSH_KEY" "${EXCLUDES[@]}" $LOCAL_DIR2/ ubuntu@$server:$REMOTE_DIR2
    echo "Sync complete to $server for $LOCAL_DIR2."

}

# Sync once immediately
sync_files $SERVER1
