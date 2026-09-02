import mysql.connector
from mysql.connector import Error
from datetime import datetime
from dateutil.relativedelta import relativedelta

def close_connection(connection):
    if connection.is_connected():
        connection.close()
        
def connect_to_db(host, user, password, database):
    try:
        connection = mysql.connector.connect(
            host=host,
            user=user,
            password=password,
            database=database
        )
        return connection
    except Error as e:
        print(f"Error connecting to database: {e}")
        return None

def execute_query(connection, query, data=None, fetch=False):
    try:
        with connection.cursor() as cursor:
            cursor.execute(query, data) if data else cursor.execute(query)
            if fetch:
                return cursor.fetchall()
            connection.commit()
            return cursor.lastrowid
    except Error as e:
        print(f"Error executing query: {e}")
        return None

def process_record(record, connection, img_extensions, vid_extensions):
    # Unpack and process record
    (record_id, creation_date, location, channel, advertiser_name, compaign_title,
     creative_wrapper, publisher, impressions, spend, monitored_page, file_id,
     advertiser_domain, campaign_landing_page) = map(str.strip, record)

    # Date calculations
    creation_date = datetime.strptime(creation_date, "%Y-%m-%d")
    start_date = creation_date.replace(day=1)
    end_date = creation_date + relativedelta(months=1, days=-1)

    # Location and channel handling
    location_state_code = location.split(",")[-1].strip() if "," in location else location
    file_ext = creative_wrapper.split(".")[-1].lower()

    # Media Channel ID
    mChannelID = 5 if file_ext in img_extensions or "display" in channel.lower() else \
                 10 if file_ext in vid_extensions or "video" in channel.lower() else 0

    # Digital Source
    digital_source = (
        2 if "mobile" in channel.lower() else
        3 if "in app android" in channel.lower() else
        4 if "in app ios" in channel.lower() else
        5 if "social" in channel.lower() else 1
    )

    # Check for duplicates
    check_dup_query = """
    SELECT id, spend, impressions FROM cscan_digital_processed_records 
    WHERE creative_wrapper = %s AND creation_date BETWEEN %s AND %s LIMIT 1
    """
    duplicate = execute_query(connection, check_dup_query, (creative_wrapper, start_date, end_date), fetch=True)

    if duplicate:
        dup_id, old_spend, old_impressions = duplicate[0]
        new_spend = float(old_spend) + float(spend)
        new_impressions = int(old_impressions) + int(impressions)

        update_query = """
        UPDATE cscan_digital_processed_records 
        SET spend = %s, impressions = %s WHERE id = %s
        """
        execute_query(connection, update_query, (new_spend, new_impressions, dup_id))
        update_status_query = "UPDATE cscan_digital_records SET productID = 2 WHERE id = %s"
        execute_query(connection, update_status_query, (record_id,))
        return
    else:
        # Insert new processed record
        insert_query = """
        INSERT INTO cscan_digital_processed_records 
        (digital_record_id, file_id, spend, impressions, advertiser_name, advertiser_domain, 
         monitored_page, campaign_landing_page, creative_wrapper, creation_date) 
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """
        insert_data = (record_id, file_id, spend, impressions, advertiser_name, advertiser_domain,
                       monitored_page, campaign_landing_page, creative_wrapper, creation_date)
        processed_record_id = execute_query(connection, insert_query, insert_data)

        update_status_query = "UPDATE cscan_digital_records SET productID = 1 WHERE id = %s"
        execute_query(connection, update_status_query, (record_id,))

        # Insert related data
        related_queries = [
            ("INSERT INTO cscan_digital_processed_location (processed_record_id, digital_record_id, location, location_state_code) VALUES (%s, %s, %s, %s)",
             (processed_record_id, record_id, location, location_state_code)),
            ("INSERT INTO cscan_digital_processed_mchannel (processed_record_id, digital_record_id, channel, mchannel_id) VALUES (%s, %s, %s, %s)",
             (processed_record_id, record_id, channel, mChannelID)),
            ("INSERT INTO cscan_digital_processed_publisher (processed_record_id, digital_record_id, publisher) VALUES (%s, %s, %s)",
             (processed_record_id, record_id, publisher)),
            ("INSERT INTO cscan_digital_processed_source (processed_record_id, digital_record_id, digital_source) VALUES (%s, %s, %s)",
             (processed_record_id, record_id, digital_source)),
            ("INSERT INTO cscan_digital_processed_title (processed_record_id, digital_record_id, compaign_title) VALUES (%s, %s, %s)",
             (processed_record_id, record_id, compaign_title))
        ]
        for query, data in related_queries:
            execute_query(connection, query, data)

def main():
    conn_main = connect_to_db("localhost", "root", "Password#!@96", "competi_competidb")
    if not conn_main:
        return

    img_extensions = {'png', 'jpg', 'gif', 'jpeg'}
    vid_extensions = {'mp4', 'mov', 'avi', 'mkv', 'webm'}

    query = """
    SELECT id, creation_date, location, channel, advertiser_name, compaign_title, 
           creative_wrapper, publisher, impressions, spend, monitored_page, 
           file_id, advertiser_domain, campaign_landing_page 
    FROM cscan_digital_records WHERE productID = 0 LIMIT 1
    """
    records = execute_query(conn_main, query, fetch=True)

    for record in records:
        process_record(record, conn_main, img_extensions, vid_extensions)

    close_connection(conn_main)

if __name__ == "__main__":
    main()
