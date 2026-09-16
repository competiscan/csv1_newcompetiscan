from pdf2image import convert_from_path
import sys
import os
from os import listdir 
import subprocess 
import glob  
from PIL import Image
Image.MAX_IMAGE_PIXELS = 1000000000
import math 
import random 
path=sys.argv[1]
name=sys.argv[2]
productID=sys.argv[3]
document_id=sys.argv[4]
dops=sys.argv[5]
full_path=str(path)+str(name)
USE_CROPBOX = True
DPI = 200
FORMAT = 'jpg'
#dops=2;
RandNum=random.randint(100, 10000)
imagearray=[]
if int(dops)==2:
    rsize=(300,None)
else:
    try:
        pages1 = convert_from_path(full_path,dpi=DPI,single_file=True,fmt=FORMAT,use_cropbox=USE_CROPBOX,size=None)
        j=1
        for pg in pages1:
            file_name=(productID+'_'+str(RandNum)+'_'+str(j)+'.jpg')
            pg.save(path+file_name, 'JPEG')
            for file in os.listdir(path):
                if file_name in file:
                    img = Image.open(path+file)
                    width, height = img.size		
        j=j+1
        if(width > height):
            rsize=(300,None)
            os.remove(path+file_name)
        else:
            rsize=(None,400)
            os.remove(path+file_name)
    except Exception as e:							
            print('There are some issue in pdf ', e)
try:
    pages = convert_from_path(full_path,dpi=DPI,fmt=FORMAT,use_cropbox=USE_CROPBOX,size=rsize)
    i=1
    imagearray=[]
    for page in pages:
        filename=(productID+'_'+str(RandNum)+'_'+str(i)+'.jpg')
        imagearray.append(filename)
        page.save(path+filename, 'JPEG')
        #if int(dops)==2:
        for file in os.listdir(path):
            if filename in file:
                img = Image.open(path+file)
                width, height = img.size
                #print(width, height)
                upper = 0
                left = 0
                slice_size=400
                if int(dops)==2:
                    if(height >500):
                        slices = int(math.ceil(height/slice_size))
                        count = 1
                        for slice in range(slices):
                            if count == slices:
                                lower = height
                            else:
                                lower = int(count * slice_size)
                            #set the bounding box! The important bit     
                            bbox = (left, upper, width, lower)
                            working_slice = img.crop(bbox)
                            upper += slice_size
                            #save the slice
                            if(count==1):
                                working_slice.save(path+file, 'JPEG', optimize=True,quality=90)
                                if file not in imagearray:
                                    imagearray.append(file)
                            else:
                                filename1=(os.path.splitext(path+file)[0]+"_" + str(count)+".jpg")
                                filename2=os.path.splitext(file)[0] +"_" + str(count)+".jpg"
                                working_slice.save(os.path.join(filename1),optimize=True,quality=90)
                                if filename2 not in imagearray:
                                    imagearray.append(os.path.splitext(file)[0] +"_" + str(count)+".jpg")
                            count +=1
                else:
                    if (width > 400):
                        new_img=img.resize((400,400),Image.ANTIALIAS)
                        new_img.save(path+file, 'JPEG', optimize=True,quality=90)
                    if (height > 500):
                        new_img=img.resize((300,400),Image.ANTIALIAS)
                        new_img.save(path+file, 'JPEG', optimize=True,quality=90)
        if filename not in imagearray:
            imagearray.append(filename)
        i=i+1
    print(imagearray)
except Exception as e:							
        print('There are some issue in pdf2 ', e)