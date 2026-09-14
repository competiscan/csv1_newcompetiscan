import json
#from dbconfig import dbconn,mydb
import sys
from urllib.parse import urlparse,parse_qs
from youtube_transcript_api import YouTubeTranscriptApi
from dbconfig import MySQLHost
import subprocess
import os
connectionObj = MySQLHost()

vid_query='SELECT id,youtube_url FROM `cscan_youtube_video` WHERE audio_text_status=%s order by id desc '
vid_records = connectionObj.select(vid_query,(str('0'),),())
if vid_records is not None:
	for vidval in vid_records:
		vid=vidval['id']
		youtube_url=vidval['youtube_url']
		url_data = urlparse(youtube_url)
		query = parse_qs(url_data.query)
		youtube_video_id = query["v"][0]
		try:		
			transcript_text=YouTubeTranscriptApi.get_transcript(youtube_video_id)
			#print(transcript_text)	
			for item in transcript_text:	
				if(item['text']!='[Music]'):
					detect_text=item['text']
					start_time=int(item['start'])
					duration=item['duration']
					print(vid,detect_text,start_time,duration)
					query='SELECT id FROM cscan_youtube_audio_text where video_id=%s and audio_text=%s'
					wheredata=(vid,detect_text,)
					already_record = connectionObj.select(query,wheredata,())
					if already_record is None:
						insert_query="Insert into cscan_youtube_audio_text (video_id,start_time,duration,audio_text) VALUES (%s, %s, %s,%s)"
						data = (vid,start_time,duration,detect_text,)
						connectionObj.execute(insert_query,data)
		
			updt_query_main="Update cscan_youtube_video set audio_text_status=1 where id=%s "
			updt_data_main = (vid,)
			connectionObj.execute(updt_query_main,updt_data_main)		
			
		except:
			updt_query_main="Update cscan_youtube_video set audio_text_status=2,is_completed=1 where id=%s "
			updt_data_main = (vid,)
			connectionObj.execute(updt_query_main,updt_data_main)				
			 
			print('There are transcipt option not available for video id '+str(vid)+' and url is: '+youtube_url)	
else:
	print('There are no record exist to fetch audio text!')

cmd = ['pgrep -f .*python.*video-frame.py']
process = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, 
stderr=subprocess.PIPE)
my_pid, err = process.communicate()
if len(my_pid.splitlines()) >0:
   #print("Running")  
   os.system("pkill -f video-frame.py")	
#subprocess.call(['python','/var/www/html/competiscan.com/video-tool/search_keyword_audio.py'])
subprocess.call(['python','/srv/httpd/competiscan.com/html/video-tool/search_keyword_audio.py'])
