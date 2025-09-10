<?php
$conn = new mysqli("localhost","root","","interview_db");
$process_id = $_POST['process_id'] ?? '';
$form_id = $_POST['form_id'] ?? '';
$result = $conn->query("SELECT * FROM pdf_annotations WHERE process_id='$process_id' AND form_id='$form_id'");
$data=[];
while($row=$result->fetch_assoc()) $data[]=$row;
echo json_encode($data);
$conn->close();
?>
