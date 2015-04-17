#! /usr/bin/env python3                                                         
                                                                                
import csv                                                                      
import json                                                                     
import sys                                                                      
import requests        
from time import sleep                                                         
                                                                                
                                                                                
# SETTINGS                                                                      
RPC_USER = ''                                                                
RPC_PASSWORD = ''                                                                 
RPC_HOST = ''                                                          
RPC_PORT = 4000                   

BTC_USER = ''
BTC_PASSWORD = ''
BTC_HOST = ''
BTC_PORT = 8332                         

#json_print = lambda x: print(json.dumps(x, sort_keys=True, indent=4))           

headers = {'content-type': 'application/json'}                                  
                                                                                
                                                                                
def api(payload, rpc, password, host, port):                                                               
    host = 'http://{}:{}@{}:{}'.format(rpc, password, host, port)
    response = requests.post(host, data=json.dumps(payload), headers=headers)   
    try:                                                                        
        return response.json()['result']                                        
    except KeyError: 
        try:
            print(response.json()['data'])                                             
        except KeyError:
            print(response.json()['error'])
        return False                                                            
                                                                                
with open(sys.argv[1], 'r') as csvfile:                                         
    reader = csv.reader(csvfile)                                                
    for row in reader:                                                          
        print('Row {}: {}'.format(reader.line_num, row))                        
        source, destination, asset, quantity, fee = row                              
        sleep(5)                                                                        
        # Create send.                                                          
        payload = {                                                             
            "method": "create_send",                                            
            "params": {'source': source, 'destination': destination, 'asset': asset, 'quantity': int(quantity), 'fee': int(fee), 'allow_unconfirmed_inputs': True, 'regular_dust_size': int(sys.argv[2]), 'multisig_dust_size': int(sys.argv[2]), 'pubkey': sys.argv[3], 'op_return_value': int(sys.argv[2]), 'encoding': 'multisig'},
            "jsonrpc": "2.0",                                                   
            "id": 0                                                             
        }
                                  
        unsigned_tx = api(payload, RPC_USER, RPC_PASSWORD, RPC_HOST, RPC_PORT)                                              
        if not unsigned_tx: continue                                            
                                                                               
        # Sign tx.                                                              
        payload = {                                                             
            "method": "signrawtransaction",                                                
            "params": [unsigned_tx],                         
            "jsonrpc": "2.0",                                                   
            "id": 0                                                             
        }                                                                       
        signed_tx = api(payload, BTC_USER, BTC_PASSWORD, BTC_HOST, BTC_PORT)                                                

        if not signed_tx: continue                                              
                                                                                
        # Broadcast tx.                                                         
        payload = {                                                             
            "method": "sendrawtransaction",                                           
            "params": [signed_tx['hex']],                             
            "jsonrpc": "2.0",                                                   
            "id": 0                                                             
        }                                                                       
        tx_hash = api(payload, BTC_USER, BTC_PASSWORD, BTC_HOST, BTC_PORT)                                                  
        print('Transaction', tx_hash)                                           
                                                                     
# vim: tabstop=8 expandtab shiftwidth=4 softtabstop=4
