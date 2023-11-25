# !/usr/bin/python
# -*- coding: utf-8 -*-
#-*-coding:gb2312-*-

import libs

#Gọi Các thư viện của vietbot dùng cho Custom Skill
from datetime import datetime, timedelta, date
from tts_process import tts_process
from stt_process import process as stt_process
from speaker_process import play_sound

#data_process gọi thư viện nhè cho Vietbot xử lý kết quả nếu custom skill không có dữ liệu
from data_process import data_process 

#Notes
#sử dụng led: led.get_state(), led.speak(), led.wakeup(), led.think(), led.off(), led.mute()
#âm thanh: play_sound('VOL_CHANGE'), play_sound('START'),play_sound('FINISH')

Custom_Skill_TEXT_Log = "Custom Skill:> "
Custom_Skill_Color_Text_Log = "yellow"

def custom_data_process(player2, led, volume):

    #Đặt Giá trị mặc định cho câu trả lời
    response_text='Custom Skill và Vietbot Không có câu trả lời cho tình huống này'
    
    try:
        data = stt_process().lower()
    except:
        libs.logging('left', f'{Custom_Skill_TEXT_Log} Không nhận dạng được lệnh:', Custom_Skill_Color_Text_Log)
        play_sound('FINISH')
        return play_sound('START'), led.wakeup()
        
    #Gọi led trước khi xử lý dữ liệu
    led.speak()

#-------------------------------------------------------------------------------------
#Bắt đầu tùy biến cho custom skill xử lý dữ liệu theo code của người dùng
#code bên dưới đây chỉ là mẫu cơ bản để tham khảo

    #nếu người dùng nói 1 trong các từ này
    if 'chào bạn' in data or 'xin chào' in data or 'chào' in data: 
    
        #Nội dung Vietbot phản hồi lại ra loa: response_text
        response_text = f'xin chào bạn Tuyển'
        
        #hiển thị log trên console hoặc WebUI (tùy chọn trong config)
        libs.logging('left', f'{Custom_Skill_TEXT_Log} {response_text}', Custom_Skill_Color_Text_Log)
        
    #hoặc nói 1 trong các từ này
    elif 'tôi tên gì' in data or 'tên tôi là gì' in data or 'tên của tôi là gì' in data:
        response_text = f'Tên bạn là Tuyển'
        libs.logging('left', f'{Custom_Skill_TEXT_Log} {response_text}', Custom_Skill_Color_Text_Log)
       
    #hoặc nói 1 trong các từ này
    elif 'đây là đâu' in data:
        response_text = f'tôi không biết vị trí của bạn'
        libs.logging('left', f'{Custom_Skill_TEXT_Log} {response_text}', Custom_Skill_Color_Text_Log)
        
    #Nếu không có các giá trị phù hợp với các từ bên trên
    # sẽ sử dụng data_process để chuyển qua Vietbot xử lý nếu custom skill không có từ khóa để lấy dữ liệu
    else:
        #vietbot_data_process =  data_process(None,led,volume,data)  
        vietbot_data_process = data_process(None,None,None,data)
        libs.logging('left', f'Vietbot Xử Lý:> {vietbot_data_process[0]}', Custom_Skill_Color_Text_Log)
        
        #response_text = vietbot_data_process[1]
        response_text = vietbot_data_process[0]

#Kết thúc tùy biến cho custom skill xử lý dữ liệu theo code của người dùng
#--------------------------------------------------------------------------------------------------

    #Mượn tài nguyên của vietbot để xử lý dữ liệu
    player2.play_and_wait(tts_process(response_text, False)) #False - Phát câu trả lời TTS ko cache lại nội dung, True - Có cache lại để cho lần sau
    
    #Gọi Âm Thanh
    play_sound('START')  
    
    #Gọi Led
    led.wakeup() 

if __name__ == '__main__':  
    from speaker_process import Player, Volume
    #Hàm gọi thư viện led
    from led_process import Led
    led=Led(conf_data,True)
    player=Player()
    volume=Volume(0)
    print(custom_data_process(player,led,volume,data))
