#!/usr/bin/env bash
set -euo pipefail

host=${1:?Expected an SSH destination}
themes_directory=${2:?Expected the remote themes directory}
if [[ ! $host =~ ^[[:alnum:]_][[:alnum:]_.@-]*$ || ! $themes_directory =~ ^/[[:alnum:]_./-]+$ || /$themes_directory/ == */../* ]]; then
    echo 'Use an SSH host or alias and an absolute remote path without spaces or parent directory segments.' >&2
    exit 2
fi

cd "$(git rev-parse --show-toplevel)"
target="$host:${themes_directory%/}/author-theme/"
# Deploy only tracked WordPress theme files and assets.
theme_files() {
    git ls-files -z -- '*.php' '*.css' assets/ languages/ theme.json screenshot.png
}
if [[ ${3:-} != --non-interactive ]]; then
    theme_files | rsync -rcni --from0 --files-from=- ./ "$target"
    read -r -p 'Deploy these changes? [y/N] ' answer
    [[ $answer == y || $answer == Y ]] || exit 0
fi
theme_files | rsync -rci --delay-updates --from0 --files-from=- ./ "$target"
