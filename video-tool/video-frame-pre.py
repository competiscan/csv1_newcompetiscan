#import cv2     # for capturing videos
#import math   # for mathematical operations
#import matplotlib.pyplot as plt    # for plotting the images %matplotlib inline
#import pandas as pd
import os
#from keras.preprocessing import image   # for preprocessing the images
#import numpy as np    # for mathematical operations
#from keras.utils import np_utils
#from skimage.transform import resize   # for resizing images
#from dbconfig import dbconn,mydb
from dbconfig import MySQLHost
import subprocess
connectionObj = MySQLHost()

if not os.path.exists("video-frame"):
	os.mkdir("video-frame")
try:
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
				os.system(cmd)			
							
			else:
				print('already frame exist for id: '+str(vid))
		print('Done!')
	else:
		print('There are no video exist to fetch frame')
except:
	print('There are some error incountered')	
cmd = ['pgrep -f .*python.*download-video.py']
process = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, 
stderr=subprocess.PIPE)
my_pid, err = process.communicate()
if len(my_pid.splitlines()) >0:
   #print("Running")  
   os.system("pkill -f download-video.py")	
#subprocess.call(['python','/var/www/html/competiscan.com/video-tool/video-frame.py'])
subprocess.call(['python','/srv/httpd/competiscan.com/html/video-tool/video-frame.py'])
