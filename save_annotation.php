<?php
$conn = new mysqli("localhost","root","","interview_db");
if($conn->connect_error) die($conn->connect_error);

$input = file_get_contents("php://input");
if(!$input) die("No input received!");
$data = json_decode($input,true);
if(!$data) die("Invalid JSON!");

$process_id = $data['process_id'] ?? null;
$form_id = $data['form_id'] ?? null;
$ann = $data['annotation'] ?? null;

if(!$process_id || !$form_id || !$ann) die("Required data missing!");

$bbox = $ann['bbox'];

$stmt = $conn->prepare("INSERT INTO pdf_annotations 
(process_id,form_id,field_name,field_header,field_type,page,bbox_x1,bbox_y1,bbox_x2,bbox_y2,scale,metadata,image_w,image_h) 
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

$meta_json = json_encode($ann['metadata']);
$image_w = $ann['image_w'] ?? null;
$image_h = $ann['image_h'] ?? null;

$stmt->bind_param("sssssidddddsdd",
    $process_id,$form_id,
    $ann['field_name'],$ann['field_header'],$ann['field_type'],
    $ann['page'],$bbox[0],$bbox[1],$bbox[2],$bbox[3],
    $ann['scale'],$meta_json,$image_w,$image_h
);

$stmt->execute() ? print("✅ Saved") : print("❌ ".$stmt->error);
$stmt->close(); 
$conn->close();
?>
