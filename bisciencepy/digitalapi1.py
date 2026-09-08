from flask_cors import CORS
from flask import request, jsonify, Flask, send_file
import pandas as pd
import glob
import zipfile
from zipfile import ZipFile
import time
import os
from os import listdir
from datetime import datetime
from dbconfig import MySQLHost
import re
import numpy as np
connectionObj = MySQLHost()
now = datetime.now()
curr_date = now.strftime("%d-%m-%Y")
if not os.path.exists("digitaldata"):
	os.mkdir("digitaldata")
foldername='digitaldata'
zipfile_name='/competiscan_digital_record_'
fullpath=foldername+zipfile_name

app = Flask(__name__)
## getMainQuery
def getMainQuery(query):
    try:
        respQuery=connectionObj.select(query)
        resultQuery=pd.DataFrame(respQuery)
        return resultQuery
    except Exception as e:
        print(e)

def getAllJoinQuery(from_date,to_date):
    #query='SELECT pr.id as id ,c.companyName as Company,(CASE WHEN pmc.mchannel_id = 5 THEN "Online Display" WHEN pmc.mchannel_id = 10 THEN "Online Video" END) AS "Media Channel", creation_date as Date, GROUP_CONCAT( DISTINCT pt.compaign_title SEPARATOR " ; ") AS Headline,pr.campaign_landing_page as "Campaign Landing Page",pr.creative_wrapper as Creative,GROUP_CONCAT(DISTINCT pb.publisher SEPARATOR " ; ") AS Publiser,GROUP_CONCAT(DISTINCT CONCAT(CASE ps.digital_source WHEN 1 THEN "Desktop" WHEN 2 THEN "Mobile" WHEN 3 THEN "In App Android" WHEN 4 THEN "In App Ios" WHEN 5 THEN "Social" END) SEPARATOR "; ") AS "Digital Source",pr.spend as Spend,pr.impressions as Impressions FROM cscan_digital_processed_records pr LEFT JOIN cscan_company c ON(c.companyID = pr.company_id) LEFT JOIN cscan_digital_processed_location pl ON (pl.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_mchannel pmc ON (pmc.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_publisher pb ON (pb.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_source ps ON (ps.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_title pt ON (pt.processed_record_id = pr.id) WHERE creation_date >= "2021-01-01" AND creation_date <= "2021-01-31" GROUP BY pr.id'
    #query=f'SELECT pr.id as id ,c.companyName as Company, (CASE WHEN pmc.mchannel_id = 5 THEN "Online Display" WHEN pmc.mchannel_id = 10 THEN "Online Video" END) AS "Media Channel", creation_date as Date, GROUP_CONCAT( DISTINCT pt.compaign_title SEPARATOR " ; ") AS Headline,pr.campaign_landing_page as "Campaign Landing Page",pr.creative_wrapper as Creative,GROUP_CONCAT(DISTINCT pb.publisher SEPARATOR " ; ") AS Publiser,GROUP_CONCAT(DISTINCT CONCAT(CASE ps.digital_source WHEN 1 THEN "Desktop" WHEN 2 THEN "Mobile" WHEN 3 THEN "In App Android" WHEN 4 THEN "In App Ios" WHEN 5 THEN "Social" END) SEPARATOR "; ") AS "Digital Source",pr.spend as Spend,pr.impressions as Impressions FROM cscan_digital_processed_records pr LEFT JOIN cscan_company c ON(c.companyID = pr.company_id) LEFT JOIN cscan_digital_processed_location pl ON (pl.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_mchannel pmc ON (pmc.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_publisher pb ON (pb.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_source ps ON (ps.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_title pt ON (pt.processed_record_id = pr.id) WHERE creation_date >="{from_date}" AND creation_date <="{to_date}" GROUP BY pr.id'
    try:
        query=f'SELECT pr.id as id ,c.companyName as Company, (CASE WHEN pmc.mchannel_id = 5 THEN "Online Display" WHEN pmc.mchannel_id = 10 THEN "Online Video" END) AS "Media Channel", creation_date as Date, GROUP_CONCAT( DISTINCT pt.compaign_title SEPARATOR " ; ") AS Headline,pr.campaign_landing_page as "Campaign Landing Page",pr.creative_wrapper as Creative,GROUP_CONCAT(DISTINCT pb.publisher SEPARATOR " ; ") AS Publiser,GROUP_CONCAT(DISTINCT CONCAT(CASE ps.digital_source WHEN 1 THEN "Desktop" WHEN 2 THEN "Mobile" WHEN 3 THEN "In App Android" WHEN 4 THEN "In App Ios" WHEN 5 THEN "Social" END) SEPARATOR "; ") AS "Digital Source",pr.spend as Spend,pr.impressions as Impressions FROM cscan_digital_processed_records pr LEFT JOIN cscan_company c ON(c.companyID = pr.company_id) LEFT JOIN cscan_digital_processed_mchannel pmc ON (pmc.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_publisher pb ON (pb.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_source ps ON (ps.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_title pt ON (pt.processed_record_id = pr.id) WHERE creation_date >="{from_date}" AND creation_date <="{to_date}" GROUP BY pr.id'
        print(query)
        start = time.time()
        selectData=getMainQuery(query)
        print("select main query", time.time()-start)
        selectData['Creative'] = selectData['Creative'].str.replace("http://biscience.s3.amazonaws.com", "https://files2.competiscan.com")
        selectData['Creative'] = selectData['Creative'].str.replace("https://biscience.s3.amazonaws.com", "https://files2.competiscan.com")
        #print(selectData)
        return selectData
    except Exception as e:
        print('Fetch Main Query DATA:'+str(e))

def getAllJoinLocationQuery(process_ids):
    try:
        queryLocation=f"select processed_record_id,SUBSTRING_INDEX(location, ',', 1)  AS city,location_state_code as state_code from cscan_digital_processed_location where processed_record_id In ({process_ids})"
        print(queryLocation)
        start = time.time()
        selectLocation=getMainQuery(queryLocation)
        print("selectLocation query", time.time()-start)
        queryCityState="select city,state_province,state_code,country from cscan_digital_city_state"
        print(queryCityState)
        start = time.time()
        selectCityState=getMainQuery(queryCityState)
        print("time took in selectCityState query", time.time()-start)
        start = time.time()
        newMergelocationDF = pd.merge(selectLocation, selectCityState, how='left', on=['city','state_code'],suffixes=('_left', '_right'))
        print("time took in merging location and state DF:", time.time()-start)
        grouped_df = newMergelocationDF.groupby("processed_record_id", as_index=True)
        geo_data = []
        start = time.time()
        for i in grouped_df:
            city = "; ".join(i[1]['city'].to_list())
            state_province = i[1]['state_province'].dropna()
            state = "; ".join(list(set(state_province.to_list())))
            country = i[1]['country'].dropna()
            country = "; ".join(list(set(country.to_list())))
            data  = {"processed_record_id" : i[1]['processed_record_id'].iloc[0] , "City" : city,"State" : state,"Country": country}
            geo_data.append(data)
            newMergefilterDF=pd.DataFrame(geo_data)

        print("time took in grouping DF:", time.time()-start)
        return newMergefilterDF
    except Exception as e:
        print('Fetch Location and state Query DATA:'+str(e))

def split_dataframe_to_chunks(df, n):
    try:
        df_len = len(df)
        count = 0
        dfs = []
        while True:
            if count > df_len-1:
                break

            start = count
            count += n
            #print("%s : %s" % (start, count))
            dfs.append(df.iloc[start : count])
        return dfs
    except Exception as e:
        print('Split data frame DATA:'+str(e))   

def zip_directory(folder_path,zip_path,zipObj):
    try:
        for root, _, files in os.walk(folder_path):
            for files_name in files:
                file_path = os.path.join(root, files_name)
                if '.csv' in files_name:
                    zipObj.write(file_path, files_name)
                if '.csv' in files_name:
                    os.remove(file_path)
                #final_zip=''
                if '.zip' in files_name:
                    final_zip=files_name
        return final_zip    
    except Exception as e:
       print('All csv file convert into Zip file:'+str(e)) 


CORS(app)
app.config["DEBUG"] = True
@app.route('/export_data1', methods=['POST', 'GET'])
def export_data():
    data = request.get_json()
    #return data
    try:
        if request.method == "POST":
             #from_date="2021-01-01"
             #to_date="2021-01-31"
             from_date=data["from_date"]
             to_date = data['to_date']
             if from_date=="" and to_date=="":
                 return jsonify([])
             else:
                selectData=getAllJoinQuery(from_date,to_date)
                #print(timer(getAllJoinQuery))
                process_ids=str(list(selectData['id']))[1:-1]
                newMergefilterDF=getAllJoinLocationQuery(process_ids)
                start = time.time()
                full_df=pd.merge(selectData,newMergefilterDF,how='left',left_on='id',right_on='processed_record_id').drop(columns = ['id','processed_record_id'])
                print("Final total memory usage dataframe:",full_df.memory_usage())
                Country_column = full_df.pop('Country')
                State_column = full_df.pop('State')  
                City_column = full_df.pop('City')
                full_df.insert(3, 'Country', Country_column)
                full_df.insert(4, 'State', State_column)
                full_df.insert(5, 'City', City_column)
                print("Final merge all dataframe:", time.time()-start)
                 #full_df.to_csv("digitaldata/report.csv",index=False)
                
                #for _ in range (9):
                    #full_df= full_df.append(full_df, ignore_index=True)
                #print(full_df)
                num_row=200000
                start = time.time()
                split_df_to_chunks = split_dataframe_to_chunks(full_df, num_row)
                zipObj = ZipFile(fullpath+curr_date+'.zip', 'w')
                for i, fulldataframe in enumerate(split_df_to_chunks):
                #print(full_df)
                    fulldataframe.to_csv(fullpath+str(i+1)+'.csv',index=False)
                finalzipfile=zip_directory(foldername,fullpath,zipObj)
                print("Final DF convert Into ZIP FILE:", time.time()-start)
                return finalzipfile   

        return ''     
    except Exception as e:
            print(e)
    return ''
app.run(debug=True)
#app.run(host='0.0.0.0', debug=True)
