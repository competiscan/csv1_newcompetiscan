import os
import sys
import mysql.connector
from mysql.connector import Error
import socket
hostname=socket.gethostname()
host_ip = socket.gethostbyname(hostname)
if(host_ip=='127.0.1.1'):
    HOST_NAME = '10.0.0.19'
    DATABASE = 'competi_competidb'
    USER = 'root'
    PASSWORD = 'root@20165'
else:
    HOST_NAME = '34.224.108.197'
    DATABASE = 'competi_competidb'
    USER = 'app_readuser'
    PASSWORD = 'Ano@11SDFLH@13NMldrf'

class MySQLHost:
    connection = None
    cursor = None

    def get_connection(self):
        if self.connection is None:
            try:
                connection = mysql.connector.connect(host=HOST_NAME,database=DATABASE,user=USER,password=PASSWORD)
                if connection.is_connected():
                    self.connection = connection
                    return self.connection
            except Error as e:
                print("Error while connecting to MySQL", e)
        else:
            return self.connection

    def select(self, query, args=None, recordFlag=False):
        if self.get_cursor() is not None:
            if args is None:
                args = ()
            try:
                self.cursor.execute(query, args)
                if self.cursor.rowcount > 0:
                    if recordFlag:
                        record = self.cursor.fetchone()
                        self.close()
                        return record

                    if self.cursor.rowcount > 0:
                        records = self.cursor.fetchall()
                        self.close()
                        return records

            except Error as e:
                try:
                    print("MySQL Error [%d]: %s" % (e.args[0], e.args[1]))
                except IndexError:
                    print("MySQL Error: %s" % str(e))
            else:
                return None

    def insert(self, query, args=None):
        if self.get_cursor() is not None:
            if args is None:
                args = ()
            self.cursor.execute(query, args)
            self.connection.commit()
            lastrowid = self.cursor.lastrowid
            self.close()
            return lastrowid


    def execute(self, query, args=None):
        if self.get_cursor() is not None:
            if args is None:
                args = ()
            self.cursor.execute(query, args)
            self.connection.commit()
            self.close()


    def close(self):
        if self.get_connection() is not None:
            self.connection.close()
            self.connection = None
            self.cursor = None

    def get_cursor(self):
        if self.cursor is None:
            if self.get_connection() is not None:
                self.cursor = self.connection.cursor(buffered=True, dictionary=True)
                return self.cursor
            else:
                return None
        else:
            return self.cursor
           

