#!/bin/bash

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
PROJECT_ROOT="$( dirname "$SCRIPT_DIR" )"
cd "$PROJECT_ROOT" || exit 1

PLUGIN_SLUG="wpss-ultimate-user-management"
BUILD_FOLDER=".build"
BUILD_BASE="$BUILD_FOLDER/build_test"
BUILD_DIR="$BUILD_BASE/$PLUGIN_SLUG"
ZIP_FILE="$BUILD_FOLDER/$PLUGIN_SLUG.zip"

rm -rf "$BUILD_BASE"
rm -f "$ZIP_FILE"

mkdir -p "$BUILD_DIR"

echo "Copying production files..."

rsync -rc --exclude-from='.distignore' --exclude='.build' ./ "$BUILD_DIR/"

cd "$BUILD_BASE" || exit 1
zip -r "../$PLUGIN_SLUG.zip" "$PLUGIN_SLUG" > /dev/null
cd "$PROJECT_ROOT" || exit 1

echo "------------------------------------------------"
echo "Build completed successfully!"
echo "Test folder: ./.build/build_test/$PLUGIN_SLUG"
echo "ZIP:        ./.build/$PLUGIN_SLUG.zip"
echo "------------------------------------------------"
