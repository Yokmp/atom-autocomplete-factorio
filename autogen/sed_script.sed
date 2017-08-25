# remove top
s#^.*brief-members">##
# remove bottom
s#</table.*html># #
# add newline for each tag
s#<#\n<#g
# insert xml open tag
1i <?xml version="1.0" encoding="UTF-8" standalone="yes"?>\n<file>
# remove empty lines
/^\s*$/d
# remove whitespace
s/^[ \t]*//
# remove arrow icon
s/&rarr;//g
# remove dots
s/&hellip;//g
