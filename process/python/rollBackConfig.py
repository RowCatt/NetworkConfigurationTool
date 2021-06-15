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

time = datetime.datetime.now()

localdb = mysql.connector.connect( # Connect to the local database
    host="localhost",
    user="phproot",
    passwd="phproot",
    db="NetworkConfigurationTool",
    auth_plugin='mysql_native_password' # Needed for mysql authentication
)
localdb_cursor = localdb.cursor() # Used to interact with the DB

# Collect arguments (device details from PHP)
config_id = sys.argv[1]

# Get config from DB
localdb_fetch = f"SELECT * FROM configurations WHERE id='{config_id}' LIMIT 1"
localdb_cursor.execute(localdb_fetch)
localdb_data = localdb_cursor.fetchall()

for data in localdb_data:
    device_id = data[1]
    config_config = data[3]

# Get device info from DB
localdb_fetch = f"SELECT * FROM devices WHERE id='{device_id}' LIMIT 1"
localdb_cursor.execute(localdb_fetch)
localdb_data = localdb_cursor.fetchall()

for data in localdb_data:
    device_ip_address = data[4]
    device_username = data[5]
    device_password = data[6]

# Attempt connection using Netmiko
# Create device connection object
device = {
    'device_type':  'cisco_ios',
    'host':          device_ip_address,
    'username':      device_username,
    'password':      device_password,
    'port' :         22,
    'secret':        device_password,
}

# RE-ADD LATER. REMOVED FOR TESTING
# try:
#     connect = ConnectHandler(**device) # Connect to device
# except Exception as error: # If there's an error (device not reachable, wrong credentials)
#     print(f"""ERROR
#     CONNECTION FAILED: {error}
#     """)
#     sys.exit()


# Write rollback config to temp.txt
file = open("temp.txt", "w") 
file.write(config_config)
file.close()

# connect.enable() # Jump to enable mode
# Write rollback config
# output = connect.send_config_from_file("temp.txt")

# Set last online
localdb_update = f"UPDATE devices SET online='1', last_online='{time}' WHERE id='{device_id}'"
localdb_cursor.execute(localdb_update)
localdb.commit()