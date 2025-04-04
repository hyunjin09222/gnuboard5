import json

path = "./twitter.json"
json_data = []

from pathlib import Path


json_file = Path(path)
if not json_file.is_file():
    open(json_file, 'a').close()


json_data = []
data = {
    "video" : {
        "img" : "bb",
        "twitter" : "cc"
    }
}
'''
data2 = {
    "video" : "adfasdf",
    "img" : "basdfasdfb",
    "twitter" : "asdfasdfcc"
}
'''
json_data.append(data)
#json_data.insert(0, data2)

#print(json_data)

with open(path, "r+") as json_file:
    try:
        read_json_data = json.load(json_file)
        print(read_json_data)

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
        json.dump(json_data, json_file,  indent=4)