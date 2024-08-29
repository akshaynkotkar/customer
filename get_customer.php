<?php
include 'connection.php';

$id = $_GET['id'];
$sql = "SELECT * FROM customers WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();
    if ($customer['profile_pic']) {
        $customer['profile_pic'] = $customer['profile_pic']; 
    }
    echo json_encode($customer);
} else {
    echo json_encode([]);
}

$conn->close();
?>
