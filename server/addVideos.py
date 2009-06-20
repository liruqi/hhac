#!/usr/bin/env python

import getopt
import os
import sys
import string

def __print_usage_info():
    print("Usage: %s [path]\n"%sys.argv[0])
    print("Options:")
    print("  -d: add all video files in a directory[but not recursive]")

def add_file(path):
    assert os.path.isfile(path), path + " not valid path"
    info = path.split('/')
    title = string.join(info, '-')
    tags = info[2]+'-'+info[3]
    description = title
    sql = "insert into videos(path, title, tags, description, owner) values('%s', '%s', '%s', '%s', %d)"%(path, title, tags, description, 1)
    cmd = "mysql -u hhac -piamharmless -D hhac -e \""+sql +"\";"
    print (cmd)
    os.system(cmd)

def add_dir(dir):
    assert os.path.isdir(dir), dir + " not valid directory"
    print ("add directory "+ dir)
    fd = os.popen("ls " + dir)
    files = fd.read().split()
    print (str(files))
    for f in files:
        add_file(dir + f)

if __name__ == "__main__":
    try:
        opts, args = getopt.getopt(sys.argv[1:], ":dh")
    except getopt.GetoptError, err:
        print(str(err))
        __print_usage_info()
        sys.exit(2)

    print (opts)
    print (args)
    directory = False

    for o,a in opts:
        if o == "-d":
            directory = True
        if o in ("-h","--help"):
            __print_usage_info()
            sys.exit()
    path = args[0]
    if directory :
        add_dir(path)
    else :
        add_file(path)
    
