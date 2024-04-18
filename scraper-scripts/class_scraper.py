import pprint
import re

from selenium import webdriver

from selenium.common.exceptions import NoSuchElementException
from selenium.webdriver.common.by import By
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait
import json
import time

def generate_json(course_prefix):
    
    driver = webdriver.Chrome()

    driver.get("https://services.eaglenet.jbu.edu/Student/Courses/Search?subjects=" + course_prefix)
    WebDriverWait(driver, 10).until(
        EC.presence_of_all_elements_located((By.ID, "course-results"))
    )
    course_data = []
    page_number_span = driver.find_element(By.ID, "course-results-total-pages")
    number_of_pages = int(page_number_span.text)
    for i in range(number_of_pages):
        element = driver.find_element(By.ID, "course-resultul")
        li = element.find_elements(By.TAG_NAME, "li")
        for list_item in li:
            this_course_data = {}
            course_span = list_item.find_element(
                By.XPATH,
                ".//span[@data-bind=\"attr: { id: 'course-'+$data.Id() }, text: $data.FullTitleDisplay() + ' (' + $data.CreditsCeusDisplay() + ' ' + $data.CreditsDisplayLabel() + ')'\"]",
            )
            # course title, code, credits count

            text = course_span.text
            credits_count_match = re.search(r"\((\d+ Credits)\)", text)
            try:
                credits_count = credits_count_match.group(1)
            except AttributeError:
                continue
            course_title = text.replace(credits_count, "")
            course_code = course_title.split()[0]
            course_title = course_title.replace(course_code, "")
            course_title = course_title[1:-3]
            credits_count = int(credits_count[:1])
            this_course_data["credits_count"] = credits_count
            this_course_data["course_title"] = course_title
            this_course_data["course_code"] = course_code

            # course description
            description = list_item.find_element(
                By.XPATH,
                './/div[@data-bind="html: $data.Description, visible: !isNullOrEmpty($data.Description())"]',
            )
            this_course_data["description"] = description.text

            # requisites
            requisite_div = list_item.find_element(
                By.XPATH, ".//div[@class='search-coursedataheader']"
            )
            if requisite_div.text == "Requisites:":
                requisites = requisite_div.find_element(By.XPATH, "./following-sibling::div[1]")
                requisite_text_list = []
                spans = requisites.find_elements(By.TAG_NAME, "span")
                non_hidden_spans = [
                    span for span in spans if span.value_of_css_property("display") != "none"
                ]
                requisite_text_list = [span.text for span in non_hidden_spans]
                this_course_data["requisites"] = requisite_text_list
            # get the div after requisite_div
            requisite_div_parent = requisite_div.find_element(By.XPATH, "..")
            location_div = requisite_div_parent.find_element(By.XPATH, "./following-sibling::div[1]")
            this_course_data["location"] = location_div.find_elements(By.TAG_NAME, "div")[1].text

            # find div with data-bind "visible: showCourseOffering()"
            course_offering_div = list_item.find_element(
                By.XPATH, './/div[@data-bind="visible: showCourseOffering()"]'
            )
            second_div = course_offering_div.find_elements(By.TAG_NAME, "div")[1]
            try:
                if second_div.find_element(By.TAG_NAME, "span"):
                    span_text = second_div.find_element(By.TAG_NAME, "span").text
                    # print(course_code)
                    span_text_list = [text.strip() for text in span_text.split(",")]
                    this_course_data["time-and-place-list"] = span_text_list            
            except NoSuchElementException:
                pass

            try:
                if course_offering_div.text == "":
                    table_div = course_offering_div.find_element(
                        By.XPATH, "./following-sibling::div"
                    )
                    second_child = table_div.find_elements(By.TAG_NAME, "div")[1]
                    table = table_div.find_element(By.ID, "course-location-table")
                    tbody = table.find_element(By.TAG_NAME, "tbody")
                    rows = tbody.find_elements(By.TAG_NAME, "tr")
                    td_texts = []
                    for row in rows:
                        td_texts.extend([
                            td.text
                            for td in row.find_elements(By.TAG_NAME, "td")
                        ])
                        # print(td_texts)
                        this_course_data["time-and-place-list"] = td_texts
            except NoSuchElementException:
                pass

            course_data.append(this_course_data)

        if i < number_of_pages - 1:
            button = driver.find_element(By.ID, "course-results-next-page")
            button.click()
            time.sleep(2)
            WebDriverWait(driver, 10).until(
                EC.presence_of_all_elements_located((By.XPATH, ".//span[@data-bind=\"attr: { id: 'course-'+$data.Id() }, text: $data.FullTitleDisplay() + ' (' + $data.CreditsCeusDisplay() + ' ' + $data.CreditsDisplayLabel() + ')'\"]"))
            )

        # Write course_data to a JSON file
        with open('output.json', 'w') as file:
            json.dump(course_data, file)


if __name__ == "__main__":
    prefix = str(input("Enter a Prefix to Scrape: ")).upper()
    generate_json(prefix)
