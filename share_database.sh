#!/usr/bin/env bash
mkdir -p ~/Code/2dam1617/sql
mysqldump -p scool > ~/Code/2dam1617/sql/scool.sql
NOW=$(date +"%F")
NOWT=$(date +"%T")
cp ~/Code/2dam1617/sql/scool.sql ~/Code/2dam1617/sql/scool_${NOW}_${NOWT}.sql