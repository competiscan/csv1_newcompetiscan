import cv2     # for capturing videos
import math   # for mathematical operations
# import matplotlib.pyplot as plt    # for plotting the images %matplotlib inline
#import pandas as pd
import os
# from keras.preprocessing import image   # for preprocessing the images
# import numpy as np    # for mathematical operations
#from keras.utils import np_utils
#from skimage.transform import resize   # for resizing images
#from dbconfig import dbconn,mydb
from dbconfig import MySQLHost
import subprocess
connectionObj = MySQLHost()

if not os.path.exists("video-frame"):
	os.mkdir("video-frame")

query='SELECT id,video_name,video_path,status FROM cscan_youtube_video where status=%s'
records = connectionObj.select(query,(str('1'),),())
if records is not None:
	for row in records:
		vid=row['id']
		video_name=row['video_name']
		video_path=row['video_path']
		status=row['status']
		videoFile=video_path+'/'+video_name
		file_name, file_extension = os.path.splitext(videoFile)			
				
		#frameRate = cap.get(5)
		check_query='SELECT id FROM cscan_youtube_video_frame where video_id=%s'
		whereval=(vid,)
		record = connectionObj.select(check_query,whereval,())
		if record is None:			
			if not os.path.exists("video-frame/"+str(vid)):
				os.mkdir("video-frame/"+str(vid))
			count = 0
			
			cmd='ffmpeg -i '+videoFile+' -r 1 video-frame/'+str(vid)+'/frame%01d.jpg'
			#os.system(cmd)
			path = 'video-frame/'+str(vid)+'/'
			files = []
			# r=root, d=directories, f = files
			for r, d, f in os.walk(path):
				for file in f:
					if '.jpg' in file:
						files.append(os.path.join(r, file))
						chk_query='SELECT count(*) FROM `cscan_youtube_search_logos` '		
						#chk_records = connectionObj.select(chk_query,())
						

			for f in files:					
				filepath = f.split("/")
				filename=filepath[-1]					
				query='SELECT id FROM cscan_youtube_video_frame where video_id=%s and frame_name=%s'
				wheredata=(vid,filename,)
				already_record = connectionObj.select(query,wheredata,())				
				if already_record is None:
					insert_query="Insert into cscan_youtube_video_frame (video_id,frame_name,frame_path) VALUES (%s, %s, %s)"
					data = (vid,filename,'video-frame/'+str(vid),)
					connectionObj.execute(insert_query,data)
						
		else:
			print('already frame exist for id: '+str(vid))
		check_query2='SELECT id FROM cscan_youtube_video_frame where video_id=%s'
		whereval2=(vid,)
		record2 = connectionObj.select(check_query2,whereval2,())
		if record2 is not None:
			updt_query="Update cscan_youtube_video set status=2 where id=%s "
			updt_data = (vid,)
			connectionObj.execute(updt_query,updt_data)
	print('Done!')
else:
	print('There are no video exist to fetch frame')
	
cmd = ['pgrep -f .*python.*video-frame-pre.py']
process = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, 
stderr=subprocess.PIPE)
my_pid, err = process.communicate()
if len(my_pid.splitlines()) >0:
   #print("Running")  
   #os.system("pkill -f download-video.py")
   os.system("pkill -f video-frame-pre.py")	
#subprocess.call(['python','/var/www/html/competiscan.com/video-tool/youtube_transcript.py'])
subprocess.call(['python','/srv/httpd/competiscan.com/html/video-tool/youtube_transcript.py'])
