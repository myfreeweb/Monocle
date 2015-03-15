#!/bin/bash

mysqldump -u root --no-data --compact --skip-set-charset --default-character-set=utf8mb4 monocle > schema.sql
