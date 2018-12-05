#!/bin/bash

MYDIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

export PATH="$PATH:/usr/local/bin"

(

date

) > $MYDIR/../logs/every_5mins.log 2> $MYDIR/../logs/every_5mins.err
