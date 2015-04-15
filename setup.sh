#!/bin/bash
# Delete existing dumps
# rm -rf 2.*/

# FIRMWARES=firmwares/*.bin
# for f in $FIRMWARES
# do
#   echo $f
#   ./tools/extract-firmware.sh $f
#   sudo chown $USER:$USER -R fmk
#   mv fmk $(basename "$f" ".bin")
# done

# ruby fix_links.rb

ROOT=`pwd`

commit () {
  VERSION=$1
  cp -a $ROOT/$VERSION/rootfs/* .
  git add .
  git commit -m "Version $VERSION"
  git tag "$VERSION"
}

rm -rf repo
mkdir repo
cd repo
git init

commit "2.04"
commit "2.05"
commit "2.17"