#!/usr/bin/python
# -*- coding: utf-8 -*-
"""
=begin CGI的环境变量举例
"HTTP_HOST"=>"127.0.0.1"
"HTTP_USER_AGENT"=>"Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10"
"HTTP_ACCEPT"=>"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
"HTTP_ACCEPT_LANGUAGE"=>"zh-cn,zh;q=0.5"
"HTTP_ACCEPT_ENCODING"=>"gzip,deflate"
"HTTP_ACCEPT_CHARSET"=>"gb2312,utf-8;q=0.7,*;q=0.7"
"HTTP_KEEP_ALIVE"=>"300"
"HTTP_CONNECTION"=>"keep-alive"
"HTTP_CACHE_CONTROL"=>"max-age=0"
"SERVER_SIGNATURE"=>""
"SERVER_SOFTWARE"=>"Apache/2.2.4 (Win32) PHP/5.2.5"
"SERVER_NAME"=>"127.0.0.1"
"SERVER_ADDR"=>"127.0.0.1"
"SERVER_PORT"=>"80"
"REMOTE_ADDR"=>"127.0.0.1"
"DOCUMENT_ROOT"=>"E:/programme/apache/htdocs"
"SERVER_ADMIN"=>"physacco@gmail.com"
"SCRIPT_FILENAME"=>"C:/program1/apache/cgi-bin/test.rb"
"REMOTE_PORT"=>"1952"
"GATEWAY_INTERFACE"=>"CGI/1.1"
"SERVER_PROTOCOL"=>"HTTP/1.1"
"REQUEST_METHOD"=>"GET"
"QUERY_STRING"=>""
"REQUEST_URI"=>"/cgi-bin/test.rb"
"SCRIPT_NAME"=>"/cgi-bin/test.rb"
=end
"""


import MySQLdb
import sys
import cgi
import cgitb
import os
cgitb.enable()
cgitb.enable(display=0, logdir="/tmp")


# 将相对路径为rel_path的文件发给客户端
document_root = "/data/videos/"
def send_file(rel_path):
    data = open(document_root + rel_path, "rb")
    print (data.read())


# 在数据库中根据id查询FLV文件的相对路径
def lookup_path(id):
    db = MySQLdb.connect('localhost', 'hhac', 'iamharmless', 'hhac')
    reader = db.cursor()
    num = reader.execute("SELECT path FROM videos WHERE id = " + str(id) + ";")
    if num == 1:
        path = reader.fetchone()
        return path[0]
    return None

try :
#if __name__ == '__main__'
    form = cgi.FieldStorage()
    id = form.getvalue("id")
    #print (os.environ)
    if id:
        try :
            rel_path = lookup_path(id)
            print "Content-Type: application/octet-stream\n";
            send_file(rel_path)
        except :
            print ("Content-Type: text/html\n\n" + rel_path);
            print ("<html><head><title>Error0</title></head><body><h1>403 Forbidden</h1></body></html>")
    else :
        # 处理id无效的情况
        print ("Content-Type: text/html\n\n");
        print "HTTP/1.0 404 Not Found"
        print "Server: physacco@gmail.com\n"  # 无效
        print "Content-Type: text/html; charset=utf-8\n";
        print "Connection: Close\n"
        print "<html><head><title>Error1</title></head><body><h1>404 Not Found</h1></body></html>"

except:
    print ("Content-Type: text/html\n\n" + rel_path);
    print ("<html><head><title>Error2</title></head><body>404 Not Found<br/>")
    for k in os.environ:
        print (k, os.environ[k])
        print ("<br/>")
    print (id)
    #print (session)
    print ( "</body></html>")

