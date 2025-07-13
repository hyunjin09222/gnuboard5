import os
import subprocess
import time
import json
import schedule


from pathlib import Path

from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.common.exceptions import NoSuchElementException
from selenium.common.exceptions import ElementClickInterceptedException
from selenium.common.exceptions import TimeoutException
from selenium.common.exceptions import JavascriptException
from selenium.common.exceptions import WebDriverException
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait


path = "./twitter.json"
chromedriver = '/home/www-data/chrome/121.0.6167.184/chromedriver-linux64/chromedriver'
chrome = '/home/www-data/chrome/121.0.6167.184/chrome-linux64/chrome'

def get_driver(port):
        try:
            options = webdriver.ChromeOptions()
            #options.add_argument("--headless")
            #options.add_argument('--disable-gpu')
            #options.add_argument('--no-sandbox')
            #options.add_argument('--disable-dev-shm-usage')
            #options.add_argument('--start-maximized')
            #options.add_argument('--disable-blink-features=AutomationControlled')

            options.add_experimental_option("debuggerAddress", f"127.0.0.1:{port}")
            return webdriver.Chrome(service=Service(executable_path=chromedriver), options=options)
        except:
            raise

def runChrome(port):

    excuted = False
    while True:
        cmd = f'netstat -tnlp | grep "{port}"'
        proc = subprocess.Popen(cmd, stdout=subprocess.PIPE, shell=True)
        out, err = proc.communicate()
        if str(port) in out.decode('utf-8'):
            print(f'found chrome port : {port}')
            break
        else:
            if not excuted:
                path = os.path.dirname(chrome)
                user_data_dir = f"{path}/{port}"
                if not os.path.exists(user_data_dir):
                    os.mkdir(user_data_dir)
                cmd = f'{chrome} --disable-gpu --start-maximized --remote-debugging-port={port} --user-data-dir={user_data_dir} --enable-chrome-browser-cloud-management &'
                print(cmd)
                subprocess.call(cmd, shell=True)
                excuted = True
            time.sleep(5)

def readPage(port):
    print("read Page")
    driver = get_driver(port)
    driver.get("https://www.twidouga.net/ko/realtime_t1.php")

    video_elements = driver.find_elements(By.XPATH, '//*[@id="container"]/div/a')
    img_elements = driver.find_elements(By.CSS_SELECTOR, "#container > div > a > img")
    twitter_elements = driver.find_elements(By.CSS_SELECTOR, "#container > div > div > a")

    json_data = []
    for i in range(len(video_elements)):
        data = {
            "video" : video_elements[i].get_attribute("href"),
            "img" : img_elements[i].get_attribute("src"),
            "twitter" : twitter_elements[i].get_attribute("href")
        }
        json_data.append(data)

    json_file = Path(path)
    if not json_file.is_file():
        open(json_file, 'a').close()
    with open(path, "r+") as json_file:
        try:
            read_json_data = json.load(json_file)
            #print(read_json_data)
            for data in json_data:
                if data not in read_json_data:
                    read_json_data.insert(0, data)
                    #downloadfile(sb, data['video'].rsplit('?',1)[0])
            #print(read_json_data)
            json_file.seek(0)
            json.dump(read_json_data, json_file, indent=4)
            json_file.truncate()

        except json.JSONDecodeError as e:
            print(e)
            print("json file is empty", len(json_data))

            #중복 제거
            temp_json_data = []
            for data in json_data:
                if data not in temp_json_data:
                    temp_json_data.append(data)
                    #downloadfile(sb, data['video'].rsplit('?',1)[0])

            json.dump(temp_json_data, json_file,  indent=4)
    driver.quit()
    print("read done", len(video_elements))
    return len(video_elements)

def main():
    port = 9100
    runChrome(port)

    while readPage(port) == 0:
        print("sleep 60sec..")
        time.sleep(60)


def job():
    main()

# 매 시간 정각에 실행
schedule.every().hour.at(":00").do(job)


if __name__ == '__main__':
    print("스케줄러 시작됨")
    job()  # 시작 즉시 한 번 실행 (선택)
    while True:
        schedule.run_pending()
        time.sleep(1)

