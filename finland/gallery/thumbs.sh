#!/bin/sh

for i in *.jpg; do
	convert -size x80 $i -resize x80 +profile "*" ../thumbs/$i
done

for i in *.JPG; do
	convert -size x80 $i -resize x80 +profile "*" ../thumbs/$i
done
