import sys
import requests
from bardapi.constants import SESSION_HEADERS
from bardapi import Bard

# Kiểm tra xem ba giá trị cookie đã được cung cấp từ đối số dòng lệnh chưa
if len(sys.argv) != 4:
    print("Sử dụng: python3 script.py <Cookie_1PSID> <Cookie_1PSIDTS> <Cookie_1PSIDCC>")
    sys.exit(1)

# Trích xuất giá trị cookie từ đối số dòng lệnh
Cookie_1PSID = sys.argv[1]
Cookie_1PSIDTS = sys.argv[2]
Cookie_1PSIDCC = sys.argv[3]

# Thiết lập phiên với các tiêu đề và cookie cần thiết
session = requests.Session()
session.headers = SESSION_HEADERS
session.cookies.set("__Secure-1PSID", Cookie_1PSID)
session.cookies.set("__Secure-1PSIDTS", Cookie_1PSIDTS)
session.cookies.set("__Secure-1PSIDCC", Cookie_1PSIDCC)

# Khởi tạo Bard với mã thông báo và phiên
bard = Bard(token=Cookie_1PSID, session=session)

# Ví dụ: Thực hiện một yêu cầu đến API của Bard
try:
    res = bard.get_answer("chào bard")

    # Kiểm tra xem có nội dung hay không
    if 'content' in res:
        print("Cookie sử dụng được")
        print(f"Nội dung: {res['content']}")
    else:
        print("Lỗi, Không có nội dung trả về hãy kiểm tra lại Cookie hoặc thay mới")
except Exception as e:
    print(f"Lỗi Cookie: {e}")
