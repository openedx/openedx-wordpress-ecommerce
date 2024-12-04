#!/usr/bin/env bash

pluginVersion(){

    INPUT_PLUGIN_VERSION="$1"

    case "$INPUT_PLUGIN_VERSION" in
        'tag')
            INPUT_PLUGIN_VERSION=${GITHUB_REF#refs/tags/}
            ;;
        'readme' | '')
            INPUT_PLUGIN_VERSION=$(find "$INPUT_WORKING_DIRECTORY" -iname "README.TXT" -exec grep -oiP -m 1 'stable\s+tag\s*:\s\K.*' {} \;)
            ;;
        
        *)
            #default to provided message
            ;;
    esac

    echo "$INPUT_PLUGIN_VERSION"
}
