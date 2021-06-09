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

# This script runs periodically to peform many functions
# Use Netmiko to attempt connections to devices in the DB

localdb_fetch = f"SELECT * FROM `devices`"
localdb_cursor.execute(localdb_fetch)
localdb_data = localdb_cursor.fetchall()

for device in localdb_data:
    device_id = device[0]
    device_ip_address = device[4]
    device_username = device[5]
    device_password = device[6]
    device_use_global_conf = device[7]

    # Connect to deivce
    netmiko_connect = {
        'device_type':  'cisco_ios',
        'host':          device_ip_address,
        'username':      device_username,
        'password':      device_password,
        'port' :         22,
        'secret':        device_password,
    }

    print(f"Attempting connection to {device_ip_address}")

    # try:
    #     connect = ConnectHandler(**device) # Connect to device
    #     print("Connection successful")
    # except Exception as error:
    #     # Connection failed, set the device to offline
    #     print("Connection failed")
    #     localdb_update = f"UPDATE devices SET online='0' WHERE id='{device_id}'"
    #     localdb_cursor.execute(localdb_update)
    #     localdb.commit()
    #     continue

    # Set oneline=1 and last_online=now
    print("Setting online and last_online")
    time = datetime.now()
    localdb_update = f"UPDATE devices SET online='1', last_online='{time}' WHERE id='{device_id}'"
    localdb_cursor.execute(localdb_update)
    localdb.commit()
    print(f"Device {device_ip_address} done")

print("End")