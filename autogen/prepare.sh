#!/bin/bash

tempfile="temp.txt"
source=$1
file="$(echo $source | head -c-5).xml"

if [ -z ${file+x} ] || ! [ -r "$1" ];then
	echo "File '$1' not found or so."
	return
else
	hbar
	version="$(grep -o "Factorio [[:digit:]]\..*\..[[:digit:]]" $source)"

	echo -e "\nDetected API Version: $version"
	echo -e "\nCreating \e[1;31m$file\e[0m ...\n"

	newdir="${version// /_}"
	if [ ! -d "$newdir" ]; then
  	mkdir $newdir
	fi

# just take a nap ...
	 cat $source > $file
	 tr -d '\r\n' < "$file" > $tempfile        		# trim to one line

	 sed -f sed_script.sed "$tempfile" > "$file"


	 echo -e "\n</file>" >> "$file"       		# insert xml close tag
	 cat $file > $tempfile
	 sum="$(grep -c 'element-name' "$tempfile")"	# count items

	 echo -e "Found \e[1;31m$sum\e[0m items."
	 hbar
	 echo -e "\n"
	 php index.php $file $sum $newdir
fi
echo
