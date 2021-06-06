#!/usr/bin/python3

# import nmap3
import json
import sys
import ipaddress
import socket
import mysql.connector
import datetime
from netmiko import ConnectHandler
import re

datetime = datetime.datetime.now()

localdb = mysql.connector.connect( # Connect to the local database
    host="localhost",
    user="phproot",
    passwd="phproot",
    db="NetworkConfigurationTool"
)
localdb_cursor = localdb.cursor() # Used to interact with the DB

# Collect arguments (device details from PHP)
ip = sys.argv[1]
username = sys.argv[2]
password = sys.argv[3]
model = sys.argv[4]

print(f"""
Entered Data:
IP:       {ip}
Username: {username}
Password: {password}
Model:    {model}
""")