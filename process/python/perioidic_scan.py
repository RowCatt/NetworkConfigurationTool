#!/usr/bin/python3

# import nmap3
import json
from os import name
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

    # Change details for global conf
    # Connect to deivce
    netmiko_connect = {
        'device_type':  'cisco_ios',
        'host':          device_ip_address,
        'username':      device_username,
        'password':      device_password,
        'port' :         22,
        'secret':        device_password,
        'use_keys': 'false',
        'allow_agent': ' false'
    }

    print(f"Attempting connection to {device_ip_address}")

    # ADD BACK LATER
    try:
        connect = ConnectHandler(**netmiko_connect) # Connect to device
        connect.enable()
        print("Connection successful")
    except:
        try:
            localdb_fetch = f"SELECT * FROM `global_configuration` LIMIT 1"
            localdb_cursor.execute(localdb_fetch)
            localdb_data = localdb_cursor.fetchall()

            for global_conf in localdb_data:
                global_username = global_conf[1]
                global_password = global_conf[2]
                print(f"Global username: {global_username}")
                print(f"Global password: {global_password}")

            netmiko_connect = {
                'device_type':  'cisco_ios',
                'host':          device_ip_address,
                'username':      global_username,
                'password':      global_password,
                'port' :         22,
                'secret':        global_password,
                'use_keys': 'false',
                'allow_agent': ' false'
            }
            connect = ConnectHandler(**netmiko_connect)
            connect.enable()
            print("Connection successful")
            # except Exception as error:
        except:
            print("Connection failed")
            localdb_update = f"UPDATE devices SET online='0' WHERE id='{device_id}'"
            localdb_cursor.execute(localdb_update)
            localdb.commit()
            continue

    # connect.disconnect()
    # connect = ConnectHandler(**netmiko_connect)

    # Set oneline=1 and last_online=now
    print("Setting online and last_online")
    localdb_update = f"UPDATE devices SET online='1', last_online='{time}' WHERE id='{device_id}'"
    localdb_cursor.execute(localdb_update)
    localdb.commit()

    # Checking if device has global config enabled
    if device_use_global_conf == 1:
        # It does - check if the current config is different
        connect.enable()
        running_config = connect.send_command('show run')

        print("Finding current username")
        current_username = running_config
        current_username = current_username.split("username ", 1)
        current_username = current_username[1].split("password")[0]
        print(current_username)

        # print("Finding current domain-name")
        # current_domain_name = running_config
        # current_domain_name = current_domain_name.split("domain-name ", 1)
        # current_domain_name = current_domain_name[1].split("\n")[0]
        # print(current_domain_name)

        current_domain_name = running_config
        try:
            current_domain_name = current_domain_name.split("domain name ", 1)
            current_domain_name = current_domain_name[1].split("\n")[0]
        except:
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
        applied_config = connect.send_config_set(config_commands, cmd_verify=False)     # Apply config to device

        # VLAN MANAGEMENT
        # Get all deleted Vlans from the DB
        # Loop through and remove from device
        localdb_del_vlans = f"SELECT * FROM `vlans` WHERE deleted='1'"
        localdb_cursor.execute(localdb_del_vlans)
        localdb_vlan_data = localdb_cursor.fetchall()

        for vlan in localdb_vlan_data:
            vlan_number = vlan[1]
            delete_vlan = [f'no int vlan {vlan_number}']
            print(delete_vlan)
            output = connect.send_config_set(delete_vlan, cmd_verify=False)

        # Get all active Vlans from the db
        # Loop through and add to device
        # Add description / name too
        localdb_add_vlans = f"SELECT * FROM `vlans` WHERE deleted='0'"
        localdb_cursor.execute(localdb_add_vlans)
        localdb_vlan_data = localdb_cursor.fetchall()

        for vlan in localdb_vlan_data:
            vlan_number = vlan[1]
            vlan_name = vlan[2]
            add_vlan = [    f'int vlan {vlan_number}',
                            f'desc {vlan_name}']
            print(add_vlan)
            output = connect.send_config_set(add_vlan, cmd_verify=False)

    else:
        # Check if running config is different to device table config, if so, change

        # UNCOMMENT AFTER TESTING
        connect.enable()
        running_config = connect.send_command('show run')

        # Gather data from running-conf
        print("Finding current username")
        current_username = running_config
        current_username = current_username.split("username ", 1)
        current_username = current_username[1].split("password")[0]
        print(current_username)

        # print("Finding current domain-name")
        # current_domain_name = running_config
        # current_domain_name = current_domain_name.split("domain-name ", 1)
        # current_domain_name = current_domain_name[1].split("\n")[0]
        # print(current_domain_name)

        current_domain_name = running_config
        print(current_domain_name)
        try:
            current_domain_name = current_domain_name.split("domain name ", 1)
            current_domain_name = current_domain_name[1].split("\n")[0]
        except:
            try:
                current_domain_name = current_domain_name.split("domain-name ", 1)
                current_domain_name = current_domain_name[1].split("\n")[0]
            except:
                current_domain_name = 'example.org'
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
                            f'hostname {device_hostname}'] # CHECK THIS TOO AS IN THE GLOBAL_CONF
        applied_config = connect.send_config_set(config_commands, cmd_verify=False)     # Apply config to device


    print(f"Device {device_ip_address} done")



print("End")