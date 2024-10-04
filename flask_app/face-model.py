from flask import Flask, request, jsonify
import mysql.connector
import json
import numpy as np
import face_recognition
from PIL import Image
import io
import os
import time  # เพิ่มการ import time

app = Flask(__name__)

# ฟังก์ชันสำหรับการเชื่อมต่อฐานข้อมูล
def connect_DB():
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="projecta"
    )

# ฟังก์ชันดึงข้อมูลนักเรียน
def backup_students(cursor, table_name):
    query = f"SELECT first_name, last_name, student_number, image, image1, image2 FROM {table_name}"
    cursor.execute(query)
    return cursor.fetchall()

# ฟังก์ชันสำหรับบันทึกรูปภาพ
def backup_imagefromBLOB(image_data, file_name):
    try:
        image = Image.open(io.BytesIO(image_data))
        save_path = f'C:/xampp/htdocs/myproject/learn-reactjs-2024/flask_app/images_fromBLOB/BLOB_images_{file_name}'
        image.save(save_path)
        return save_path
    except Exception as e:
        print(f"เกิดข้อผิดพลาดในการประมวลผลรูปภาพ {file_name}: {e}")
        return None

# ฟังก์ชันในการ learn_facesStudents และสร้าง encoding
def learn_facesStudents(students):
    known_faces = []
    for student in students:
        first_name, last_name, student_number, image_data, image1_data, image2_data = student
        
        file_names = [
            f"{first_name}_{last_name}.jpg", 
            f"{first_name}_{last_name}_1.jpg", 
            f"{first_name}_{last_name}_2.jpg"
        ]
        image_datas = [image_data, image1_data, image2_data]
        student_encodings = []

        for image_data, file_name in zip(image_datas, file_names):
            if image_data:
                saved_file_path = backup_imagefromBLOB(image_data, file_name)
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

        if student_encodings:
            mean_encoding = np.mean(student_encodings, axis=0).tolist()
            known_faces.append({"Name": f"{first_name} {last_name}", "Student_number": f"{student_number}", "Encoding": mean_encoding})
    
    return known_faces

@app.route('/recognize', methods=['POST'])
def recognize_faces(): # Fn ที่ใช้สำหรับการ Uodate โดยใช้วิธีการดึงข้อมูลเก่ามาหากมี และเขียนข้อมูลของบุคคลที่ยังไม่มีลงไป!

    start_time = time.time()  # บันทึกเวลาเริ่มต้น

    table_name = request.form.get('table_name')
    conn = connect_DB()
    cursor = conn.cursor()
    students = backup_students(cursor, table_name)
    known_faces = learn_facesStudents(students)

    cursor.close()
    conn.close()

    json_file_name = f'faces_{table_name}.json'
    
    if os.path.exists(json_file_name):
        with open(json_file_name, 'r', encoding='utf-8') as f:
            existing_data = json.load(f)
            existing_known_faces = existing_data.get('known_face_names', [])
    else:
        existing_known_faces = []

    existing_dict = {face["Student_number"]: face for face in existing_known_faces}

    for face in known_faces:
        student_number = face["Student_number"]
        if student_number not in existing_dict:
            existing_dict[student_number] = face
        else:
            print(f"พบ student_number ซ้ำ: {student_number} จะข้ามการอัปเดตนี้")

    known_face_data = {
        "total_known_faces": len(existing_dict),
        "known_face_names": list(existing_dict.values())
    }

    with open(json_file_name, 'w', encoding='utf-8') as f:
        json.dump(known_face_data, f, ensure_ascii=False, indent=4)

    end_time = time.time()  # บันทึกเวลาสิ้นสุด
    processing_time = end_time - start_time  # คำนวณเวลาในการประมวลผล

    print(f"Update use Processing time : {processing_time:.2f} Sec.")  # แสดงเวลาใน terminal

    return jsonify(known_face_data)

@app.route('/recognize_clear', methods=['POST']) 
def recognize_faces_clear(): # Fn ที่ใช้สำหรับการ del โดยใช้วิธีการเขียนข้อมูล ขึ้นมาใหม่!

    start_time = time.time()  # บันทึกเวลาเริ่มต้น

    table_name = request.form.get('table_name')
    conn = connect_DB()
    cursor = conn.cursor()
    students = backup_students(cursor, table_name)
    known_faces = learn_facesStudents(students)

    cursor.close()
    conn.close()

    known_face_data = {
        "total_known_faces": len(known_faces),
        "known_face_names": known_faces
    }

    json_file_name = f'faces_{table_name}.json'

    with open(json_file_name, 'w', encoding='utf-8') as f:
        json.dump(known_face_data, f, ensure_ascii=False, indent=4)

    end_time = time.time()  # บันทึกเวลาสิ้นสุด
    processing_time = end_time - start_time  # คำนวณเวลาในการประมวลผล

    print(f"Delete use Processing time : {processing_time:.2f} Sec.")  # แสดงเวลาใน terminal

    return jsonify(known_face_data)

if __name__ == "__main__":
    app.run(debug=True, port=5000)
