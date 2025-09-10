<?php

$file = isset($_GET['file']) ? $_GET['file'] : null;
if (!$file || !file_exists("uploads/" . $file)) {
    die("PDF file not found!");
}
$pdfFile = "uploads/" . $file;

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "interview_db";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>PDF Annotation</title>
<style>
body { margin:0; padding:0; font-family:Arial, sans-serif; background:#f4f4f9; }
#pdf-container { margin-top:20px; border:1px solid #ccc; box-shadow:0 0 5px rgba(0,0,0,0.2); width:90%; max-width:900px; position:relative; }
canvas { width:100%; height:auto; display:block; border-bottom:1px solid #ddd; }
#overlay { position:absolute; top:0; left:0; width:100%; height:100%; cursor:crosshair; }
.rect { position:absolute; border:2px dashed red; pointer-events:none; }
#fieldForm { position:fixed; top:20px; right:20px; background:white; padding:15px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.2); z-index:1000; }
#fieldForm input, #fieldForm select, #fieldForm button { display:block; margin:5px 0; width:200px; }
</style>
</head>
<body>

<h2>PDF Annotation + Field Mapping</h2>
<div id="pdf-container"></div>
<div id="fieldForm" style="display:none;">
    <h3>Field Info</h3>
    <input type="text" id="field_name" placeholder="Field Name" required>
    <input type="text" id="field_header" placeholder="Field Header">
    <select id="field_type">
        <option value="CharField">CharField</option>
        <option value="DateField">DateField</option>
        <option value="NumberField">NumberField</option>
    </select>
    <button id="saveField">Save Mapping</button>
</div>

<!--  ye iska hai PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.13.216/pdf.min.js"></script>
<script>
const pdfUrl = "<?php echo $pdfFile; ?>";
const container = document.getElementById("pdf-container");

let rectangles = []; // store annotations
let startX, startY, isDrawing=false;
let overlay, currentRect;

const renderPDF = async (url) => {
    const pdf = await pdfjsLib.getDocument(url).promise;
    for (let pageNum=1; pageNum<=pdf.numPages; pageNum++){
        const page = await pdf.getPage(pageNum);
        const viewport = page.getViewport({scale:1.5});
        const canvas = document.createElement("canvas");
        canvas.height = viewport.height;
        canvas.width = viewport.width;
        const ctx = canvas.getContext("2d");
        await page.render({canvasContext: ctx, viewport: viewport}).promise;
        container.appendChild(canvas);

        if(pageNum==1){ // overlay only on first page
            overlay = document.createElement("div");
            overlay.id="overlay";
            overlay.style.width=canvas.offsetWidth+"px";
            overlay.style.height=canvas.offsetHeight+"px";
            container.appendChild(overlay);

            overlay.addEventListener("mousedown", e=>{
                isDrawing=true;
                const rect = overlay.getBoundingClientRect();
                startX = e.clientX - rect.left;
                startY = e.clientY - rect.top;
                currentRect = document.createElement("div");
                currentRect.className="rect";
                currentRect.style.left=startX+"px";
                currentRect.style.top=startY+"px";
                overlay.appendChild(currentRect);
            });

            overlay.addEventListener("mousemove", e=>{
                if(!isDrawing) return;
                const rect = overlay.getBoundingClientRect();
                let w = (e.clientX - rect.left) - startX;
                let h = (e.clientY - rect.top) - startY;
                currentRect.style.width = Math.abs(w)+"px";
                currentRect.style.height = Math.abs(h)+"px";
                currentRect.style.left = (w<0?startX+w:startX)+"px";
                currentRect.style.top = (h<0?startY+h:startY)+"px";
            });

            overlay.addEventListener("mouseup", e=>{
                isDrawing=false;
                document.getElementById("fieldForm").style.display="block";
            });
        }
    }
};

renderPDF(pdfUrl);

//  Mapping
document.getElementById("saveField").addEventListener("click", ()=>{
    const name = document.getElementById("field_name").value;
    const header = document.getElementById("field_header").value;
    const type = document.getElementById("field_type").value;

    if(!name){ alert("Field Name required"); return; }

    const bbox = currentRect.getBoundingClientRect();
    const overlayRect = overlay.getBoundingClientRect();
    const x1 = bbox.left - overlayRect.left;
    const y1 = bbox.top - overlayRect.top;
    const x2 = x1 + bbox.width;
    const y2 = y1 + bbox.height;

    //  annotation with bbox and metadata
    rectangles.push({
        field_name: name,
        field_header: header,
        field_type: type,
        bbox: [x1, y1, x2, y2],
        page: 1,
        scale: 1.5,
        metadata: {} // empty 
    });

    
    const xhr = new XMLHttpRequest();
    xhr.open("POST","save_annotation.php",true);
    xhr.setRequestHeader("Content-Type","application/json");
    xhr.onload = ()=>{ alert(xhr.responseText); }
    xhr.send(JSON.stringify({
        process_id: "<?php echo uniqid("proc_"); ?>",
        form_id: 1,
        annotation: rectangles[rectangles.length-1]
    }));

   
    document.getElementById("fieldForm").style.display="none";
    currentRect=null;
});
</script>
</body>
</html>
