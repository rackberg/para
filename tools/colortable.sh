#!/usr/bin/env bash

for color in {0..255} ; do # Colors
    # Display the color
#    printf "\e[38;5;%sm  %3s  \e[0m" $color $color
    printf "\e[38;5;%sm  %3s  \e[0m" $color $color
    # Display 6 colors per lines
    if [ $((($color + 1) % 8)) == 0 ] ; then
        echo # New line
    fi
done
exit
