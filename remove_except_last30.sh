#! /bin/bash
[ -z "$*" ] && exit
export NB=$(ls -1t $*|wc -l)
[ $NB -gt 30 ] || exit
export TR=$(expr $NB - 30)
rm -f $(ls -1tr $*|head -$TR)
