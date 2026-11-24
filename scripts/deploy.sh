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
# Limit uploads to tracked, public theme files.
files=(style.css functions.php header.php footer.php index.php single-book.php single-chapter.php inc/publications.php)
printf '%s\n' "Deploy destination: $target"
printf '%s\n' "${files[@]}" | rsync -rni --files-from=- -e ssh ./ "$target"
read -r -p 'Deploy these changes? [y/N] ' answer
[[ $answer == y || $answer == Y ]] || exit 0
printf '%s\n' "${files[@]}" | rsync -ri --files-from=- -e ssh ./ "$target"
