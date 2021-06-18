#!/usr/bin/python3

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

# Find model os_type
localdb_fetch = f"SELECT * FROM `models` WHERE `name`='{model}' LIMIT 1"
localdb_cursor.execute(localdb_fetch)
localdb_data = localdb_cursor.fetchall()
if not localdb_data: # If the model is in the DB
    print("""ERROR
    Model not in database.
    """)
    sys.exit()

for data in localdb_data:
    model_id = data[0]
    model_os_type = data[2]

if model_os_type != "IOS":
    print("""ERROR
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
    'use_keys': 'false',
    'allow_agent': ' false'
}

try:
    connect = ConnectHandler(**device) # Connect to device
except Exception as error: # If there's an error (device not reachable, wrong credentials)
    print(f"""ERROR
    CONNECTION FAILED: {error}
    """)
    sys.exit()

# # If device is logged into:
connect.enable() # Jump to enable mode
running_config = connect.send_command('show run') # Show running configuration

# We already have username and password from adding the device
# Hostname and Domain-name are needed so they will be extracted from the config
hostname = running_config
# Grab hostname
# Get everything after hostname:
hostname = hostname.split("hostname ", 1)
# Remove everything after the hostname line
hostname = hostname[1].split("\n")[0]

domain_name = running_config

# Different depending on device
if model == "Cisco 2901 Router":
    # Grabbing domain-name
    domain_name = domain_name.split("domain name ", 1)
    # Remove everything after the domain-name line
    domain_name = domain_name[1].split("\n")[0]
else:
    domain_name = domain_name.split("domain-name ", 1)
    domain_name = domain_name[1].split("\n")[0]



time = datetime.now()
localdb_insert = f"""INSERT INTO devices
(model, last_online, online, ip_address, username, password, use_global_conf, hostname, domain_name)
VALUES ('{model_id}', '{time}', '1', '{ip}', '{username}', '{password}', '0', '{hostname}', '{domain_name}')"""
localdb_cursor.execute(localdb_insert)
localdb.commit()

# Save the config to the configurations table
# Get the ID of the device just inserted

localdb_fetch = f"SELECT * FROM `devices` WHERE `ip_address`='{ip}' LIMIT 1"
localdb_cursor.execute(localdb_fetch)
localdb_data = localdb_cursor.fetchall()
for device in localdb_data:
    device_id = device[0]


localdb_insert = f"""INSERT INTO configurations
(device_id, time_saved, configuration)
VALUES ('{device_id}', '{time}', '{running_config}')"""
localdb_cursor.execute(localdb_insert)
localdb.commit()
