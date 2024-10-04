import { useEffect } from 'react';
import './App.css';
import { GoogleLogin } from 'react-google-login';
import { gapi } from 'gapi-script';

function App() {
  const clientId = "516643276798-0fskrc4md13hnh6auparb51ute8e35rf.apps.googleusercontent.com";

  useEffect(() => {
    const initClient = () => {
      gapi.load('client:auth2', () => {
        gapi.client.init({
          clientId: clientId,
          scope: ''
        }).then(() => {
          console.log('Google API initialized');
        }).catch(error => {
          console.error('Error initializing Google API:', error);
        });
      });
    }
    initClient();
  }, []);

  const onSuccess = (res) => {
    const url = getRedirectUrl(res.profileObj);
    window.location.href = url; // Redirect to the specified URL with user data
  };

  const onFailure = (res) => {
    console.log('failed', res);
  }

  // สร้าง URL สำหรับการเปลี่ยนเส้นทาง
  // Test in This Computer (localhost) // Hotspot Hao (172.20.10.10) // WIFI House Tar (192.168.1.39)
  
  const getRedirectUrl = (profile) => {
    const baseUrl = profile.email.endsWith('@gmail.com')
      ? 'http://localhost/myproject/learn-reactjs-2024/web_app/admin/admin_home.php' // @gmail
      : 'http://localhost/myproject/learn-reactjs-2024/course-app/tabledetails.php'; // @mail.rmutk.ac.th // Teacher Page
  
    const params = new URLSearchParams({
      user: profile.email,
      name: profile.name,
      image: profile.imageUrl
    });
  
    return `${baseUrl}?${params.toString()}`;
  };

  return (
    <div className="app-container">
      <div className="container">
        <div className="left-side">
          <div>
            <h2 className="title">Welcome to Website</h2>
            <h3 className="title">TIME ATTENDANCE SYSTEM WITH FACE RECOGNITION</h3>
            <br />
            <GoogleLogin
              clientId={clientId}
              buttonText="Sign in with Google"
              onSuccess={onSuccess}
              onFailure={onFailure}
              cookiePolicy={'single_host_origin'}
              isSignedIn={false} // ปิดการล็อกอินอัตโนมัติ
              prompt="select_account" // เพิ่มตัวเลือกนี้เพื่อให้เลือกบัญชีใหม่ทุกครั้ง
              className="google-login-button" // เพิ่ม className สำหรับการปรับแต่ง
            />
          </div>
        </div>
        <div className="right-side">
          {/* ไม่ต้องใส่ <img> ที่นี่อีกต่อไป เพราะพื้นหลังเป็นรูปภาพแล้ว */}
        </div>
      </div>
    </div>
  );
}

export default App;
