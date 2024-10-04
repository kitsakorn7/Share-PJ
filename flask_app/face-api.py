from PIL import Image, ImageDraw, ImageFont
import face_recognition
import numpy as np
import json
import io
import base64
import time  # เพิ่มการ import time
from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # เปิดใช้งาน CORS สำหรับ Flask

@app.route("/faces_recognition_Project", methods=["POST"])
def faces_recognition():

    start_time = time.time()  # บันทึกเวลาเริ่มต้น

    # รับค่า table_name จากคำขอ POST
    table_name = request.form.get('table_name')

    # สร้างชื่อไฟล์ JSON ตามชื่อของตาราง
    json_file_name = f'faces_{table_name}.json'

    # โหลดข้อมูลใบหน้าที่รู้จักจากไฟล์ JSON ที่ถูกกำหนด
    try:
        with open(json_file_name, 'r', encoding='utf-8') as f:
            data = json.load(f)
            known_face_names = [face["Name"] for face in data["known_face_names"]]
            known_face_stdId = [face["Student_number"] for face in data["known_face_names"]]
            known_face_encodings = [face["Encoding"] for face in data["known_face_names"]]
    except FileNotFoundError:
        return jsonify({"error": "JSON file not found"}), 404

    # อ่านรูปภาพที่อัปโหลด
    file = request.files['file']
    image = Image.open(file.stream)

    # หาตำแหน่งใบหน้าและเข้ารหัสใบหน้าในรูปภาพ
    face_locations = face_recognition.face_locations(np.array(image))
    face_encodings = face_recognition.face_encodings(np.array(image), face_locations)

    result_face_names = []
    result_face_stdId = []

    # สร้างออบเจ็กต์สำหรับการวาดรูป
    draw = ImageDraw.Draw(image)

    for face_encoding, face_location in zip(face_encodings, face_locations):
        # คำนวณระยะห่างของใบหน้าที่เจอกับใบหน้าที่รู้จัก
        face_distances = face_recognition.face_distance(known_face_encodings, face_encoding)
        best_match_index = np.argmin(face_distances)

        # ตรวจสอบว่าระยะห่างที่เจอนั้นใกล้เคียงกับใบหน้าที่รู้จักมากที่สุดหรือไม่
        if face_distances[best_match_index] < 0.6:  # ค่า threshold ที่กำหนดเพื่อความแม่นยำ
            name = known_face_names[best_match_index]
            stdId = known_face_stdId[best_match_index]
        else:
            name = ""
            stdId = ""
            
        result_face_names.append(name)
        result_face_stdId.append(stdId)

        # โหลดฟอนต์และกำหนดขนาด
        font_size = 20  # กำหนดขนาดของฟอนต์
        font = ImageFont.truetype("C:\\Users\\Lenovo\\AppData\\Local\\Microsoft\\Windows\\Fonts\\SukhumvitSet.ttc", font_size)

        # วาดกรอบรอบใบหน้า
        top, right, bottom, left = face_location
        for i in range(4):  # เพิ่มความหนาของกรอบ
            draw.rectangle([left - i, top - i, right + i, bottom + i], outline=(0, 255, 0))  # วาดกรอบใบหน้าด้วยสีเขียว
            y_position = top - 30  # ปรับให้ชื่อแสดงสูงขึ้นจากกรอบ    
            x_position = left
            draw.text((x_position, y_position), name, fill=(255, 255, 255), font=font)

    # แปลงภาพที่มีการวาดกรอบและชื่อกลับไปเป็นไบต์เพื่อส่งเป็นผลลัพธ์
    img_byte_arr = io.BytesIO()
    image.save(img_byte_arr, format='JPEG')
    img_byte_arr = img_byte_arr.getvalue()
    encoded_image = base64.b64encode(img_byte_arr).decode('utf-8')

    end_time = time.time()  # บันทึกเวลาสิ้นสุด
    processing_time = end_time - start_time  # คำนวณเวลาในการประมวลผล

    print(f"Found and macth use Processing time : {processing_time:.2f} Sec.")  # แสดงเวลาใน terminal

    # ส่งผลลัพธ์เป็น JSON และภาพที่มีการวาดกรอบและชื่อ
    return jsonify({"faces": result_face_names, "stdId": result_face_stdId, "image": encoded_image})

if __name__ == "__main__":
    app.run(debug=True, port=8000)
