#!/usr/bin/env python

import time
import datetime
import sys
import socket


from getpass import getpass
from textwrap import TextWrapper
import re
import pytz

from sets import Set


import tweepy

TERMS = ["gunman","shooting","gunshots","gun shots","explosion","mass shooting","hiding","heard gun","hope you are safe","hope everyone is safe","everyone in","praying for","stay safe","the victims","going on in","pray for","is happening","happening in","OMG","the people in","for the people","for everyone in","those in","Shooting In","Whats happening","happening","everyone in","shooting in","on in","for those in","safe","the victims","stay","happened in","Please pray for","Please pray","the people","for everyone","praying for all","thoughts are with","prayers are with","horrible","right now","going on","My prayers","My thoughts","hope everyone in","scary","safe in","people of","hope everyone","so sad","everyone is safe","killed","dead","{NUM} killed","{NUM} dead"]
SEEN = [Set(),Set()]
SEENPOS = 0

sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)

save = open('twitter.txt','a+')

class StreamWatcherListener(tweepy.StreamListener):

	status_wrapper = TextWrapper(width=60, initial_indent='    ', subsequent_indent='    ')

	def on_status(self, status):
		global SEEN
		global SEENPOS
		try:
			if not status.author.id in SEEN[0] and not status.id in SEEN[1]:
				SEEN[SEENPOS].add(status.author.id)
				if( len(SEEN[SEENPOS]) > 2500 ):
					#print "Swapping Buffers ",SEENPOS, SEEN[SEENPOS]
					SEENPOS = 1-SEENPOS

					SEEN[SEENPOS].clear()
				text = " " +status.text+" "
				text = text.replace(",", " ")
				text = text.replace("\n", " ")
				text = text.replace("'", "")
				text = text.replace("\"", "")
				text = re.sub(r'https?:\/\/.*[\r\n]*', '', text, flags=re.MULTILINE)
				text = re.sub(r'@[A-Za-z0-9_]+', '', text, flags=re.MULTILINE)
				text = re.sub(r'#[A-Za-z0-9_]+', '', text, flags=re.MULTILINE)
				text = re.sub(r' [0-9]+ ', ' {NUM} ', text, flags=re.MULTILINE)
				text = re.sub(r' .*[:\\;@$%^&*()-+~]+.* ', ' ', text, flags=re.MULTILINE)
				text = re.sub(r' [:\\;@$%^&*()-+~]+ ', ' ', text, flags=re.MULTILINE)
				text = text.replace(".", " ")
				text = text.replace("!", " ")
				text = text.replace("?", " ")
				timestamp = int((status.created_at - datetime.datetime(1970, 1, 1)).total_seconds())
				#time.mktime(status.created_at.replace(tzinfo=pytz.UTC).timetuple()) #.replace(tzinfo=pytz.UTC)

				if len(text)> 10 and len(text) < 88:
					#print self.status_wrapper.fill(text)
					#print timestamp
					sock.sendto("%d|0|%s" % (timestamp,text.encode('utf-8')), ("127.0.0.1", 4125))
					save.write( "%d|%s|%s\n" % (timestamp,status.id,text.encode('utf-8')) );
					save.flush()
			#else:
				#print "Repeat: ", status.author.screen_name
				#print '\n %s  %s  via %s\n' % (status.author.screen_name, status.created_at, status.source)
				#print status.place.name if status.place else "Undefined place"
				#if status.coordinates != None:
				#	print status.coordinates
		except Exception as e:
			#print e
			# Catch any unicode errors while printing to console
			# and just ignore them to avoid breaking application.
			pass

	def on_error(self, status_code):
		print 'An error has occured! Status code = %s' % status_code
		return True  # keep stream alive

	def on_timeout(self):
		print 'Snoozing Zzzzzz'


def main():
	# Prompt for login credentials and setup stream object
	consumer_key = "sJ6pdpIUGWssFNC7HOwGMWFYR"
	consumer_secret = "WeAZfZf0IAmRNZrJKOtlKfsMdYelwYZ6CyGNVQ5Gt3F1Zjie3P"
	access_token = "4790161039-xCCRpwy5r2KbfNetecOUPEYpSSPtjtTUrB08ct3"
	access_token_secret = "WXub1klclHs3IZTUAuvzLWHSpli1aviR3yZ4m8CFgJ70H"

	auth = tweepy.auth.OAuthHandler(consumer_key, consumer_secret)
	auth.set_access_token(access_token, access_token_secret)
	stream = tweepy.Stream(auth, StreamWatcherListener(), timeout=None)

	# Prompt for mode of streaming
	while True:
		print "Watching For: ", TERMS[0:16]
		stream.filter(None, TERMS[0:16])
#"i hope everyone is safe", "sounds like gun", "our prayers", "pray for"

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print '\nGoodbye!'
