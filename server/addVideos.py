#!/usr/bin/env python
import MySQLdb
import getopt
import os
import sys
import string

writer = None

def __print_usage_info():
    print("Usage: %s [path]\n" % sys.argv[0])
    print("Options:")
    print("  -d: add all video files in a directory[but not recursive]")

def add_file(path):
    assert os.path.isfile(path), path + " not valid path"
    if path[0]!='/': #for windows
        path = path.replace('\\', '/')
        
    info = path.split('/')[1:]
    filename = info[-1]
    title = filename.split(".")[0]
    tags = title
    description = title
    sql = "insert into videos(path, title, tags, description, owner) values('%s', '%s', '%s', '%s', %d)"%(path, title, tags, description, 1)
    #cmd = "mysql -u hhac -piamharmless -D hhac -e \""+sql +"\";"
    #print (cmd)
    #os.system(cmd)
    writer.execute(sql)

def add_dir(dir):
    assert os.path.isdir(dir), dir + " not valid directory"
    if dir[-1] != '/':
        dir = dir + '/'
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

    print ("Options: %s" % opts)
    print ("Arguments: %s" % args)

    if len(args) < 1:
        __print_usage_info()
        sys.exit(1)

    directory = False
    db = MySQLdb.connect('localhost', 'hhac', 'iamharmless', 'hhac')
    writer = db.cursor()
    
    for o,a in opts:
        if o == "-d":
            directory = True
        if o in ("-h","--help"):
            __print_usage_info()
            sys.exit(1)

    if directory:
        for folder in args:
            add_dir(folder)
    else:
        for folder in args:
            add_file(args[0])
