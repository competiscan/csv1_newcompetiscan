import tika
import re
from tika import parser
import mysql.connector
#import webbrowser
import csv
#import os
import time
#cnx = mysql.connector.connect(host='10.0.0.19',user='root', password='root@20165', database='competi_competidb')
cnx = mysql.connector.connect(host='34.226.25.177',user='app_writeuser', password='Ano@11SDFLH@13NMldrf', database='competi_competidb')
cursor = cnx.cursor(buffered=True)
cursor.execute("SELECT * FROM cscan_trend_report where trend_link IS NOT NULL LIMIT 0,2")
myresult = cursor.fetchall()
for row in myresult:
	trend_id=row[0]
	trend_link =row[3]
	parsed = parser.from_file(trend_link)	
	#print(parsed["content"]) # To get the content of the file
	data_text=parsed['content']; # To get the content of the file
	output_text = re.sub("[^a-zA-Z0-9 ]", '', data_text).strip()
	#print(out)	
	Query ="SELECT * FROM cscan_trend_document_text where trend_id='"+str(trend_id)+"'"
	cursor.execute(Query)
	#print("Rows returned = ",cursor.rowcount)
	#print("SELECT trend_id FROM cscan_trend_document_text where trend_id='"+str(trend_id)+"'")
	#print("count_row",)
	if cursor.rowcount < 1:
	   	#deleteQuery="DELETE FROM cscan_trend_document_text WHERE trend_id='"+str(trend_id)+"'"
	   	#cursor.execute(deleteQuery)
        	#cnx.commit()
		sql = "INSERT cscan_trend_document_text SET trend_id='"+str(trend_id)+"',document_text='"+(output_text)+"'"
		cursor.execute(sql)
		cnx.commit()
print("Suceess")
#parsed = parser.from_file('https://files.competiscan.com/fileuploads/53236Alert_SSQ_LaCapitale.pdf')
#print(parsed["metadata"]) #To get the meta data of the file
#print(parsed["content"]) # To get the content of the file
#data_text=parsed['content'];
#out = re.sub("[^a-zA-Z0-9\s\-\.\n]", '', data_text).strip()
#out = re.sub("[^a-zA-Z0-9 ]", '', data_text).strip()
#print(out)
