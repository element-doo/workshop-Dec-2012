@ECHO OFF

cd ../server/git/bin
git.exe --git-dir=../../../web/.git --work-tree=../../../web/ checkout master
