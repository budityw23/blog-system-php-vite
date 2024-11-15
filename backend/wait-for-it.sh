#!/bin/bash
# wait-for-it.sh

set -e

host="$1"
shift
cmd="$@"

until php -r "
try {
    \$dbh = new PDO(
        'mysql:host=$host;dbname=blog',
        'bloguser',
        'blogpass'
    );
    exit(0);
} catch (PDOException \$e) {
    exit(1);
}
"; do
  >&2 echo "MySQL is unavailable - sleeping"
  sleep 1
done

>&2 echo "MySQL is up - executing command"
exec $cmd
