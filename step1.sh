#!/bin/bash

cd "$( dirname "$0" )"

export LD_LIBRARY_PATH="../server/usr/lib"

_ld_linux="../server/usr/lib/ld-linux.so.2"
_git="../server/usr/bin/git"

"$_ld_linux" "$_git" checkout master

