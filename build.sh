#!/bin/bash

mkdir ~/plugins/

ENV_PREFIX=$1 zip -r ~/plugins/wordpress-plugin-$1.zip .  -x *.git*
