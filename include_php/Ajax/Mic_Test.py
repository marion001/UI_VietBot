import os
import sys
import time
import pyaudio
import wave

def test_specific_microphone_and_save_wav(filename, mic_id):
    p = pyaudio.PyAudio()
    
    print(f"Bắt đầu kiểm tra microphone (ID: {mic_id}) trong vòng 6 giây...", flush=True)
    sys.stdout.flush()  # Đảm bảo kết quả được đẩy ra ngay lập tức
    
    stream = p.open(format=pyaudio.paInt16, channels=1, rate=16000, input=True, input_device_index=mic_id)
    frames = []
    
    start_time = time.time()
    while time.time() - start_time < 6:
        data = stream.read(1024)
        frames.append(data)
    
    stream.stop_stream()
    stream.close()
    p.terminate()
    
    print("Kiểm tra microphone hoàn thành.", flush=True)
    sys.stdout.flush()  # Đảm bảo kết quả được đẩy ra ngay lập tức
    
    # Lưu âm thanh thành file WAV
    wf = wave.open(filename, 'wb')
    wf.setnchannels(1)
    wf.setsampwidth(pyaudio.PyAudio().get_sample_size(pyaudio.paInt16))
    wf.setframerate(16000)
    wf.writeframes(b''.join(frames))
    wf.close()
    print(f"File WAV đã được lưu: {filename}", flush=True)
    sys.stdout.flush()  # Đảm bảo kết quả được đẩy ra ngay lập tức
    
    # Cấp quyền 777 cho tệp
    os.chmod(filename, 0o777)
    print(f"Quyền truy cập của file đã được cập nhật thành 777.", flush=True)
    sys.stdout.flush()  # Đảm bảo kết quả được đẩy ra ngay lập tức

if __name__ == "__main__":
    if len(sys.argv) != 2:
        print("Sử dụng: python3 test_mic.py <mic_id>")
        sys.exit(1)
    
    mic_id = int(sys.argv[1])
    test_specific_microphone_and_save_wav("test_recording.wav", mic_id)
