import requests
import json
import csv
import time
import datetime
from socket import *

def get_content():
    api_url = 'http://content.guardianapis.com/search'
    payload = {
		'api-key':              '225f5834-fe12-4951-9072-a649acd0016d',
		'q': 'explosion OR terrorist OR gunman OR killed OR hiding OR shooting OR gunshots OR dead OR "mass shooting"',
		'from-date': '2014-03-01',
		'to-date': '2016-03-01'

    }
    response = requests.get(api_url, params=payload)
    data = response.json() # convert json to python-readable format
    return data["response"]['results']

if __name__ == '__main__':
	data = get_content()

	with open('content.csv', 'wb') as f:
		writer = csv.writer(f, delimiter='|')
		for row in data:
			print "\n\n",row;
			unixtimestamp = int( (datetime.datetime.strptime(row["webPublicationDate"], '%Y-%m-%dT%H:%M:%SZ')-datetime.datetime(1970, 1, 1) ).total_seconds() )
			writer.writerow( [ unixtimestamp , "4", row["webTitle"].encode('utf-8') ])
