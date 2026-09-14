'''

from __future__ import print_function
import cv2 as cv2
import numpy as np
import argparse

def MSE(img1, img2):
    squared_diff = img1 -img2
    summed = np.sum(squared_diff)
    num_pix = img1.shape[0] * img1.shape[1] #img1 and 2 should have same shape
    err = summed / num_pix
    return err

small = cv2.imread('search-logo/614hig.png')
large = cv2.imread('video-frame/41/frame1.jpg')
pixel = np.reshape(small[3,3], (1,3))
lower =[pixel[0,0]-10,pixel[0,1]-10,pixel[0,2]-10]
lower = np.array(lower, dtype = 'uint8')
upper =[pixel[0,0]+10,pixel[0,1]+10,pixel[0,2]+10]
upper = np.array(upper, dtype = 'uint8')
mask = cv2.inRange(large,lower, upper)
mask2 = cv2.inRange(small, lower, upper)
#print(mask,mask2)

im, contours, hierarchy = cv2.findContours(mask,cv2.RETR_EXTERNAL,cv2.CHAIN_APPROX_SIMPLE)
#cv2.drawContours(large, contours, -1, (0,0,255), 1)

if(len(contours)>0):
	
	cnt = max(contours, key = cv2.contourArea)
else:
	cnt=1	
x,y,w,h = cv2.boundingRect(cnt)
wanted_part = mask[y:y+h, x:x+w]
wanted_part = cv2.resize(wanted_part, (mask2.shape[1], mask2.shape[0]), interpolation = cv2.INTER_LINEAR)

'''


import numpy as np
import cv2 as cv2
from matplotlib import pyplot as plt

MIN_MATCH_COUNT = 10


#img1 = cv2.imread('search-logo/4613.png',cv2.IMREAD_GRAYSCALE)          # queryImage
#img2 = cv2.imread('video-frame/46/frame132.jpg',0) # trainImage

img1 = cv2.imread('search-logo/187hig.png', cv2.IMREAD_GRAYSCALE)          # queryImage
img2 = cv2.imread('video-frame/49/frame217.jpg', cv2.IMREAD_GRAYSCALE) # trainImage


#img1 = cv2.imread('search-logo/354uhg.png',0)          # queryImage






# Initiate SIFT detector
#sift = cv2.SIFT()
sift = cv2.xfeatures2d.SIFT_create()

# find the keypoints and descriptors with SIFT
kp1, des1 = sift.detectAndCompute(img1,None)
kp2, des2 = sift.detectAndCompute(img2,None)

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





minHessian = 400
compare_points=29.2
detector = cv2.xfeatures2d_SURF.create(hessianThreshold=minHessian)
keypoints1, descriptors1 = detector.detectAndCompute(img1, None)
keypoints2, descriptors2 = detector.detectAndCompute(img2, None)					

matcher = cv2.DescriptorMatcher_create(cv2.DescriptorMatcher_FLANNBASED)					

knn_matches = matcher.knnMatch(descriptors2, descriptors1, 2)
#-- Filter matches using the Lowe's ratio test
ratio_thresh = 0.6
good_matches = []
for m,n in knn_matches:
	if m.distance < ratio_thresh * n.distance:
		good_matches.append(m)


number_keypoints = 0
if len(keypoints1) <= len(keypoints2):
	number_keypoints = len(keypoints1)
else:
	number_keypoints = len(keypoints2)

if(number_keypoints<=0):
	number_keypoints=1
#matched_points=len(good_points) / number_keypoints * 100
matched_points=len(good_matches) / number_keypoints * 100



print(len(good),matched_points)


