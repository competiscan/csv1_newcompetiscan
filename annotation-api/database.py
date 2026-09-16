import mysql.connector
import os
from dotenv import load_dotenv
load_dotenv('.env')
env = os.getenv('ENV')

class Db:
    
    def __init__(self):
        self.conn_competi  = None
        self.conn_spacy  = None
        self.conn = None
        self.__cs_conn=None
        self.conn_comp = None
        #print(os.environ)
        #exit("OKKKKKKKKKKKKKk")
        self.COMPETI_HOST_NAME = os.getenv('LOCAL_HOSTNAME')
        self.DATABASE_LOCAL = os.getenv('LOCAL_COMPETIDB')
        self.COMPETI_USER = os.getenv('LOCAL_USER')
        self.COMPETI_PASSWORD = os.getenv('LOCAL_PASSWORD')
        #self.COMPETI_HOST_NAME = os.environ['LOCAL_HOSTNAME']
        #self.COMPETI_DATABASE = os.environ['LOCAL_COMPETIDB']
        #self.COMPETI_USER = os.environ['LOCAL_USER']
        #self.COMPETI_PASSWORD = os.environ['LOCAL_PASSWORD'] 

        #print(self.COMPETI_HOST_NAME, self.COMPETI_DATABASE,self.COMPETI_USER,self.COMPETI_PASSWORD)
        #exit("OKKKKK")
    # for competiscan localdb
    def get_connection_competiscan(self):
        #print(self.conn_competi)
        if self.conn_competi is None:
            try:
                self.conn_competi = mysql.connector.connect(
                    host=self.COMPETI_HOST_NAME,
                    database=self.DATABASE_LOCAL,
                    user=self.COMPETI_USER,
                    password=self.COMPETI_PASSWORD,
                    auth_plugin='mysql_native_password'
                )
            except Exception as e:
                print(str(e))
        return self.conn_competi

    
    
    

    
    
    

