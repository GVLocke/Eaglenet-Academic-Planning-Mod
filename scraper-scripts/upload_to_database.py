import mysql.connector
import json

def upload_to_database():
    cnx = mysql.connector.connect(user="root", password="example", host="127.0.0.1", database="jbu_catalog", port=3308)

    cursor = cnx.cursor()

    with open('output.json') as f:
        data = json.load(f)

    for course in data:
        insert_query = """
        INSERT INTO all_classes (course_code, course_title, credits_count, description, requisites, time_and_place_list, location)
        VALUES (%s, %s, %s, %s, %s, %s, %s)
        """
        cursor.execute(insert_query, (course['course_code'], course['course_title'], course['credits_count'], course['description'], ', '.join(course['requisites']), ', '.join(course['time-and-place-list']), course['location']))

    cnx.commit()

    cursor.close()
    cnx.close()

if __name__ == "__main__":
    upload_to_database()