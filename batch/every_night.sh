#!/bin/bash

MYDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

export PATH="$PATH:/usr/local/bin"

(

#php $MYDIR/doSomething.php

date

) > $MYDIR/../logs/every_night.log 2> $MYDIR/../logs/every_night.err
