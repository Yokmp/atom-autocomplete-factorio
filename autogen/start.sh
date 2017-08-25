#!/bin/bash

#********* START configuration
pathtodocs="/cygdrive/f/Steam/SteamApps/common/Factorio/doc-html"
#*********** END configuration

function hbar()
{
	[ $1 ] && x=$1 || x=' '
	for ((i=0; i<$COLUMNS; i++))
	{
		echo -en "\e[4m\e[94m$x\e[0m"
	}
}

if [ "$1" == 'latest' ] && [ $(which wget 2> /dev/null) ]; then
  hbar _
  echo "Using latest source. Downloading ..."
  rm -rf latest/
  mkdir latest
  wget -r -nv --no-parent -P latest/ http://lua-api.factorio.com/latest/
  pathtodocs='latest\lua-api.factorio.com\latest'
fi

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

clear
hbar _
echo -ne "Creating Directory \e[1;31mtemp\e[0m ... "
mkdir -p temp
rm -f ../lib/api.json
echo -e "done!\n"

echo "[" > ../lib/api.json
ls $pathtodocs | grep "^Lua.*html" | while read -r line; do
  hbar
  echo -e "Current File in process:\n\e[1;31m$pathtodocs/$line\e[0m"
  cp -f "$pathtodocs/$line" "$DIR/temp/"
  source prepare.sh "temp/$line"
done
cat ../lib/api.json | sed -e '$s/,$/]/'  > ../lib/api2.json
mv  ../lib/api2.json  ../lib/api.json
#echo -e "\n]" >> ../lib/api.json

#newdir='Factorio_0.13.16'

echo "removing temporary files and folders ... "
rm -rf temp/ latest/
rm -f temp.txt

find . -name '.cson' -delete # last $line is empty ...
echo $newdir
unset newdir version
echo -ne "done!\n"
hbar _
# move all into one
# echo *.txt | xargs cat > all.txt