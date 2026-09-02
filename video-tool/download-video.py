from __future__ import unicode_literals
#import youtube_dl
import sys
#importing the module 
#from dbconfig import dbconn,mydb
from dbconfig import MySQLHost
import os
from pytube import YouTube
import uuid
import subprocess
import ssl
ssl._create_default_https_context = ssl._create_unverified_context
connectionObj = MySQLHost()

def downloadYoutubeVideo(link,download_video_path):	
	blankval=''
	f_name=''
	print(link)
	filename=str(uuid.uuid4())
	print(filename)
	video_path=download_video_path+'/'+filename+'.mp4'
	yt = YouTube(link)
	stream = yt.streams.get_highest_resolution()
	#stream = yt.streams.filter(file_extension='mp4').first()
	dd=stream.download(output_path=download_video_path)
	os.rename(dd,video_path)
	if(filename!=''):		
		for root, dirs, files in os.walk(download_video_path):	
			#print("files chck:",files)		
			check_filename=filename+'.mp4'
			if check_filename in files :
				f_name = check_filename
				#print('f_name:',f_name)
				break
			else:
				allfiles=[]
				for file_name in files:
					without_ext_file_name=os.path.splitext(file_name)[0]
					if((filename==without_ext_file_name) or (check_filename==without_ext_file_name)):
						f_name=file_name						
						break
				if(f_name!=''):
					break
	if(f_name!=''):
		#print('fname',f_name)
		return f_name
	else:
		#print('blank',blankval)
		return blankval

if not os.path.exists("youtube_video"):
	os.mkdir("youtube_video")
download_video_path='youtube_video'

url_query='SELECT id,youtube_url FROM cscan_youtube_video where status=%s'
url_records = connectionObj.select(url_query,(str('0'),),())
if url_records is not None:
	for urlval in url_records:
		vid=urlval['id']
		link=urlval['youtube_url']		
		video_name=downloadYoutubeVideo(link,download_video_path)
		#print(video_name)
		if(video_name!=''):
			updt_query="Update cscan_youtube_video set video_name=%s,video_path=%s,status=%s where id=%s"	
			data = (video_name,download_video_path,1,vid,)			
			connectionObj.execute(updt_query,data)			
			print('updated successfully')
		else:
			print('Something went wrong! Please review youtube url')

else:
	print('There are no video url exist to process')

cmd = ['pgrep -f .*python.*sentiment.py']
process = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, 
stderr=subprocess.PIPE)
my_pid, err = process.communicate()
if len(my_pid.splitlines()) >0:
   #print("Running")   
   os.system("pkill -f sentiment.py")	
url_query='SELECT id,youtube_url FROM cscan_youtube_video where is_completed=0'
url_records = connectionObj.select(url_query,())
if url_records is not None:
	#subprocess.call(['python','/var/www/html/competiscan.com/video-tool/video-frame-pre.py'])
	subprocess.call(['python','/srv/httpd/competiscan.com/html/video-tool/video-frame-pre.py'])
	
	

