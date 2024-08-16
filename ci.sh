#!/bin/sh
USERNAME="example";

useradd -m -s /bin/bash "$USERNAME";
echo "$USERNAME:password" | chpasswd;
usermod -aG sudo "$USERNAME";
chown "$USERNAME":"$USERNAME" -R .;

./install.sh