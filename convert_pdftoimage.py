#print("Hello World!")
import sys
from pdf2image import convert_from_path
path=sys.argv[1]
name=sys.argv[2]
productID=sys.argv[3]
document_id=sys.argv[4]
full_path=str(path)+str(name)
#print(full_path)
pages = convert_from_path(full_path, size=(400,400))
i=0
for page in pages:
    #print('process....')
    filename=(productID+'-'+str(i)+'.jpg')
    page.save(path+filename, 'JPEG')
    #print('image save...')
    i=i+1
