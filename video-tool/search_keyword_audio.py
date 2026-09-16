import json
#from dbconfig import dbconn,mydb
from dbconfig import MySQLHost
import sys
import subprocess
import os
connectionObj = MySQLHost()

def convertSecondToMinute(seconds): 
    seconds = seconds % (24 * 3600) 
    hour = seconds // 3600
    seconds %= 3600
    minutes = seconds // 60
    seconds %= 60      
    return "%d:%02d:%02d" % (hour, minutes, seconds)

check_query='SELECT id,keyword FROM cscan_youtube_search_keywords'
records = connectionObj.select(check_query,())
#print(records)
if records is not None:
	for row in records:
		kid=row['id']
		#print(kid) 
		#sys.exit()
		keyword=row['keyword'].lower()
		vid_query='SELECT id FROM `cscan_youtube_video` WHERE audio_text_status=1 order by id desc '		
		vid_records = connectionObj.select(vid_query,())
		if vid_records is not None:		
			for vidval in vid_records:
				vid=vidval['id']
				audio_query='SELECT id,start_time,audio_text,duration FROM `cscan_youtube_audio_text` WHERE audio_text IS NOT NULL AND video_id=%s'
				where_audio_val=(vid,)				
				audio_records = connectionObj.select(audio_query,where_audio_val,())
				if audio_records is not None:						
					matchedAudio=[]
					for audioval in audio_records:
						aid=audioval['id']
						start_time=audioval['start_time']						
						audio_text=audioval['audio_text'].lower()
						duration=audioval['duration']														
						if(keyword in audio_text):
							matchedAudio.append(start_time)							
									
					if(len(matchedAudio)>0):
						matchedAudio.sort()
						matched_time=[]
						inc_insert=0
																		
						for timeaudio in matchedAudio:
							timeaudio=int(timeaudio)
							if (((timeaudio-1) not in matched_time) and (inc_insert !=(timeaudio-1))):						
								matched_time.append(timeaudio)						
							else:
								inc_insert=timeaudio				
						
						if(len(matched_time)>0):
							matched_time.sort()
							actual_matched_time=[]
							for gettime in matched_time:
								actual_time=convertSecondToMinute(gettime)
								actual_matched_time.append(actual_time)
								
							save_time=', '.join(actual_matched_time)
							check_query='SELECT id FROM cscan_youtube_keywords_match where video_id=%s AND keyword_id=%s '
							whereval=(vid,kid,) 							
							record = connectionObj.select(check_query,whereval,())
							if record is None:					
								insert_query="Insert into cscan_youtube_keywords_match (video_id,keyword_id,audio_match_time) VALUES (%s, %s, %s) "
								data = (vid,kid,save_time,)
								connectionObj.execute(insert_query,data)			
								
							else:				
								updt_query="Update cscan_youtube_keywords_match set audio_match_time=%s where video_id=%s AND keyword_id=%s "
								updt_data = (save_time,vid,kid,)
								connectionObj.execute(updt_query,updt_data)					
													
								
						print(matched_time)
					else:
						#print('hello ')						
						check_query_match='SELECT id FROM cscan_youtube_keywords_match where video_id=%s AND keyword_id=%s '
						where_check_val=(vid,kid,)						
						check_records = connectionObj.select(check_query_match,where_check_val,())
						
						if check_records is None:						
							insert_query_m="Insert into cscan_youtube_keywords_match (video_id,keyword_id) VALUES (%s, %s) "
							data_m = (vid,kid,)
							connectionObj.execute(insert_query_m,data_m)						
					#print(matchedFrame)					
				else:	
					print('There are no data available to match keywords for video id: '+str(vid))		
			
	print('Done!')		
else:
	print('There are no keyword exist in database!')
	
cmd = ['pgrep -f .*python.*youtube_transcript.py']
process = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, 
stderr=subprocess.PIPE)
my_pid, err = process.communicate()
if len(my_pid.splitlines()) >0:
   #print("Running")  
   os.system("pkill -f youtube_transcript.py")	
	
#subprocess.call(['python','/var/www/html/competiscan.com/video-tool/search_logo.py'])
subprocess.call(['python','/srv/httpd/competiscan.com/html/video-tool/search_logo.py'])


 

