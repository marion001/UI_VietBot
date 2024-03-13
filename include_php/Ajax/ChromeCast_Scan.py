import pychromecast
import json

# Lấy danh sách các thiết bị Chromecast trong mạng
chromecasts, browser = pychromecast.get_chromecasts()

# Tạo danh sách để lưu trữ thông tin về các thiết bị Chromecast
chromecasts_info = []

# Lặp qua từng thiết bị Chromecast và thêm thông tin của chúng vào danh sách
for cc in chromecasts:
    chromecast_info = {
        "name": cc.name,
        "model": cc.model_name,
        "uuid": str(cc.uuid),
        "manufacturer": cc.cast_info.manufacturer,
        "ip_address": cc.cast_info.host,
        "cast_type": cc.cast_info.cast_type,
        "port": cc.cast_info.port,
        "friendly_name": cc.cast_info.friendly_name
    }
    chromecasts_info.append(chromecast_info)

# Chuyển đổi danh sách thành chuỗi JSON và in ra màn hình
print(json.dumps(chromecasts_info, indent=4))
