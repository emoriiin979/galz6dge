#!/usr/bin/env bash

export WWWUSER=${WWWUSER:-$UID}
export WWWGROUP=${WWWGROUP:-$(id -g)}

if [ $# != 1 ]; then
    echo "INFO: 実行対象のpythonファイルを指定してください。"
    exit 1
fi

docker compose exec python python "$1"
