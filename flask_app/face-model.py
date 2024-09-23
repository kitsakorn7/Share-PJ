from flask import Flask, request, jsonify
import mysql.connector
import json
import numpy as np
import face_recognition
from PIL import Image
import io
import os

# create APT by Flask!
app = Flask(__name__)

# ฟังก์ชันสำหรับการเชื่อมต่อและจัดการฐานข้อมูล
def เชื่อมต่อฐานข้อมูล():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="projecta"
    )

# ฟังก์ชันดึงข้อมูลนักเรียนตามตารางที่ส่งมา (เพิ่มความ Dynamic ให้กับ API)
def ดึงข้อมูลนักเรียน(cursor, table_name):
    query = f"SELECT first_name, last_name, student_number, image, image1, image2 FROM {table_name}"
    cursor.execute(query)
    return cursor.fetchall()

# ฟังก์ชันสำหรับการจัดการรูปภาพ (เก็บรูปภาพที่ถูกดึงเข้ามา ไปยัง path ที่กำหนด)
def บันทึกรูปภาพจากBLOB(image_data, file_name):
    try:
        # เปิดรูปภาพจากข้อมูล BLOB
        image = Image.open(io.BytesIO(image_data))
        
        # กำหนดพาธที่ต้องการบันทึกไฟล์ C:/xampp/htdocs/myproject/learn-reactjs-2024/flask_app/images_fromBLOB/
        save_path = 'C:/xampp/htdocs/myproject/learn-reactjs-2024/flask_app/images_fromBLOB/BLOB_images_' + file_name # uploaded_ + file_name ชื่อไฟล์
        
        # บันทึกรูปภาพ
        image.save(save_path)
        
        return save_path  # return พาธของไฟล์ที่บันทึก
    except Exception as e:
        print(f"เกิดข้อผิดพลาดในการประมวลผลรูปภาพ {file_name}: {e}")
        return None
    
# ฟังก์ชันหลักในการตรวจจับใบหน้า!
@app.route('/recognize', methods=['POST'])
def recognize_faces():
    # รับค่าจาก PHP
    table_name = request.form.get('table_name')
    
    # เชื่อมต่อกับฐานข้อมูล
    conn = เชื่อมต่อฐานข้อมูล()
    cursor = conn.cursor()
    
    # ดึงข้อมูลนักเรียนจากตารางที่ระบุ
    students = ดึงข้อมูลนักเรียน(cursor, table_name)
    known_faces = []

    # ประมวลผลใบหน้า
    for student in students:
        first_name, last_name, student_number, image_data, image1_data, image2_data = student
        
        file_names = [
            f"{first_name}_{last_name}.jpg", 
            f"{first_name}_{last_name}_1.jpg", 
            f"{first_name}_{last_name}_2.jpg"
        ]
        student_number = student_number
        image_datas = [image_data, image1_data, image2_data]

        student_encodings = []

        # อ่านรูปภาพทั้งหมดและแปลงเป็น image_encoding จากนั้นนำมาวิเคราะห์เพื่อสร้าง face_encoding
        for image_data, file_name in zip(image_datas, file_names):
            if image_data:
                saved_file_path = บันทึกรูปภาพจากBLOB(image_data, file_name)
                if saved_file_path:
                    encoded_image = face_recognition.load_image_file(saved_file_path)
                    face_encodings = face_recognition.face_encodings(encoded_image)
                    
                    if face_encodings:
                        student_encodings.append(face_encodings[0])
                    else:
                        print(f"ไม่พบใบหน้าในรูปภาพ: {file_name}")
                else:
                    print(f"ไม่สามารถบันทึกรูปภาพ: {file_name}")
            else:
                print(f"ไม่มีข้อมูลรูปภาพสำหรับ: {file_name}")

        # นำค่าของทั้ง 3 รูป มาหาค่าเฉลี่ย และสร้างเป็น encode ชุดเดียว (mean_encoding)
        if student_encodings:
            mean_encoding = np.mean(student_encodings, axis=0).tolist()
            
            # เตรียมข้อมูลเพื่อบันทึกลงไฟล์ .json ในรูปแบบดังนี้
            known_faces.append({"Name": f"{first_name} {last_name}", "Student_number": f"{student_number}", "Encoding": mean_encoding})

    cursor.close()
    conn.close()

    known_face_data = {
        "known_face_names": known_faces,
        "num_known_faces": len(known_faces)
    }

    # สร้างชื่อไฟล์ JSON ตามชื่อของตาราง
    json_file_name = f'faces_{table_name}.json'
    
    # Write data to faces_{table_name}.json
    with open(json_file_name, 'w', encoding='utf-8') as f:
        json.dump(known_face_data, f, ensure_ascii=False, indent=4)

    return jsonify(known_face_data)  # return เป็นรูปแบบ json

if __name__ == "__main__":
    app.run(debug=True, port=5000)
