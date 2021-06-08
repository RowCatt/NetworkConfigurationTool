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
    print("""ERROR
    Model not in database.
    """)
    sys.exit()

for data in localdb_data:
    model_id = data[0]
    model_os_type = data[2]
    # print(model_os_type)

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
}

# RE-ADD LATER. REMOVED FOR TESTING
# try:
#     connect = ConnectHandler(**device) # Connect to device
# except Exception as error: # If there's an error (device not reachable, wrong credentials)
#     print(f"""ERROR
#     CONNECTION FAILED: {error}
#     """)
#     sys.exit()

# # If device is logged into:
# connect.enable() # Jump to enable mode
# running_config = connect.send_command('show run') # Show running configuration

# This should output something like:
#=====================================================
running_config = """
Building configuration...

Current configuration : 833 bytes
!
version 15.1
no service timestamps log datetime msec
no service timestamps debug datetime msec
no service password-encryption
!
hostname R1
!
enable password adminpassword
!
ip cef
no ipv6 cef
!
username admin password 0 adminpassword
!
license udi pid CISCO2901/K9 sn FTX1524ZQ02-
!
ip domain-name lab.local
!
spanning-tree mode pvst
!
interface GigabitEthernet0/0
 ip address 10.0.3.1 255.255.255.0
 duplex auto
 speed auto
!
interface GigabitEthernet0/1
 ip address 10.0.2.1 255.255.255.0
 duplex auto
 speed auto
!
interface Vlan1
 no ip address
 shutdown
!
router eigrp 1
 network 10.0.3.0 0.0.0.255
 network 10.0.2.0 0.0.0.255
!
ip classless
!
ip flow-export version 9
!
line con 0
!
line aux 0
!
line vty 0 4
 login local
line vty 5 15
 login local
!
end 
"""
#=====================================================

# We already have username and password from adding the device
# Hostname and Domain-name are needed so they will be extracted from the config
# print (f"""
# =========================================
# {running_config}
# =========================================
# """)
hostname = running_config
# Grab hostname
# Get everything after hostname:
hostname = hostname.split("hostname ", 1)
# Remove everything after the hostname line
hostname = hostname[1].split("\n")[0]
# print(f"Hostname: {hostname} .")

domain_name = running_config
# Grabbing domain-name
domain_name = domain_name.split("domain-name ", 1)
# Remove everything after the domain-name line
domain_name = domain_name[1].split("\n")[0]
# print(f"Domain Name: {domain_name} .")

# Insert device into the database using the info collected
# localdb_insert = f"""INSERT INTO devices 
#         (ip_address, mac_address, vendor, os, os_type, os_family, os_gen, ssh, http, hostname, online, last_pinged) 
#         VALUES ('{ipAddress}', '{macAddress}', '{vendor}', '{os}', '{os_type}', '{os_family}', '{os_gen}', '{ssh}', '{http}', '{hostname}', 'Yes', '{datetime.datetime.now()}')"""
#         localdb_cursor.execute(localdb_insert)
#         localdb.commit()
time = datetime.now()
localdb_insert = f"""INSERT INTO devices
(model, last_online, online, ip_address, username, password, use_global_conf, hostname, domain_name)
VALUES ('{model_id}', '{time}', '1', '{ip}', '{username}', '{password}', '0', '{hostname}', '{domain_name}')"""
localdb_cursor.execute(localdb_insert)
localdb.commit()
print("Device entered into database")

# Save the config to the configurations table
# Get the ID of the device just inserted

localdb_fetch = f"SELECT * FROM `devices` WHERE `ip_address`='{ip}' AND `last_online`='{time}' LIMIT 1"
localdb_cursor.execute(localdb_fetch)
localdb_data = localdb_cursor.fetchall()
for device in localdb_data:
    device_id = device[0]
    print(f"dev is: {device}")

print(f"Device ID is: {device_id}")
