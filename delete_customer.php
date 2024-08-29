<?php
include 'connection.php';

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "SELECT profile_pic FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($profile_pic);
    $stmt->fetch();
    $stmt->close();

    if ($profile_pic && file_exists($profile_pic)) {
        unlink($profile_pic); 
    }
    $sql = "DELETE FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo 'Customer deleted successfully!';
    } else {
        echo 'Failed to delete customer.';
    }
    $stmt->close();
}

$conn->close();
?>
