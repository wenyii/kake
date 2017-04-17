#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys
import datetime
import MySQLdb
import traceback
sys.path.append(os.getcwd())

def debug(data, color = 34, exit = False):
    print '\n\033[1;%dm%s\033[0m\n' % (color, data)
    if exit:
        sys.exit(exit)

if 1 not in range(len(sys.argv)):
    debug('未给定 MySQL 配置索引(参数位1)', 31, 1)

if 2 not in range(len(sys.argv)):
    debug('未给定服务器上传目录(参数位2)', 31, 1)

config = {
    'kake': dict(
        host = 'localhost',
        user = 'maiqi_kake_write',
        passwd = 'maiqi@KAKE2016',
        db = 'kake'
    )
}

days_ago = int(sys.argv[3]) if 3 in range(len(sys.argv)) else 7

try:
    mysql = MySQLdb.connect(**config[sys.argv[1]])
except:
    debug(traceback.format_exc(), 31, 1)

link = mysql.cursor()

date = str(datetime.datetime.now() - datetime.timedelta(days = days_ago))
sql = 'SELECT `deep_path`,`filename` FROM `attachment` WHERE `state` = 0 AND `update_time` < "%s"' % date
link.execute(sql)
result = link.fetchall()

for i in range(len(result)):
    file = sys.argv[2] + result[i][0] + '/' + result[i][1]
    os.remove(file) if os.path.exists(file) else None

sql = 'UPDATE `attachment` SET `state` = 2 WHERE `state` = 0 AND `update_time` < "%s"' % date
count = link.execute(sql)

debug(count, 35)

link.close()
mysql.commit()
mysql.close()

# -- eof --
