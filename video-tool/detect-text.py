import boto3
from PIL import Image
import io
import json
import subprocess
import os
#from dbconfig import dbconn,mydb
from dbconfig import MySQLHost
connectionObj = MySQLHost()

client = boto3.client('rekognition')
check_query='SELECT id FROM cscan_youtube_video where status=%s'
records = connectionObj.select(check_query,(str('2'),),())
if records is not None:
	for row in records:
		vid=row['id']
		check_frame_query='SELECT id,video_id,frame_name,frame_path FROM cscan_youtube_video_frame where status=0 and video_id=%s'
		whereval=(vid,)
		frame_records = connectionObj.select(check_frame_query,whereval,())		
		if(len(frame_records)):
			for frameval in frame_records:
				fid=frameval['id']
				vid=frameval['video_id']			
				frame_name=frameval['frame_name']
				frame_path=frameval['frame_path']		
				framefile=frame_path+'/'+frame_name
				try:
					image = Image.open(framefile)
					stream = io.BytesIO()
					image.save(stream,format="png")
					image_binary = stream.getvalue()
					print(image_binary)
					response = client.detect_text(Image={'Bytes':image_binary})
					#print(response.pretty()) 
					#json_formatted_str = json.dumps(response, indent=2)
					linesArr=[]
					detectedtext='';
					for item in response['TextDetections']:	
						if(item['Type']=='LINE'):
							linesArr.append(item['DetectedText'])		
							
					if(len(linesArr)>0):
						detectedtext=' '.join(linesArr)	
					if(len(detectedtext)>0):
						updt_query="Update cscan_youtube_video_frame set status=1,frame_text=%s where id=%s "
						updt_data = (detectedtext,fid,)
						connectionObj.execute(updt_query,updt_data)
				except:					
					print('There are some issue with detect text in '+framefile)
				
				#print(detectedtext)
			updt_query_main="Update cscan_youtube_video set status=3 where id=%s "
			updt_data_main = (vid,)
			connectionObj.execute(updt_query_main,updt_data_main)			
			
		else:	
			print('There are no data available to detect text for video id: '+str(vid))		
			
	print('Done!')		
else:
	print('There are no video exist to detect text!')

cmd = ['pgrep -f .*python.*search_logo.py']
process = subprocess.Popen(cmd, shell=True, stdout=subprocess.PIPE, 
stderr=subprocess.PIPE)
my_pid, err = process.communicate()
if len(my_pid.splitlines()) >0:
   #print("Running")  
   os.system("pkill -f search_logo.py")	
#subprocess.call(['python','/var/www/html/competiscan.com/video-tool/search_keyword_frame.py'])
subprocess.call(['python','/srv/httpd/competiscan.com/html/video-tool/search_keyword_frame.py'])


 

