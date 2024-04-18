import class_scraper as scrape
import upload_to_database as up

with open('course_prefixes.txt', 'r') as f:
    prefixes = [line.strip() for line in f]

for prefix in prefixes:
    try:
        scrape.generate_json(prefix)
        up.upload_to_database()
        print("Uploaded " + prefix + " classes")
    except Exception as e:
        print("An error occurred on prefix "+ prefix + ":, " + str(e))