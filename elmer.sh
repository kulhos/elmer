#!/bin/sh
### BEGIN INIT INFO
# Provides:          elmer
# Required-Start:    $remote_fs $syslog $local_fs
# Required-Stop:     $remote_fs $syslog
# Default-Start:     2 3 4 5
# Default-Stop:      0 1 6
# Short-Description: Put a short description of the service here
# Description:       Put a long description of the service here
### END INIT INFO

# Change the next 3 lines to suit where you install your script and what you want to call it
DIR=/root/elmer
DAEMON=$DIR/sqlite_uploader.py
DAEMON_NAME=elmer

# Add any command line options for your daemon here
DAEMON_OPTS=""

# This next line determines what user the script runs as.
# Root generally not recommended but necessary if you are using the Raspberry Pi GPIO from Python.
DAEMON_USER=root

# The process ID of the script when it runs is stored here:
PIDFILE=/var/run/$DAEMON_NAME.pid

. /lib/lsb/init-functions

do_start () {
	rsync -a -u -v /var/elmer/elmer.db /run/shm/elmer.db
	log_daemon_msg "Starting elmer monitoring daemon"
	start-stop-daemon --start --background --pidfile /var/run/elmer_monitor.pid --make-pidfile --user $DAEMON_USER --chuid $DAEMON_USER --startas ${DIR}/sqlite_monitor.py
	log_end_msg $?
	log_daemon_msg "Starting elmer uploader daemon"
	start-stop-daemon --start --background --pidfile /var/run/elmer_uploader.pid --make-pidfile --user $DAEMON_USER --chuid $DAEMON_USER --startas ${DIR}/sqlite_uploader.py
	log_end_msg $?
}
do_stop () {
	log_daemon_msg "Stopping elmer uploader daemon"
	start-stop-daemon --stop --pidfile /var/run/elmer_uploader.pid --retry 10
	log_end_msg $?
	log_daemon_msg "Stopping elmer monitoring daemon"
	start-stop-daemon --stop --pidfile /var/run/elmer_monitor.pid --retry 10
	log_end_msg $?
	rsync -a -u -v /run/shm/elmer.db /var/elmer/elmer.db
}

case "$1" in

	start|stop)
		do_${1}
		;;

	restart|reload|force-reload)
		do_stop
		do_start
		;;

	status)
		status=0
		status_of_proc "elmer_uploader" "${DIR}/sqlite_uploader.py" || status=1
		status_of_proc "elmer_monitor" "${DIR}/sqlite_monitor.py" || status=1
		exit $status
		;;

	*)
		echo "Usage: /etc/init.d/$DAEMON_NAME {start|stop|restart|status}"
		exit 1
		;;

esac
exit 0
