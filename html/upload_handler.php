<?php
header('Content-Type: application/json');
if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["file"]["name"]);
    move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile);
    echo json_encode(['status' => 'success', 'message' => 'File uploaded']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Upload error']);
}
?>