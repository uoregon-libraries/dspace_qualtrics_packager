#!/bin/bash

cd $1
for NAME in *
do
  if [ -d $NAME ]
  then
    zip -r "$NAME.zip" "$NAME"
  fi
done 
