<?php
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    parse_str(file_get_contents('php://input'), $_DELETE);
    $item = $_GET['item'] ?? '';
    // Perform delete operation here
    echo json_encode(['status' => 'success', 'message' => "Item $item deleted"]);
}
?>