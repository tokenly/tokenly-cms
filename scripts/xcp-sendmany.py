#! /usr/bin/env python3                                                         
                                                                                
import csv                                                                      
import json                                                                     
import sys                                                                      
import requests                                                                 
                                                                                
                                                                                
# SETTINGS                                                                      
RPC_USER = ''                                                                
RPC_PASSWORD = ''                                                                 
RPC_HOST = ''                                                          
RPC_PORT = 4000                                            
                                                                                
                                                                                
json_print = lambda x: print(json.dumps(x, sort_keys=True, indent=4))           
headers = {'content-type': 'application/json'}                                  
                                                                                
                                                                                
def api(payload):                                                               
    host = 'http://{}:{}@{}:{}'.format(RPC_USER, RPC_PASSWORD, RPC_HOST, RPC_PORT)
    response = requests.post(host, data=json.dumps(payload), headers=headers)   
    try:                                                                        
        return response.json()['result']                                        
    except KeyError:                                                            
        print(response.json()['error'])                                         
        return False                                                            
                                                                                
with open(sys.argv[1], 'r') as csvfile:                                         
    reader = csv.reader(csvfile)                                                
    for row in reader:                                                          
        print('Row {}: {}'.format(reader.line_num, row))                        
        source, destination, asset, quantity, fee = row                              
                                                                                
        # Create send.                                                          
        payload = {                                                             
            "method": "create_send",                                            
            "params": {'source': source, 'destination': destination, 'asset': asset, 'quantity': int(quantity), 'fee': int(fee), 'allow_unconfirmed_inputs': True, 'regular_dust_size': int(sys.argv[2]), 'multisig_dust_size': int(sys.argv[2])},
            "jsonrpc": "2.0",                                                   
            "id": 0                                                             
        }   
        #raise Exception(payload)                                         
        unsigned_tx = api(payload)                                              
        if not unsigned_tx: continue                                            
                                                                                
        # Sign tx.                                                              
        payload = {                                                             
            "method": "sign_tx",                                                
            "params": {'unsigned_tx_hex': unsigned_tx},                         
            "jsonrpc": "2.0",                                                   
            "id": 0                                                             
        }                                                                       
        signed_tx = api(payload)                                                
        if not signed_tx: continue                                              
                                                                                
        # Broadcast tx.                                                         
        payload = {                                                             
            "method": "broadcast_tx",                                           
            "params": {'signed_tx_hex': signed_tx},                             
            "jsonrpc": "2.0",                                                   
            "id": 0                                                             
        }                                                                       
        tx_hash = api(payload)                                                  
        print('Transaction', tx_hash)                                           
                                                                                
                                                                                
# vim: tabstop=8 expandtab shiftwidth=4 softtabstop=4
