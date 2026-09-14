from __future__ import unicode_literals
import pysftp
import paramiko
import sys
from dbconfig import MySQLHost
import os
connectionObj = MySQLHost()

if not os.path.exists("digitalrecordsftp"):
	os.mkdir("digitalrecordsftp")
download_file_path='digitalrecordsftp'
remote_file_directory='/uploads'

HostnameFTP = "52.73.94.198"
UsernameFTP = "biscience"
PasswordFTP = "O56lqMx0CB2wHyvF"

class My_Connection(pysftp.Connection):
    def __init__(self, *args, **kwargs):
        self._sftp_live = False
        self._transport = None
        super().__init__(*args, **kwargs)

#with pysftp.Connection(host=HostnameFTP, username=UsernameFTP, password=PasswordFTP) as sftp:
with My_Connection(host=HostnameFTP, username=UsernameFTP, password=PasswordFTP) as sftp:
    print ("Connection succesfully stablished ... ")

    # Switch to a remote directory
    sftp.cwd(remote_file_directory)

    # Obtain structure of the remote directory '/var/www/vhosts'
    directory_structure = sftp.listdir_attr()
    #print(directory_structure)

    # Print data
    loopcount=0
    for attr in directory_structure:
        
        print (attr.filename, attr)
        remoteFilePath = remote_file_directory+'/'+attr.filename
        localFilePath = download_file_path+'/'+attr.filename
        name, ext = os.path.splitext(attr.filename)
        ext=ext.lower()        
        
        url_query='SELECT id FROM cscan_digital_files where file_name=%s'
        url_records = connectionObj.select(url_query,(str(attr.filename),),())
        if (url_records is None) and (ext=='.csv'):
            sftp.get(remoteFilePath, localFilePath)
            file_size=os.path.getsize(localFilePath)
            
            print(attr.filename,'biscienceFTP/'+download_file_path,file_size)
            
            insert_query="Insert into cscan_digital_files (file_name,file_path,filesize_in_byte) VALUES (%s, %s, %s) "
            data = (attr.filename,'biscienceFTP/'+download_file_path,file_size,)
            connectionObj.execute(insert_query,data)
            sftp.remove(remoteFilePath)
        else:
            insert_query="Insert into cscan_digital_files_rejected (file_name,file_path) VALUES (%s, %s) "
            data = (attr.filename,'biscienceFTP/'+download_file_path,)
            connectionObj.execute(insert_query,data)
        loopcount=loopcount+1
        if loopcount==10:
            exit()
print("Completed")		
	

