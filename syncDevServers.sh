#!/bin/bash

#npx spack entry=/src/EmailTunnel/SettingsPage.js_src/SettingsPage.js output=/src/EmailTunnel

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
LOCAL_DIR="/var/www/html/wp-content/plugins/aion-chat"
REMOTE_DIR="/var/www/html/wp-content/plugins/aion-chat"

# SSH Key location
SSH_KEY="~/ozempic.pem"

# Exclusions for rsync
EXCLUDES=(
    --exclude='.idea'
    --exclude='.git'
    --exclude='bin'
    --exclude='node_modules'
    --exclude='src/plugin-update-checker-4.11'
    --exclude='tests'
    --exclude='vendor'
)

# Function to perform rsync
sync_files () {
    echo "Syncing files to $1..."
    rsync -avz -e "ssh -i $SSH_KEY" "${EXCLUDES[@]}" $LOCAL_DIR/ ubuntu@$1:$REMOTE_DIR
    echo "Sync complete to $1."
}

# Sync once immediately
sync_files $SERVER1
sync_files $SERVER2

# Uncomment the following lines to start watching the directory and sync on changes
#echo "Watching for changes. Ctrl+C to stop."
#while true; do
#    inotifywait -e close_write,moved_to,create $LOCAL_DIR
#    sync_files $SERVER1
#    sync_files $SERVER2
#done
