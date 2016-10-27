#!/bin/bash

# sort the bad-actors ip file and drop duplicates
# citation: https://www.madboa.com/geek/sort-addr/

sort -V bad_actors_ips.txt | uniq > bad_actors_ips.txt

