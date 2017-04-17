#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys
import re
import time
import datetime
import MySQLdb
import traceback
import commands
sys.path.append(os.getcwd())
sys.setrecursionlimit(1000000)

config_map = {
    'kake': dict(
        host = 'localhost',
        user = 'maiqi_kake_write',
        passwd = 'maiqi@KAKE2016',
        db = 'kake'
    ),
    'service': dict(
        host = 'localhost',
        user = 'maiqi_service_write',
        passwd = 'maiqi@SERVICE2016',
        db = 'service'
    )
}

level_map = dict(
    error = 1,
    warning = 2,
    info = 4,
    trace = 8
)

def debug(data, color = 34, exit = False):
    print '\n\033[1;%dm%s\033[0m\n' % (color, data)
    if exit:
        sys.exit(exit)

if 1 not in range(len(sys.argv)):
    debug('未给定 MySQL 配置索引(参数位1)', 31, 1)

config = config_map[sys.argv[1]]

if 2 not in range(len(sys.argv)):
    debug('未给定 yii2.0 日志文件所在目录(参数位2)', 31, 1)

log_path = sys.argv[2]

if not os.path.exists(log_path):
    debug('该目录不存在', 31, 1)

if 3 not in range(len(sys.argv)):
    debug('未给定日志文件的最大个数(参数位3)', 31, 1)

log_max_file = int(sys.argv[3])

filename = sys.argv[4] if 4 in range(len(sys.argv)) else 'debug.log'

reg_date = '\d{4}-\d{2}-\d{2}'

# 读取并删除一条日志
def read_line_and_delete(file, line_no = 1, content = ''):
    
    line_content = commands.getoutput('sed -n %dp %s' % (line_no, file))
    if line_content.strip() == '':
        line_numbers = int(commands.getoutput("wc -l %s | awk '{print $1}'" % file))
        if not line_numbers > 1:
            return content

    if content.strip() == '' or not re.match(reg_date, line_content):
        # OSX 系统需要在 -i 参数后加上 ''
        os.system("sed -i '%dd' %s" % (line_no, file))
        return read_line_and_delete(file, line_no, content + line_content)

    return content


try:
    mysql = MySQLdb.connect(**config)
except:
    debug(traceback.format_exc(), 31, 1)

link = mysql.cursor()

for i in range(0, log_max_file + 1):
    
    suffix = '' if i == 0 else '.' + str(i)
    file = os.path.join(log_path, filename + suffix)
    
    if os.path.exists(file):

        while True:

            log = read_line_and_delete(file)
            if log.strip() == '':
                break

            if not re.match(reg_date, log):
                continue

            log = log.split(' ')
            log_time_string = log[0] + ' ' + log[1]
            if '#' in log_time_string:
                format = log_time_string.split('#')
                log_time_string = format[0]
                ms = format[1]
            else:
                ms = 000

            log_time = str(int(time.mktime(time.strptime(log_time_string, "%Y-%m-%d %H:%M:%S"))))
            log_time += '.' + str(ms)

            prefix = log[2].split('][')
            level = level_map[prefix[3]]
            category = prefix[4].rstrip(']')

            prefix.remove(prefix[4])
            prefix.remove(prefix[3])

            prefix = ']['.join(prefix) + ']'

            log.remove(log[2])
            log.remove(log[1])
            log.remove(log[0])

            message = ' '.join(log).replace('    ', "\n")

            sql = "INSERT INTO `app_log` (`level`, `category`, `log_time`, `prefix`, `message`) VALUES (%d, '%s', %s, '%s', '%s')" % (level, category, log_time, prefix, MySQLdb.escape_string(message))
            debug(sql, 34)

            link.execute(sql)
            mysql.commit()

        # os.remove(file) if i > 0 else None

link.close()
mysql.close()
# -- eof --
