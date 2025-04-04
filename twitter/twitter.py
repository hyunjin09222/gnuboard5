#https://www.twidouga.net/ko/realtime_t1.php

#/home/www-data/.local/lib/python3.8/site-packages/seleniumbase/drivers/

from seleniumbase import SB
from pathlib import Path

import json

path = "./twitter.json"

def download():
    with SB(uc=True, test=True, headed=True, xvfb=True) as sb:
        sb.maximize_window()
        url = "https://www.twidouga.net/ko/realtime_t1.php"
        sb.open(url)

        video_elements = sb.find_elements("#container > div > a")
        img_elements = sb.find_elements("#container > div > a > img")
        twitter_elements = sb.find_elements("#container > div > div > a")

        json_data = []
        print(str(len(video_elements))), str(len(img_elements)), str(len(twitter_elements))
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
                #print(read_json_data)
                json_file.seek(0)
                json.dump(read_json_data, json_file, indent=4)
                json_file.truncate()
            except json.JSONDecodeError as e:
                print(e)
                print("json file is empty")
                print(len(json_data))

                #중복 제거
                temp_json_data = []
                for data in json_data:
                    if data not in temp_json_data:
                        temp_json_data.append(data)

                print(len(temp_json_data))
                json.dump(temp_json_data, json_file,  indent=4)
download()
