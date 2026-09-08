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
		keyword=row['keyword'].lower()
		vid_query='SELECT id FROM `cscan_youtube_video` WHERE status=3 order by id desc '		
		vid_records = connectionObj.select(vid_query,())
		if vid_records is not None:
			for vidval in vid_records:
				vid=vidval['id']
				frame_query='SELECT id,frame_text,frame_name FROM `cscan_youtube_video_frame` WHERE frame_text IS NOT NULL AND video_id=%s'
				where_frame_val=(vid,)				
				frame_records = connectionObj.select(frame_query,where_frame_val,())
				if frame_records is not None:			
					matchedFrame=[]
					for frameval in frame_records:
						fid=frameval['id']						
						frame_text=frameval['frame_text'].lower()
						frame_name=frameval['frame_name']														
						if(keyword in frame_text):
							#print('keyword: '+ keyword+' text: '+frame_text+' frame name: '+frame_name)					
							frame_time=frame_name.replace("frame", "")
							frame_time=int(frame_time.replace(".jpg", ""))					  
							matchedFrame.append(frame_time)
							
					#print(matchedFrame)					
					if(len(matchedFrame)>0):
						matchedFrame.sort()
						matched_time=[]
						inc_insert=0
												
						for timeframe in matchedFrame:
							timeframe=int(timeframe)
							if(timeframe==1):
								matched_time.append(timeframe)
								inc_insert=timeframe
							elif (((timeframe-1) not in matched_time) and (inc_insert !=(timeframe-1))):						
								matched_time.append(timeframe)						
							else:
								inc_insert=timeframe				
						
						if(len(matched_time)>0):
							actual_matched_time=[]
							matched_time.sort()
							for gettime in matched_time:
								frametime=gettime
								if(gettime>5):
									frametime=gettime-2	
								actual_time=convertSecondToMinute(frametime)
								actual_matched_time.append(actual_time)
								
							save_time=', '.join(actual_matched_time)
							check_query='SELECT id FROM cscan_youtube_keywords_match where video_id=%s AND keyword_id=%s '
							whereval=(vid,kid,)							
							record = connectionObj.select(check_query,whereval,())
							if record is None:					
								insert_query="Insert into cscan_youtube_keywords_match (video_id,keyword_id,keyword_match_time) VALUES (%s, %s, %s) "
								data = (vid,kid,save_time,)
								connectionObj.execute(insert_query,data)				
								
								
							else:				
								updt_query="Update cscan_youtube_keywords_match set keyword_match_time=%s where video_id=%s AND keyword_id=%s "
								updt_data = (save_time,vid,kid,)
								connectionObj.execute(updt_query,updt_data)		
											
						
								
						print(matched_time)
					else:
						print('hello ')						
						check_query_match='SELECT id FROM cscan_youtube_keywords_match where video_id=%s AND keyword_id=%s '
						where_check_val=(vid,kid,)
						check_records = connectionObj.select(check_query_match,where_check_val,())
						
						if check_records is None:							
							insert_query_m="Insert into cscan_youtube_keywords_match (video_id,keyword_id) VALUES (%s, %s) "
							data_m = (vid,kid,)
							connectionObj.execute(insert_query_m,data_m)							
					
					#print(matchedFrame)	
					
				else:	
					print('There are no data available to detect text for video id: '+str(vid))		
			
	print('Done!')		
else:
	print('There are no keyword exist in database!')
	
cmd = ['pgrep -f .*python.*detect-text.py']
process = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, 
stderr=subprocess.PIPE)
my_pid, err = process.communicate()
if len(my_pid.splitlines()) >0:
   #print("Running")  
   os.system("pkill -f detect-text.py")	
subprocess.call(['python','/var/www/html/competiscan.com/video-tool/sentiment.py'])	
#subprocess.call(['python','/srv/httpd/competiscan.com/html/video-tool/sentiment.py'])


 

