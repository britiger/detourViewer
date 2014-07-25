#!/bin/bash

# Overpass QL for all detour relations:
# relation[route=detour];
# out;
wget http://overpass-api.de/api/interpreter?data=relation%5Broute%3Ddetour%5D%3B%0Aout%3B -O detour-all.xml

# Overpass QL Germany only:
# relation["route"="detour"](area:3600051477);
# out;
wget http://overpass-api.de/api/interpreter?data=relation%5B%22route%22%3D%22detour%22%5D\(area%3A3600051477\)%3B%0Aout%3B -O detour.xml
