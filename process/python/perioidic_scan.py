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

# This script runs periodically to peform many functions
# Use Netmiko to attempt connections to devices in the DB

# Get global config first - optimisation to stop querying on every for loop
localdb_fetch = f"SELECT * FROM `global_configuration`"
localdb_cursor.execute(localdb_fetch)
localdb_data = localdb_cursor.fetchall()
for global_conf in localdb_data:
    global_username = global_conf[1]
    global_password = global_conf[2]
    global_domain_name = global_conf[3]

localdb_fetch = f"SELECT * FROM `devices`"
localdb_cursor.execute(localdb_fetch)
localdb_data = localdb_cursor.fetchall()

for device in localdb_data:
    device_id = device[0]
    device_ip_address = device[4]
    device_username = device[5]
    device_password = device[6]
    device_use_global_conf = device[7]
    device_hostname = device[8]
    device_domain_name = device[9]

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

    # ADD BACK LATER
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
    localdb_update = f"UPDATE devices SET online='1', last_online='{time}' WHERE id='{device_id}'"
    localdb_cursor.execute(localdb_update)
    localdb.commit()

    # Checking if device has global config enabled
    if device_use_global_conf == 1:
        # It does - check if the current config is different
        # connect.enable()
        # running_config = connect.send_command('show run')

        # Placeholder running conf
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

        print("Finding current username")
        current_username = running_config
        current_username = current_username.split("username ", 1)
        current_username = current_username[1].split("password")[0]
        print(current_username)

        # print("Finding current password")
        # current_password = running_config
        # current_password = current_password.split("password ", 1)
        # current_password = current_password[1].split("\n")[0]
        # print(current_password)

        print("Finding current domain-name")
        current_domain_name = running_config
        current_domain_name = current_domain_name.split("domain-name ", 1)
        current_domain_name = current_domain_name[1].split("\n")[0]
        print(current_domain_name)

        # Compare and if there's a difference, backup the old running config
        if current_username != global_username or current_domain_name != global_domain_name:
            # Backup current config
            localdb_insert = f"INSERT INTO configurations (device_id, time_saved, configuration) VALUES ('{device_id}', '{time}', '{running_config}')"
            localdb_cursor.execute(localdb_insert)
            localdb.commit()

        # If no difference...
        # Write the global config
        # Remove current username and insert new user+pass
        # Remove and add domain-name
        config_commands = [ f'no username {current_username}',
                            f'username {global_username} password {global_password}',
                            'no enable password',
                            f'enable password {global_password}',
                            'no ip domain-name',
                            f'ip domain-name {global_domain_name}',
                            'wr'] # check if this needs to be 'do wr' to write run mem to start
        # applied_config = connect.send_config_set(config_commands)     # Apply config to device
    else:
        # Check if running config is different to device table config, if so, change

        # UNCOMMENT AFTER TESTING
        # connect.enable()
        # running_config = connect.send_command('show run')

        # Placeholder running conf
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

        # Gather data from running-conf
        print("Finding current username")
        current_username = running_config
        current_username = current_username.split("username ", 1)
        current_username = current_username[1].split("password")[0]
        print(current_username)

        print("Finding current domain-name")
        current_domain_name = running_config
        current_domain_name = current_domain_name.split("domain-name ", 1)
        current_domain_name = current_domain_name[1].split("\n")[0]
        print(current_domain_name)

        print("Finding current hostname")
        current_hostname = running_config
        current_hostname = current_hostname.split("hostname ", 1)
        current_hostname = current_hostname[1].split("\n")[0]
        print(current_hostname)

        # Compare and if there's a difference, backup the old running config
        if current_username != device_username or current_domain_name != device_domain_name or current_hostname != device_hostname:
            # Backup current config
            localdb_insert = f"INSERT INTO configurations (device_id, time_saved, configuration) VALUES ('{device_id}', '{time}', '{running_config}')"
            localdb_cursor.execute(localdb_insert)
            localdb.commit()

        # If no difference...
        # Remove current config
        # Write the new config
        config_commands = [ f'no username {current_username}',
                            f'username {device_username} password {device_password}',
                            'no enable password',
                            f'enable password {device_password}',
                            'no ip domain-name',
                            f'ip domain-name {device_domain_name}',
                            'no hostname',
                            f'hostname {device_hostname}',
                            'wr'] # CHECK THIS TOO AS IN THE GLOBAL_CONF
        # applied_config = connect.send_config_set(config_commands)     # Apply config to device


    print(f"Device {device_ip_address} done")



print("End")