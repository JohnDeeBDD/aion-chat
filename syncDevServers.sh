#!/bin/bash

# Path to the JSON file containing the server IPs
JSON_FILE="servers.json"

# Check if the JSON file exists
if [ ! -f "$JSON_FILE" ]; then
    echo "JSON file not found: $JSON_FILE"
    exit 1
fi

# Read IPs from the JSON file
SERVER1=$(jq -r '.[0]' $JSON_FILE)
SERVER2=$(jq -r '.[1]' $JSON_FILE)

# Local and Remote directory paths
LOCAL_DIR1="/var/www/html/wp-content/plugins/aion-chat"
REMOTE_DIR1="/var/www/html/wp-content/plugins/aion-chat"

LOCAL_DIR2="/var/www/html/wp-content/plugins/aion-mother"
REMOTE_DIR2="/var/www/html/wp-content/plugins/aion-mother"

LOCAL_DIR3="/var/www/html/wp-content/plugins/aion-dialectic"
REMOTE_DIR3="/var/www/html/wp-content/plugins/aion-dialectic"

# Hardcoded SSH Key location
SSH_KEY="/home/johndee/ozempic.pem"

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
)

# Function to perform rsync for multiple directories
sync_files () {
    local server=$1
    echo "SYNCING files to $server..."

    rsync -avz -e "ssh -i $SSH_KEY" "${EXCLUDES[@]}" $LOCAL_DIR1/ ubuntu@$server:$REMOTE_DIR1
    echo "Sync complete to $server for $LOCAL_DIR1."

    rsync -avz -e "ssh -i $SSH_KEY" "${EXCLUDES[@]}" $LOCAL_DIR2/ ubuntu@$server:$REMOTE_DIR2
    echo "Sync complete to $server for $LOCAL_DIR2."

    rsync -avz -e "ssh -i $SSH_KEY" "${EXCLUDES[@]}" $LOCAL_DIR3/ ubuntu@$server:$REMOTE_DIR3
    echo "Sync complete to $server for $LOCAL_DIR3."
}

# Sync once immediately
sync_files $SERVER1
sync_files $SERVER2
