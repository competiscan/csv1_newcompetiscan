import cv2
import os
import glob
from dbconfig2 import MySQLHost
method = cv2.TM_SQDIFF_NORMED
import sys
import numpy as np
import urllib
connectionObj = MySQLHost()

def convertSecondToMinute(seconds): 
    seconds = seconds % (24 * 3600) 
    hour = seconds // 3600
    seconds %= 3600
    minutes = seconds // 60
    seconds %= 60      
    return "%d:%02d:%02d" % (hour, minutes, seconds)
    
check_query='SELECT id,logo_name,logo_path FROM cscan_youtube_search_logos'
#dbconn.execute(check_query)
#records = dbconn.fetchall
records = connectionObj.select(check_query,())
if records is not None:
	for row in records:
		lid=row['id']		
		logo_name=row['logo_name'].lower()
		logo_path=row['logo_path']
		small_image=logo_path+'/'+logo_name
		small_image = cv2.imread(small_image)
		image_to_compare=small_image
		img2=cv2.imread(logo_path+'/'+logo_name, cv2.IMREAD_GRAYSCALE)
		image1=img2	
				
		#vid_query='SELECT id FROM `cscan_youtube_video` WHERE status>=2 order by id desc '
		vid_query='SELECT id FROM `cscan_youtube_video` WHERE status>=%s order by id desc '				
		#dbconn.execute(vid_query)
		#vid_records = dbconn.fetchall()
		vid_records = connectionObj.select(vid_query,(str('2'),),())
		if vid_records is not None:
			for vidval in vid_records:
				vid=vidval['id']
				check_query='SELECT id FROM cscan_youtube_logos_match where video_id=%s AND logo_id=%s '
				whereval=(vid,lid,) 
				#dbconn.execute(check_query,whereval)
				#record = dbconn.fetchall()
				record = connectionObj.select(check_query,whereval,())
				if record is None:			
					continue			
				frame_query='SELECT id,frame_name,frame_path FROM `cscan_youtube_video_frame` WHERE video_id=%s'
				where_frame_val=(vid,) 
				#dbconn.execute(frame_query,where_frame_val)				
				#frame_records = dbconn.fetchall()
				frame_records = connectionObj.select(frame_query,where_frame_val,())
				if frame_records is not None:		
					matchedFrame=[]
					for frameval in frame_records:
						fid=frameval['id']
						frame_name=frameval['frame_name']
						frame_path=frameval['frame_path']
						#large_image=frame_path+'/'+frame_name;
						large_image = cv2.imread(frame_path+'/'+frame_name)
						img1=cv2.imread(frame_path+'/'+frame_name, cv2.IMREAD_GRAYSCALE)
						image2=img1
						#print(large_image)				
						
						#### New added method to compare ########
						try:
							minHessian = 400
							compare_points=29.2
							#detector = cv2.xfeatures2d_SURF.create(hessianThreshold=minHessian)
							detector = cv2.xfeatures2d.SURF_create(hessianThreshold=minHessian)
							keypoints1, descriptors1 = detector.detectAndCompute(img1, None)
							keypoints2, descriptors2 = detector.detectAndCompute(img2, None)					
							
							matcher = cv2.DescriptorMatcher_create(cv2.DescriptorMatcher_FLANNBASED)					
							
							knn_matches = matcher.knnMatch(descriptors2, descriptors1, 2)
							knn_matches2 = matcher.knnMatch(descriptors1, descriptors2, 2)
							#-- Filter matches using the Lowe's ratio test
							ratio_thresh = 0.6
							good_matches = []
							good_matches2= []
							for m,n in knn_matches:
								if m.distance < ratio_thresh * n.distance:
									good_matches.append(m)
							
							for a,b in knn_matches2:
								if a.distance < ratio_thresh * b.distance:
									good_matches2.append(a)
							
							number_keypoints = 0
							if len(keypoints1) <= len(keypoints2):
								number_keypoints = len(keypoints1)
							else:
								number_keypoints = len(keypoints2)

							if(number_keypoints<=0):
								number_keypoints=1
							#matched_points=len(good_points) / number_keypoints * 100
							matched_points=len(good_matches) / number_keypoints * 100
							matched_points2=len(good_matches2) / number_keypoints * 100
							
							##### using other method to ensure ########							
							# Initiate SIFT detector							
							sift = cv2.xfeatures2d.SIFT_create()
							# find the keypoints and descriptors with SIFT
							kp1, des1 = sift.detectAndCompute(image1,None)
							kp2, des2 = sift.detectAndCompute(image2,None)

							FLANN_INDEX_KDTREE = 0
							index_params = dict(algorithm = FLANN_INDEX_KDTREE, trees = 5)													
							search_params = dict(checks = 50)
							flann = cv2.FlannBasedMatcher(index_params, search_params)
							matches = flann.knnMatch(des1,des2,k=2)

							# store all the good matches as per Lowe's ratio test.
							good = []
							for m,n in matches:
								if m.distance < 0.6*n.distance:
									good.append(m)						
							##### End using other method to ensure ########
							
							print(len(good_matches),len(good),matched_points,number_keypoints,lid,frame_name)
								
							#if((matched_points>=compare_points and len(good_matches)>=45 and len(good)>=48) or (len(good)>50 and len(good_matches)>=45)):
							if((len(good)>=76) or (matched_points>=25 and len(good)>=36) or (matched_points2>=100 and matched_points>=3)):		
								frame_time=frame_name.replace("frame", "")
								frame_time=int(frame_time.replace(".jpg", ""))					  
								matchedFrame.append(frame_time)					
												
						except Exception as e:							
							print('not matched due to error coming on this ',lid,frame_name, e)
												
						
					
					#print(matchedFrame)	
					if(len(matchedFrame)>0):
						matchedFrame.sort()
						matched_time=[]
						inc_insert=0
						print(matchedFrame)						
						for timeframe in matchedFrame:
							#print(timeframe)
							if(timeframe==1):
								matched_time.append(timeframe)
								inc_insert=timeframe
							elif (((timeframe-1) not in matched_time) and (inc_insert !=(timeframe-1))):
								#print('match with frame: ',timeframe)						
								matched_time.append(timeframe)						
							else:
								#print('not match with frame: ',timeframe)
								inc_insert=timeframe				
						#print(matched_time)
						if(len(matched_time)>0):
							actual_matched_time=[]
							for gettime in matched_time:
								frametime=gettime
								if(gettime>5):
									frametime=gettime-2	
								actual_time=convertSecondToMinute(frametime)
								actual_matched_time.append(actual_time)
								
							save_time=', '.join(actual_matched_time)
							check_query='SELECT id FROM cscan_youtube_logos_match where video_id=%s AND logo_id=%s '
							whereval=(vid,lid,) 
							#dbconn.execute(check_query,whereval)
							#record = dbconn.fetchone()
							record = connectionObj.select(check_query,whereval,())
							if record is None:					
								insert_query="Insert into cscan_youtube_logos_match (video_id,logo_id,logo_match_time) VALUES (%s, %s, %s) "
								data = (vid,lid,save_time,)					
								#dbconn.execute(insert_query,data)
								#mydb.commit()
								connectionObj.execute(insert_query,data)
							else:				
								updt_query="Update cscan_youtube_logos_match set logo_match_time=%s where video_id=%s AND logo_id=%s "
								updt_data = (save_time,vid,lid,)					
								#dbconn.execute(updt_query,updt_data)
								#mydb.commit()
								connectionObj.execute(updt_query,updt_data)
					else:											
						check_query_match='SELECT id FROM cscan_youtube_logos_match where video_id=%s AND logo_id=%s '
						where_check_val=(vid,lid,) 
						#dbconn.execute(check_query_match,where_check_val)
						#check_records = dbconn.fetchone()
						check_records = connectionObj.select(check_query_match,where_check_val,())
						
						if check_records is None:						
							insert_query_m="Insert into cscan_youtube_logos_match (video_id,logo_id) VALUES (%s, %s) "
							data_m = (vid,lid,)
							#print(insert_query_m,data_m)					
							#dbconn.execute(insert_query_m,data_m)
							#mydb.commit()
							connectionObj.execute(insert_query_m,data_m)
		else:
			print('There are no data available to match logo for video id: '+str(vid))		
else:
	print('There are no logos exist in database!')
	
