#! /usr/bin/env python

import getopt
import os
import sys
import string
from config import *

def __print_usage_info():
    print("Usage: %s [path relative to videos_root]\n"%sys.argv[0])

def addfile(path):
    path = args[0]
    if path.find(videos_root) == 0:
        path = path[ len(videos_root) : ]
    info = path.split('/')
    title = string.join(info, ',')
    tags = info[0]
    description = title
    sql = "insert into videos(path, title, tags, description, owner) values('%s', '%s', '%s', '%s', %d)"%(path, title, tags, description, 1)
    cmd = "mysql -u hhac -piamharmless -D hhac -e \""+sql +"\";"
    print (cmd)
    os.system(cmd)

if __name__ == "__main__":
    try:
        opts, args = getopt.getopt(sys.argv[1:], "::")
    except getopt.GetoptError, err:
        __print_usage_info()
        sys.exit()

    print (opts)
    print (args)
    addfile(args[0])
    
    
