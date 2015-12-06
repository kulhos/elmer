#!/usr/bin/python
import sys
import time
import json
import requests
import os
import tempfile
import sqlite3

HEADERS = {'user-agent': 'smrz-elmer/0.0.1', 'Content-type': 'application/x-www-form-urlencoded'};
URL = "http://www.netfort.cz/smrz/elmer/upload.php";
INTERVAL=30

def upload(rec):
    #print "upload"
    s = json.dumps(rec)
    print s
    #print hist_copy

    try:
        r = requests.post(URL, headers = HEADERS, data={'json':s}, timeout=10)
        #print (r.request.headers)
        #print (r.headers)
        print (r.text)
        print (r.status_code, r.reason)

        if (r.status_code == 200 and r.text == 'OK'):
            #ok, now delete old values from history
            print "upload success"
            return 1
        return 0

    except KeyboardInterrupt:
        raise

    except:
        print "exception: ", sys.exc_info()
        pass

    return 0


db = sqlite3.connect('/run/shm/elmer.db', check_same_thread = False)

with db:
    cur = db.cursor()
    cur.execute("create table if not exists DATA (timestamp datetime, sensor text, value real)")
    db.commit()

cur = db.cursor()
cur.execute("select count(timestamp) from DATA")
print "Existing records: ",cur.fetchone()

try:
    
    while True:
        
        try:
            c = db.cursor()
            c.execute("select timestamp, sensor, value from DATA order by timestamp asc limit 1")
            row = c.fetchone()
        except:
            print sys.exc_info()
            time.sleep(20)
            pass

        if row == None:
            time.sleep(20)
            continue

        tstamp,sensor,value = row
        #print row
        #rec = {'timestamp':row[0], 'event':row[1], 'value':row[2]}
        rec = {'timestamp':tstamp, 'sensor':sensor, 'value':value}

        x = upload(rec)
        if x == 1:
            print "removing"
            c = db.cursor()
            c.execute("delete from DATA where timestamp=? and sensor=?", (tstamp, sensor))
            db.commit()
        else:
            print "upload failed, continuing"
            time.sleep(20)
            continue

        time.sleep(1)
except:
    print sys.exc_info()
    print "Shutting down"
    pass

db.commit()
print "Closing database"
db.close()
sys.exit()

# vim: tabstop=4 expandtab shiftwidth=4 softtabstop=4
