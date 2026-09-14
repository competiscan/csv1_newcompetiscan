from __future__ import division
#from pdf2image import convert_from_path
import sys
import ghostscript
import locale
import os
import glob  
from PIL import Image 
import math
import random
import ntpath
path=sys.argv[1]
name=sys.argv[2]
productID=sys.argv[3]
document_id=sys.argv[4]
dops=sys.argv[5]
full_path=str(path)+str(name)
#print(full_path)
#exit()
filename=(productID+'_'+str(random.randint(100, 10000))+'_%03d.jpg')
def pdf2jpeg(pdf_input_path, jpeg_output_path):
    args = ["pdftojpg", # actual value doesn't matter
            "-dNOPAUSE",
            "-dQUIET",
            "-sDEVICE=jpeg",
            "-dPrinted=false",
            "-dUseMediaBox",
            "-dUseCropBox",
            "-r45",
            "-sOutputFile=" + jpeg_output_path,
            pdf_input_path]

    encoding = locale.getpreferredencoding()
    args = [a.encode(encoding) for a in args]
    ghostscript.Ghostscript(*args)

pdf2jpeg(full_path,path+filename,)
files = []
# r=root, d=directories, f = files
for r, d, f in os.walk(path):
    for file in f:
        if '.jpg' in file:
            files.append(os.path.join(r, file))
imagearray=[]
if int(dops)==2:						
    for f in files:	
        img = Image.open(f)
        #imagearray.append(f)
        width, height = img.size
        upper = 0
        left = 0
        slice_size=400
        if(height >600):
            slices = int(math.ceil(height/slice_size))
        count = 1
        for slice in range(slices):
            #if we are at the end, set the lower bound to be the bottom of the image
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
                working_slice.save(f, 'JPEG', optimize=True,quality=90)
            else:
                filename=(os.path.splitext(f)[0]+"_" + str(count)+".jpg")
                #imagearray.append(filename)
                working_slice.save(os.path.join(filename),optimize=True,quality=90)
                #working_slice.save(os.path.join(path, filename),optimize=True,quality=90)
            count +=1
for file_name in sorted(glob.glob(path+'*.jpg')):
    head, tail = ntpath.split(file_name)
    imagearray.append(tail)
print(imagearray)

   