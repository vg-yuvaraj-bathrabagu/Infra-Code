#!/bin/bash

mysql -h $1 --user=$2 --password=$3 $4 <<EOF
UPDATE EMRStatus set status = 'stopped' where id = $5;
EOF
echo 0;

