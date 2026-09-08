import os
#from flask_cors import CORS
from flask import request, jsonify, Flask
from dotenv import load_dotenv
from database import Db
load_dotenv('.env')
env = os.getenv('ENV')
app = Flask(__name__)
# getMainQuery
#CORS(app)
app.config["DEBUG"] = True
@app.route('/company_data', methods=['POST'])
def company_data():
    try:
        if request.method == "POST":
            data = request.get_json()
            #print(data)
            connObj = Db().get_connection_competiscan()
            companydata=data['companies']
            product_id=data['product_id']
            if ((len(companydata)>0) and (product_id!='')):
                chkmatchCompany=[]
                chknomatchCompany=[]
                for company in companydata:
                    #print(company)
                    companyQuery=f'select companyID,companyName from cscan_company Where companyName ="{company}"'
                    #print(companyQuery)
                    curs = connObj.cursor(buffered=True, dictionary=True)
                    curs.execute(companyQuery)
                    chkCompany = curs.fetchone()
                    if chkCompany is not None:
                        chkCompanyID = chkCompany['companyID']
                        chkCompanyName = chkCompany['companyName']
                        chkcompanyproductQuery=f'select companyID,productID,primary_co from cscan_company_product Where companyID ="{chkCompanyID}" AND productID="{product_id}" AND primary_co!=1'
                        curs = connObj.cursor(buffered=True, dictionary=True)
                        curs.execute(chkcompanyproductQuery)
                        chkCompanyProduct = curs.fetchone()
                        #print(chkCompanyProduct)
                        if chkCompanyProduct is None:
                            chkcompanyproductMaxQuery=f'select Max(primary_co) as primary_co  from cscan_company_product Where productID="{product_id}"'
                            curs = connObj.cursor(buffered=True, dictionary=True)
                            curs.execute(chkcompanyproductMaxQuery)
                            chkCompanyMaxProductSort = curs.fetchone()
                            #print(chkCompanyMaxProductSort)
                            if chkCompanyMaxProductSort is not None:
                                chkPrimaryCompanySort=chkCompanyMaxProductSort['primary_co']
                                #print(chkPrimaryCompanySort)
                                insert_query=f'Replace into cscan_company_product (companyID,productID,primary_co) VALUES ({chkCompanyID},{product_id},{chkPrimaryCompanySort+1});'
                                insert_data_values = (chkCompanyID,product_id,chkPrimaryCompanySort+1)
                                #connObj = Db().get_connection_competiscan()
                                curs = connObj.cursor(buffered=True, dictionary=True)
                                curs.execute(insert_query)
                                connObj.commit()
                                checkProductQuery=f'select secondCompany from cscan_product_detail Where productID={product_id}'
                                #print(companyQuery)
                                curs = connObj.cursor(buffered=True, dictionary=True)
                                curs.execute(checkProductQuery)
                                chkSecondCompany = curs.fetchone()
                                if chkSecondCompany is not None:
                                    chkSecondCompanyName=chkSecondCompany['secondCompany']
                                    if (chkSecondCompanyName!=''):
                                        secondCompanyUpdate=f'CONCAT(secondCompany, ", ","{chkCompanyName}")'
                                    else:
                                        secondCompanyUpdate=f'CONCAT(secondCompany, "","{chkCompanyName}")'
                                updateProduct = f'UPDATE cscan_product_detail SET secondCompany ={secondCompanyUpdate}  where productID={product_id}'
                                curs = connObj.cursor(buffered=True, dictionary=True)
                                curs.execute(updateProduct)
                                connObj.commit()
                        chkmatchCompany.append(chkCompanyName)           
                                
                    else:
                        product_id=product_id
                        companyName=company
                        chknomatchCompany.append(company)

                allmatchcompanylist=chkmatchCompany
                #print(chknomatchCompany)
                allnomatchcompanylist=chknomatchCompany
                if((len(allmatchcompanylist)>0) or (len(allnomatchcompanylist)>0)):
                    if (len(allnomatchcompanylist)>0):
                        nomatchcomp_string = ','.join(allnomatchcompanylist)
                    else:
                        nomatchcomp_string = ''   
                    if (len(allmatchcompanylist)>0):
                        matchcomp_string = ','.join(allmatchcompanylist)
                    else:
                        matchcomp_string = ''
                    if((len(allmatchcompanylist)>0) and (len(allnomatchcompanylist)>0)):
                        match_status=2 ####partial match
                    elif((len(allmatchcompanylist)<1) and (len(allnomatchcompanylist)>1)):
                        match_status=0 ####no match
                    elif((len(allmatchcompanylist)>0) and (len(allnomatchcompanylist)<1)):
                        match_status=1 #### match company
                    insertquerycompany=f'insert into cscan_csv2_annotation_company (productID,match_company,no_match_company,status) VALUES ({product_id},"{matchcomp_string}","{nomatchcomp_string}","{match_status}");'
                    #print(insertquerycompany)
                    curs = connObj.cursor(buffered=True, dictionary=True)
                    curs.execute(insertquerycompany)
                    connObj.commit()
                    response = {"msg": "Additonal company updated successfully!", "status": 200}
                return jsonify(response)
                
            else:
                #msg='No additional company found!.'
                response = {"msg": "No additional company found!", "status": 404}
                return jsonify(response)
        else:
            response = {"msg": "Company not found!", "status": 404}
            return jsonify(response)
           
    except Exception as e:
        print(e)
    #return jsonify([msg])
app.run(host=os.getenv('FLASK_RUN_HOST'), port=os.getenv('FLASK_RUN_PORT'), debug=os.getenv('FLASK_DEBUG'))
