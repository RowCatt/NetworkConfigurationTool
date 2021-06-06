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
    db="NetworkConfigurationTool",
    auth_plugin='mysql_native_password' # Needed for mysql authentication
)
localdb_cursor = localdb.cursor() # Used to interact with the DB

# Collect arguments (device details from PHP)
ip = sys.argv[1]
username = sys.argv[2]
password = sys.argv[3]
model = sys.argv[4]

# print(f"""
# Entered Data:
# IP:       {ip}
# Username: {username}
# Password: {password}
# Model:    {model}
# """)

# Find model os_type
localdb_fetch = f"SELECT * FROM `models` WHERE `name`='{model}' LIMIT 1"
localdb_cursor.execute(localdb_fetch)
localdb_data = localdb_cursor.fetchall()
if not localdb_data: # If the model is in the DB
    print("""Error
    Model not in database.
    """)
    sys.exit()

for data in localdb_data:
    os_type = data[2]
    # print(os_type)

if os_type != "IOS":
    print("""Error
    Device Operating System not supported
    """)
    sys.exit()

# Attempt connection using Netmiko
# Create device connection object
device = {
    'device_type':  'cisco_ios',
    'host':          ip,
    'username':      username,
    'password':      password,
    'port' :         22,
    'secret':        password,
}
connect = ConnectHandler(**device) # Connect to device
# connect.enable() # Jump to enable mode