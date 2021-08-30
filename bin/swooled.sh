#!/bin/sh
#
# ServerU Inc - Rev 0.1 Feb 2020
#
# $FreeBSD$
#

# PROVIDE: proapps-im-api
# BEFORE: proapps
# KEYWORD: nojailvnet

. /etc/rc.subr

name="swooled"
rcvar=swooled_enable


start_cmd="do_start"
stop_cmd="do_stop"
status_cmd="do_status"
reload_cmd="do_reload"
extra_commands="reload"

do_start() {
	/usr/local/www/api/basic/runtime/swoole/script.php start
}

do_stop() {
	/usr/local/www/api/basic/runtime/swoole/script.php stop
}

do_reload() {
        /usr/local/www/api/basic/runtime/swoole/script.php reload
}

do_status() {
        /usr/local/www/api/basic/runtime/swoole/script.php status
}

do_restart() {
 do_stop
 do_start
}

load_rc_config $name
run_rc_command "$1"