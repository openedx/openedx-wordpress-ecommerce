#!/bin/bash

# Environment
if [[ -z "$INPUT_SVN_USERNAME" ]]; then
	echo "Set the svn-username secret"
	exit 1
fi

if [[ -z "$INPUT_SVN_PASSWORD" ]]; then
	echo "Set the svn-password secret"
	exit 1
fi

if [[ -z "$INPUT_PLUGIN_REPOSITORY" ]]; then
	echo "Set the svn-repository"
	exit 1
fi

DIRECTORY_SRC="$GITHUB_ACTION_PATH/src"

. "$DIRECTORY_SRC/working-directory.sh" 
. "$DIRECTORY_SRC/plugin-version.sh" 

# Normalize environment
INPUT_PLUGIN_VERSION=$(pluginVersion "$INPUT_PLUGIN_VERSION")
INPUT_WORKING_DIRECTORY=$(workingDirectory "$INPUT_WORKING_DIRECTORY")
INPUT_ASSETS_DIRECTORY=$(assetsDirectory "$INPUT_ASSETS_DIRECTORY")

# svn working directory
SVN_DIRECTORY=$(mktemp -d -p "$GITHUB_WORKSPACE")

# Check out svn repository
echo "➤ Checking out $INPUT_PLUGIN_REPOSITORY"
svn checkout --depth immediates "$INPUT_PLUGIN_REPOSITORY" "$SVN_DIRECTORY"

# switch to svn working directory
echo "➤ Switching to svn working directory"
cd "$SVN_DIRECTORY" || exit

# Prevent clear assets directory
svn update --set-depth infinity assets

# copy files from working directory
svn update --set-depth infinity trunk

echo "ℹ︎ Copying files from $INPUT_WORKING_DIRECTORY to trunk/"
rsync -rc "$INPUT_WORKING_DIRECTORY/" trunk/ --exclude "$INPUT_ASSETS_DIRECTORY" --delete --delete-excluded

# copy files from trunk to tag directory
svn update --set-depth infinity tags

echo "ℹ︎ Copying files from trunk/ to tags/$INPUT_PLUGIN_VERSION"

mkdir -p "tags/$INPUT_PLUGIN_VERSION/"
rsync -rc trunk/ "tags/$INPUT_PLUGIN_VERSION/" --delete --delete-excluded

# Handle assets
if [ -z "$INPUT_ASSETS_DIRECTORY" ]; then
    # copy files from assets directory
    echo "ℹ︎ Copying assets from $INPUT_ASSETS_DIRECTORY to assets/"
    rsync -rc "$INPUT_ASSETS_DIRECTORY/" assets/ --exclude "$INPUT_WORKING_DIRECTORY" --delete --delete-excluded

    echo "➤ Preparing asset files..."
    svn add --force "$SVN_DIRECTORY/assets/" > /dev/null

    # Fix asset mime type
    # https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/#issues
    if [[ -n $(find "assets" -maxdepth 1 -name "*.png" -print -quit) ]]; then
        svn propset svn:mime-type "image/png" "assets/*.png" || true
    fi
    if [[ -n $(find "assets" -maxdepth 1 -name "*.jpg" -print -quit) ]]; then
        svn propset svn:mime-type "image/jpeg" "assets/*.jpg" || true
    fi
    if [[ -n $(find "assets" -maxdepth 1 -name "*.gif" -print -quit) ]]; then
        svn propset svn:mime-type "image/gif" "assets/*.gif" || true
    fi
    if [[ -n $(find "assets" -maxdepth 1 -name "*.svg" -print -quit) ]]; then
        svn propset svn:mime-type "image/svg+xml" "assets/*.svg" || true
    fi
fi

echo "➤ Preparing files..."
svn add --force "$SVN_DIRECTORY/trunk/" > /dev/null
svn add --force "$SVN_DIRECTORY/tags/" > /dev/null

# remove missing files
# https://stackoverflow.com/a/43805181
svn status | awk '/^!/ {print $2}' | xargs -I {} svn del --force "{}"

# Fix directory out of date
# https://stackoverflow.com/a/3298401/5956589
svn update "$SVN_DIRECTORY/"

svn status

echo "➤ Committing files..."
svn commit -m "Release $INPUT_PLUGIN_VERSION" --no-auth-cache --non-interactive  --username "$INPUT_SVN_USERNAME" --password "$INPUT_SVN_PASSWORD"

echo "✓ Plugin deployed!"

echo "➤ Cleaning up working directory"
rm -rf "$SVN_DIRECTORY"

# Normalize
echo "➤ Switching to working directory"
cd "$GITHUB_WORKSPACE" || exit
