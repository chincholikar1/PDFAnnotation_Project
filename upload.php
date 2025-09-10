<?php
$host="localhost"; $user="root"; $pass=""; $dbname="interview_db";
$conn = new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error) die("Connection failed: ".$conn->connect_error);

if($_SERVER["REQUEST_METHOD"]=="POST" && isset($_FILES["pdf_file"])) {
    $targetDir = "uploads/";
    if(!is_dir($targetDir)) mkdir($targetDir,0777,true);

    $fileName = basename($_FILES["pdf_file"]["name"]);
    $targetFile = $targetDir.$fileName;

    if(move_uploaded_file($_FILES["pdf_file"]["tmp_name"],$targetFile)){
        $stmt = $conn->prepare("INSERT INTO pdf_uploads (file_name,file_path) VALUES (?,?)");
        $stmt->bind_param("ss",$fileName,$targetFile);
        $stmt->execute();

      
        $process_id = uniqid("proc_");
        $form_id = 1;

        $stmt->close();
        
        header("Location: annotate.php?file=$fileName&process_id=$process_id&form_id=$form_id");
        exit;
    }else{
        echo "❌ File upload failed!";
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload PDF</title>
<style>

* {
  margin:0;
  padding:0;
  box-sizing:border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}


body {
  background: linear-gradient(135deg,#6a11cb,#2575fc);
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  padding: 10px;
}


.upload-box {
  background: #fff;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.2);
  text-align: center;
  width: 100%;
  max-width: 400px;
  transition: transform 0.3s;
}

.upload-box:hover {
  transform: translateY(-5px);
}


.upload-box h2 {
  margin-bottom: 20px;
  color: #333;
  font-size: 1.8rem;
}


.upload-box input[type="file"] {
  display: block;
  margin: 15px auto;
  width: 100%;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 1rem;
}


.upload-box button {
  background: #2575fc;
  color: #fff;
  border: none;
  padding: 12px 25px;
  border-radius: 8px;
  font-size: 1rem;
  cursor: pointer;
  transition: 0.3s;
  width: 100%;
}

.upload-box button:hover {
  background: #6a11cb;
}

/*  bamva Responsive */
@media (max-width: 480px) {
  .upload-box {
    padding: 25px 20px;
  }
  .upload-box h2 {
    font-size: 1.5rem;
  }
  .upload-box button {
    padding: 10px 20px;
    font-size: 0.95rem;
  }
}
</style>
</head>
<body>

<div class="upload-box">
  <h2>Upload Your PDF</h2>
  <form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="pdf_file" accept="application/pdf" required>
    <button type="submit">Upload PDF</button>
  </form>
</div>

</body>
</html>













<!-- 
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Upload PDF</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f9;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .upload-box {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      text-align: center;
      width: 300px;
    }
    input[type="file"] {
      margin: 15px 0;
    }
    button {
      background: #007bff;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      cursor: pointer;
    }
    button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>

  <div class="upload-box">
    <h2>Upload PDF</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="file" name="pdf_file" accept="application/pdf" required>
      <br>
      <button type="submit">Upload</button>
    </form>
  </div>

</body>
</html> -->
