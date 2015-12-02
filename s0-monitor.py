#!/usr/bin/python
import RPi.GPIO as GPIO
import time
from threading import Event, Thread
import json
import requests
import sys
import copy
from pydblite import Base
#import argparse
import ConfigParser

# defaults
GPIO_PIN = 3
GPIO_PULL = GPIO.PUD_UP

db = Base('/run/shm/elmer.pdl')

#parser = argparse.ArgumentParser(description="ElMer Monitor")
#parser.add_argument("-f","--file", type=open, help="config file")

#args = parser.parse_args()
#if args.file:
#    CONFIG_FILE = args.log

GPIO.setmode(GPIO.BCM)     # set up BCM GPIO numbering
GPIO.setup(GPIO_PIN, GPIO.IN, pull_up_down=GPIO_PULL)    # set GPIO25 as input (button)

db.create('timestamp','event','value', mode="open")

print "Existing records:"
for r in db:
    print r

db.insert(time.time(), "elmer",1)
db.commit()

global counter
global timeStamp
counter=0
timeStamp = time.time()
global history
history = {}

headers = {'user-agent': 'smrz-elmer/0.0.1', 'Content-type': 'application/x-www-form-urlencoded'};
URL = "http://www.netfort.cz/smrz/s_elmer.php";

stopped = Event()

def impulseCount(channel):
    global counter
    counter+=1
    db.insert(time.time(), "s0", counter)
    db.commit()
    history[time.time()] = counter

GPIO.add_event_detect(GPIO_PIN, GPIO.FALLING, callback=impulseCount)
lastcnt = 0
try:
    while True:            # this will carry on until you hit CTRL+C
        time.sleep(5)
        nowcnt = counter
        if (lastcnt != nowcnt) :
            print(time.time(),nowcnt)
            #print history
            lastcnt = nowcnt
  
except:
    print "Exception"
    pass

finally:                   # this block will run no matter how the try block exits
    print "Finally"
    stopped.set()
    GPIO.cleanup()         # clean up after yourself
    pass


db.insert(time.time(), "elmer", 0)
print "Closing database"
db.commit()
sys.exit()



# vim: tabstop=4 expandtab shiftwidth=4 softtabstop=4
