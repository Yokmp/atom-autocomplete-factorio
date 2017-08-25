<<<<<<< HEAD
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
=======
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
>>>>>>> bf5c4e09d63d9a880a9d0119aa3923ae9849f02b
