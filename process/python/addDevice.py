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
    os_type = data[2]
    # print(os_type)

if os_type != "IOS":
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
print (f"""
=========================================
{running_config}
=========================================
""")

hostname = running_config.split("hostname ", 1)
# hostname[0] is before the hostname and hostname[1] is the hostname and everything after
# print(f"Hostname0: {hostname[0]}")
# print(f"Hostname1: {hostname[1]}")
# partition
# hostname = hostname[1].partition(" ")[0]
# print (f"Hostname is: {hostname} .")
# split new lines
hostname = hostname[1].split("\n")[0]
print(f"Hostname: {hostname} .")


print("END")