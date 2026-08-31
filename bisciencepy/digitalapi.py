from ast import Not
from flask_cors import CORS
from flask import request, jsonify, Flask
import pandas as pd
from zipfile import ZipFile
import time
import os
from datetime import datetime
from dbconfig import MySQLHost
connectionObj = MySQLHost()
now = datetime.now()
curr_date = now.strftime("%d-%m-%Y")
if not os.path.exists("digitaldata"):
    os.mkdir("digitaldata")
foldername = 'digitaldata'
zipfile_name = '/competiscan_digital_record_'
fullpath = foldername+zipfile_name

global geo_data
app = Flask(__name__)
# getMainQuery


def getMainQuery(query):
    try:
        respQuery = connectionObj.select(query)
        resultQuery = pd.DataFrame(respQuery)
        return resultQuery
    except Exception as e:
        print(e)


def getAllJoinQuery(from_date, to_date, digital_source, media_channel, digital_company):
    try:
        #print('FROM DATE========',from_date)
        #print('TO DATE========',to_date)
        #print('DS========',digital_source)
        #print('Media CHAnnel=======',media_channel)
        #print('DIGITAL COMP======',type(digital_company))
        
        if len(digital_source[0])<=0 :
            addQueryDS=''
            
        else :
            ds_ids = ",".join(digital_source)
            addQueryDS=f" And ps.digital_source In ({ds_ids})"

        if len(media_channel[0])>0 :
            mchannel_ids = ",".join(media_channel)
            addQueryMchannel=f" And pmc.mchannel_id In ({mchannel_ids})"
        else :
            addQueryMchannel=''

        if len(digital_company)>0 :
            #print(digital_company)
            list_compnay = digital_company.split(' or ')
            #print(list_compnay)
            fcompany=[x.strip('"') for x in list_compnay]
            #print(fcompany)
            comp_ids=[]
            for company in fcompany:
                companyQuery=f'select companyID from cscan_company Where companyName ="{company}"'
                respCompanyQuery = connectionObj.select(companyQuery)
                select_company_data=respCompanyQuery[0]['companyID']
                comp_ids.append(select_company_data)
            digitalcomp_ids=str(list(comp_ids))[1:-1]
            addQueryCompany=f" And pr.company_id In ({digitalcomp_ids})"
        else :
            addQueryCompany=''

        query = f'SELECT pr.id as id ,c.companyName as Company, (CASE WHEN pmc.mchannel_id = 5 THEN "Online Display" WHEN pmc.mchannel_id = 10 THEN "Online Video" END) AS "Media Channel", creation_date as Date, GROUP_CONCAT( DISTINCT pt.compaign_title SEPARATOR " ; ") AS Headline,pr.campaign_landing_page as "Campaign Landing Page",pr.creative_wrapper as Creative,GROUP_CONCAT(DISTINCT pb.publisher SEPARATOR " ; ") AS Publisher,GROUP_CONCAT(DISTINCT CONCAT(CASE ps.digital_source WHEN 1 THEN "Desktop" WHEN 2 THEN "Mobile" WHEN 3 THEN "In App Android" WHEN 4 THEN "In App Ios" WHEN 5 THEN "Social" END) SEPARATOR "; ") AS "Digital Source",pr.spend as Spend,pr.impressions as Impressions FROM cscan_digital_processed_records pr LEFT JOIN cscan_company c ON(c.companyID = pr.company_id) LEFT JOIN cscan_digital_processed_mchannel pmc ON (pmc.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_publisher pb ON (pb.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_source ps ON (ps.processed_record_id = pr.id) LEFT JOIN cscan_digital_processed_title pt ON (pt.processed_record_id = pr.id) WHERE creation_date >="{from_date}" AND creation_date <="{to_date}" {addQueryCompany} {addQueryDS} {addQueryMchannel} GROUP BY pr.id  ORDER BY pr.id DESC'
        #print(query) 
        #exit("OKKKKKKKKKKKKKKKK")
        selectData = getMainQuery(query)
        selectData['Creative'] = selectData['Creative'].str.replace(
            "http://biscience.s3.amazonaws.com", "https://files2.competiscan.com")
        selectData['Creative'] = selectData['Creative'].str.replace(
            "https://biscience.s3.amazonaws.com", "https://files2.competiscan.com")
        return selectData
    except Exception as e:
        print('Fetch Main Query DATA:'+str(e))
        return pd.DataFrame()



def data_df(i):
    global geo_data
    city = "; ".join(i.city.unique())
    state = "; ".join(i.state_province.unique())
    country = "; ".join(i.country.unique())
    data = {"processed_record_id": i['processed_record_id'].iloc[0],
            "City": city, "State": state, "Country": country}
    geo_data.append(data)
    return True

def getAllJoinLocationQuery(process_ids,digital_city,digital_state,digital_country):
    try:
        if len(digital_city[0])<=0 :
            addQueryCity=''
            addQueryCityStateCountry='';
        else :
            digital_citys = str(list(digital_city))[1:-1]
            addQueryCity=f" And location In ({digital_citys})"
            addQueryCityStateCountry=f" And city In ({digital_citys})"

        if len(digital_state[0])<=0 :
            addQueryState=''
            addQueryState_code=''
        else :
            digital_states = str(list(digital_state))[1:-1]
            addQueryState=f" And location_state_code In ({digital_states})"
            addQueryState_code=f" And state_code In ({digital_states})"
        
        if len(digital_country)<=0 :
            addQueryCountry=''
        else :
            digital_countrys = digital_country
            addQueryCountry=f" Where country = '{digital_countrys}'"
        
        queryLocation = f"select processed_record_id,SUBSTRING_INDEX(location, ',', 1)  AS city,location_state_code as state_code from cscan_digital_processed_location where processed_record_id In ({process_ids}) {addQueryState} "
        #print(queryLocation)
        #exit("OKKKKKKKKKKKKKKKKKKKKKKK")
        selectLocation = getMainQuery(queryLocation)
        queryCityState = "select city,state_province,state_code,country from cscan_digital_city_state"
        #queryCityState = f"select city,state_province,state_code,country from cscan_digital_city_state {addQueryCountry} {addQueryCityStateCountry} {addQueryState_code}"
        #print(queryCityState)
        #exit("OKKKKKKKKKKKKKKKKKKKKKKKK")
        selectCityState = getMainQuery(queryCityState)
        newMergelocationDF = pd.merge(selectLocation, selectCityState, how='left', on=[
                                      'city', 'state_code'], suffixes=('_left', '_right'))
        newMergelocationDF = newMergelocationDF.dropna()
        newMergelocationDF['state_province'] = newMergelocationDF['state_province'].astype(
            str)
        newMergelocationDF['city'] = newMergelocationDF['city'].astype(str)
        newMergelocationDF['country'] = newMergelocationDF['country'].astype(
            str)
        global geo_data
        geo_data = []
        grouped_df = newMergelocationDF.groupby(
            "processed_record_id", as_index=True).apply(data_df)
        newMergefilterDF = pd.DataFrame(geo_data)
        return newMergefilterDF

    except Exception as e:
        print('Fetch Location and state Query DATA:'+str(e))
        return pd.DataFrame()


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
            dfs.append(df.iloc[start: count])
        return dfs
    except Exception as e:
        print('Split data frame DATA:'+str(e))


def zip_directory(folder_path, zip_path, zipObj):
    try:
        for root, _, files in os.walk(folder_path):
            for files_name in files:
                file_path = os.path.join(root, files_name)
                if '.csv' in files_name:
                    zipObj.write(file_path, files_name)
                if '.csv' in files_name:
                    os.remove(file_path)
                # final_zip=''
                if '.zip' in files_name:
                    final_zip = files_name
        return final_zip
    except Exception as e:
        print('All csv file convert into Zip file:'+str(e))


CORS(app)
app.config["DEBUG"] = True


@app.route('/export_data', methods=['POST', 'GET'])
def export_data():
    try:
        data = request.get_json()
        #print(data)
        if request.method == "POST":
            #  from_date="2021-01-01"
            #  to_date="2021-01-31"
            from_date = data["from_date"]
            to_date = data['to_date']
            digital_source = data["digital_source"]
            media_channel = data["mchannel"]
            digital_company = data["company"]
            digital_city = data["city"]
            digital_state = data["state"]
            digital_country = data["country"]
            if from_date == "" or to_date == "":
                print("date empty")
                return jsonify([])
            else:
                start = time.time()
                selectData = getAllJoinQuery(from_date, to_date, digital_source, media_channel, digital_company)
                if selectData.empty:
                    print("selectData empty")
                    return jsonify([])
                process_ids = str(list(selectData['id']))[1:-1]
                newMergefilterDF = getAllJoinLocationQuery(process_ids,digital_city,digital_state,digital_country)
                if newMergefilterDF.empty:
                    print("newMergefilterDF empty")
                    return jsonify([])
                full_df = pd.merge(selectData, newMergefilterDF, how='left', left_on='id',
                                right_on='processed_record_id').drop(columns=['id', 'processed_record_id'])

                final_dataframe = full_df[['Company', 'Media Channel', 'Date',
                            'Country', 'State', 'City','Headline','Campaign Landing Page','Creative','Publisher','Digital Source','Spend','Impressions']]
                num_row = 200000
                split_df_to_chunks = split_dataframe_to_chunks(final_dataframe, num_row)

                for i, fulldataframe in enumerate(split_df_to_chunks):
                    fulldataframe.to_csv(fullpath + str(i+1)+'.csv', index=False)
                zipObj = ZipFile(fullpath + curr_date+'.zip', 'w')
                finalzipfile = zip_directory(foldername, fullpath, zipObj)
                #print("Final DF convert Into ZIP FILE:", time.time()-start)
                return finalzipfile

    except Exception as e:
        print(e)
    print("ALL EMPTY")
    return jsonify([])


#app.run(debug=True)
app.run(host='0.0.0.0', debug=True)
#cProfile.run('app.run(debug=True)', sort=1)
