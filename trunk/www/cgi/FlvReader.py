#! /usr/bin/env python
# -*- coding: utf-8 -*-

"""
Author: liruqi
Python Version: 2.5.2
"""

from struct import unpack
from struct import pack
from pprint import pprint
import datetime
import sys
import os
from types import *

def unpack_double(dat):
    return unpack('>d',dat)[0]
def unpack_int(dat):
    return unpack('>I',dat)[0]
def unpack_24bit(dat):
    a,b,c=unpack('3B',dat)
    return (a<<16)+(b<<8)+c
def unpack_short(dat):
    return unpack('>H',dat)[0]
def unpack_byte(dat):
    return unpack('B',dat)[0]

def pack_double(dat):
    return pack('>d',dat)
def pack_int(dat):
    return pack('>I',dat)
def pack_24bit(dat):
    return pack('BBB',dat>>16,(dat>>8)&(0xff),dat&(0xff))
def pack_short(dat):
    return pack('>H',dat)
def pack_byte(dat):
    return pack('B',dat)
    
# Tag types
FLV_AUDIO_TAG = 8
FLV_VIDEO_TAG = 9
FLV_SCRIPT_DATA_TAG = 18
FLV_TAG_HEADER_SZ = 11

def getAMFString(data):
    return pack_short(len(data)) + data
    
def getAMFDict( data):
    ret = pack_int(len(data))
    for name in data:
        ret += getAMFString(name)
        ret += getAMFData(data[name])
    return ret + getAMFString("") + chr(9)
    
def getAMFData( data):
    if type(data) is StringType :
        return chr(2) + getAMFString(data)
    if type(data) is FloatType :
        return chr(0) + pack_double(data)
    if type(data) is BooleanType :
        return chr(1) + chr(data)
    if type(data) is DictType :
        return pack_byte(8) + getAMFDict(data)
    assert False, str(type(data))

def getScriptDataTagHeader(datasize) :
    return pack_byte(FLV_SCRIPT_DATA_TAG) +  pack_24bit(datasize) + pack_24bit(0) + pack_byte(0) + pack_24bit(0)
    
class FlvTag:
    Data = None
    def __init__(self, dat):
        headElements = unpack('>B3B3BB3B', dat)
        self.TagType = headElements[0]
        self.DataSize = ((headElements[1]<<16) + (headElements[2]<<8) + (headElements[3]))
        self.Timestamp = ((headElements[4]<<16) + (headElements[5]<<8) + (headElements[6]))
        self.TimestampExtended = headElements[7]
        self.StreamId = ((headElements[8]<<16) + (headElements[9]<<8) + (headElements[10]))
        self.Header = dat
        t = self.TagType
        assert (t==FLV_AUDIO_TAG or t==FLV_VIDEO_TAG or t==FLV_SCRIPT_DATA_TAG) , "undefined tag"
    
    def __BuildHeader(self):
        self.Header = pack_byte(self.TagType) +  pack_24bit(self.DataSize) + pack_24bit(self.Timestamp) + pack_byte(self.TimestampExtended) + pack_24bit(self.StreamId)
        
    def SetData(self, dat):
        self.DataSize = len(dat)
        self.__BuildHeader()
        self.Data = dat
    
    def SetEqualSizeData(self, dat):
        assert len(dat)==self.DataSize , "data length not match"
        self.Data = dat
        
    def GetRawData(self):
        return self.Header+self.Data

    def GetSize(self):
        return self.DataSize + FLV_TAG_HEADER_SZ

    def GetTimestamp(self):
        return self.Timestamp + (self.TimestampExtended << 24)
        
    def SetTimestamp(self, value):
        self.TimestampExtended = value >> 24
        self.Timestamp = value - (self.TimestampExtended << 24)
        self.__BuildHeader()
        return self.Timestamp + (self.TimestampExtended << 24)
        
    def IsSequenceHead(self):
        if self.TagType != FLV_VIDEO_TAG:
            return False
        DataType,SubType = unpack('BB',self.Data[:2])
        FrameType = (DataType>>4)
        CodecID = (DataType & 0xf)
        if FrameType==1 and CodecID==7 and SubType==0:
            return True
        return False
    
    def IsAudioTag(self):
        return self.TagType == FLV_AUDIO_TAG

    def IsVideoTag(self):
        return self.TagType == FLV_VIDEO_TAG

    def IsScriptDataTag(self):
        return self.TagType == FLV_SCRIPT_DATA_TAG
    
    def GetCodecID(self):
        DataType = ord(self.Data[0])
        return (DataType & 0xf)

    def GetFrameType(self):
        DataType = ord(self.Data[0])
        return (DataType >> 4)

class FlvReader ():
    """
    GetTag： 返回Tag
    """

    Buffer = ''
    CurrentTag = None

    def __init__(self, ifilename):
        """
        if input is a file, DO Create this FlvReader with filename parameter
        if input is a stream, no parameter is needed
        
        FLV file head format:
        { Signature 3B
          Version 1B
          TypeFlags 1B
          Offset 4B 
        }
        Following 4 bytes:
        { PreviousTagSize 4B 
        }
        B: byte
        """
        
        if not ifilename:
            print ("No input file")
            return
        self.ifile = open(ifilename, 'rb')
        self.FLVHeader = self.ifile.read(9+4)
        Signature = self.FLVHeader[:3]
        assert Signature == 'FLV', 'Not an flv file'
        
    def GetTag(self):
        header = self.ifile.read(FLV_TAG_HEADER_SZ)
        if len(header) < FLV_TAG_HEADER_SZ:
            return None
        self.CurrentTag = FlvTag( header )
        data = self.ifile.read(self.CurrentTag.DataSize)
        if len(data) < self.CurrentTag.DataSize:
            return None
        self.CurrentTag.SetData( data )
        tagLen = unpack_int( self.ifile.read(4) )
        #print (tagLen, self.CurrentTag.DataSize)
        assert tagLen == self.CurrentTag.DataSize + FLV_TAG_HEADER_SZ, "check length error"
        return self.CurrentTag

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print ('Usage: %s input_file [output_file] ' % sys.argv[0] )
        print ('This program is used to show flv file video information')
        print ('A new flv file with correct duration is generated if output_file is provided\n')
        
        sys.exit()

    flvreader = FlvReader(sys.argv[1])
    if len(sys.argv) > 2:
        ofile = open(sys.argv[2], 'wb')
    else :
        ofile = None
    if ofile:
        ofile.write(flvreader.FLVHeader)
    #print (flvreader.FLVHeader)
    sdtag = flvreader.GetTag()
    assert sdtag.IsScriptDataTag() , "first tag not script data"
    #print ( "script data len: %d"%len(tag.Data) )
    
    data = {}
    data['duration'] = 600.0
    
    script_data = getAMFData('onMetaData') + getAMFData(data)
    sdtag.SetData(script_data)
    
    tc = 0
    audioc = 0
    videoc = 0
    sdatac = 0
    codecc = [0,0,0,0,0,0,0,0]
    frametypec = [0,0,0,0,0,0]
    timestamplog = open('timestamp.log','w')
    lasttimestamp = 0
    
    tag = FlvTag(sdtag.Header)
    tag.SetData(script_data)
    while True:
        tc += 1 
        tagType = 'Unknown'
        if(tag.IsScriptDataTag()):
            sdatac+=1
            tagType = 'ScriptData'
        if(tag.IsAudioTag()):
            audioc+=1
            tagType = 'Audio'
        if(tag.IsVideoTag()):
            videoc+=1
            tagType = 'Video'
            codecc[ tag.GetCodecID() ] += 1
            frametypec[ tag.GetFrameType() ] += 1

        if ofile:
            ofile.write( tag.GetRawData() )
            ofile.write( pack_int( tag.GetSize() ) )
        timestamplog.write ("%d: %d %s\n"%(tc,tag.GetTimestamp(), tagType) )
        lasttimestamp = tag.GetTimestamp()
        tag=flvreader.GetTag()
        if not tag:
            break
    
    timestamplog.close()
    
    print ("%d scriptdata tag\n%d audio tags\n%d video tags\n%d tags in all\n"%(sdatac, audioc, videoc, tc) )
    print ("codec list: " + str(codecc))
    print ("frametype list: " + str(frametypec))
    if ofile:
        data['duration'] = lasttimestamp / 1000.0
        print data['duration']
        ofile.seek( 9+4 )
        script_data = getAMFData('onMetaData') + getAMFData(data)
        sdtag.SetEqualSizeData(script_data)
        
        ofile.write(sdtag.GetRawData() )
        ofile.close()

