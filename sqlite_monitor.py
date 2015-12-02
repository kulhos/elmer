#!/usr/bin/python
import RPi.GPIO as GPIO
import time
from threading import Event, Thread
import json
import requests
import sys
import copy
import sqlite3
#import argparse
import ConfigParser
import os, tempfile, socket, pickle
import math
import multiprocessing

# defaults
GPIO_PIN = 3
GPIO_PULL = GPIO.PUD_UP
PRECISION = 30  #30 seconds

#parser = argparse.ArgumentParser(description="ElMer Monitor")
#parser.add_argument("-f","--file", type=open, help="config file")

#args = parser.parse_args()
#if args.file:
#    CONFIG_FILE = args.log

global counter
counter=0
global lasttime
lasttime = 0
global history
history = {}

headers = {'user-agent': 'smrz-elmer/0.0.1', 'Content-type': 'application/x-www-form-urlencoded'};
URL = "http://www.netfort.cz/smrz/s_elmer.php";
ADDRESS = os.path.join(tempfile.gettempdir(),'elmer_upload')

db_lock = multiprocessing.Lock()

def impulseCount(channel):
    global counter
    global lasttime
    global db
    now = time.time()
    time_window=math.floor(now/PRECISION)*PRECISION  # 30 secs precision
    if time_window <> lasttime:
        if lasttime <>0:
            with db_lock:
                cur = db.cursor()
                cur.execute("insert into DATA values (?,?,?)", [ lasttime, 1, counter ])
                db.commit()
            #db.insert(lasttime, "s0", counter)
            #db.commit()
        lasttime = time_window
        counter = 0

    counter+=1
    print time.ctime(now), ": ", counter
    #history[time] += 1
    #db.insert(time.time(), "s0", counter)
    #history[time.time()] = counter

GPIO.setmode(GPIO.BCM)     # set up BCM GPIO numbering
GPIO.setup(GPIO_PIN, GPIO.IN, pull_up_down=GPIO_PULL)    # set GPIO25 as input (button)

db = sqlite3.connect('/run/shm/elmer.db', check_same_thread = False)

with db_lock:
    cur = db.cursor()
    cur.execute("create table if not exists DATA (timestamp datetime, sensor text, value real)")
    db.commit()

cur = db.cursor()
cur.execute("select count(timestamp) from DATA")
print "Existing records: ",cur.fetchone()

cur = db.cursor()
cur.execute("insert into DATA values (?, ?, ?)", [ time.time(), 0, 1])
db.commit()

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
    print sys.exc_info()
    pass

finally:                   # this block will run no matter how the try block exits
    print "Finally"
    GPIO.cleanup()         # clean up after yourself
    pass


cur = db.cursor()
cur.execute("insert into DATA values (?, ?, ?)", [time.time(), 0, 0])
db.commit()
print "Closing database"
db.close()
sys.exit()



# vim: tabstop=4 expandtab shiftwidth=4 softtabstop=4
