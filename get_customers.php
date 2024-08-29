<?php
include 'connection.php';

$sql = "SELECT * FROM customers";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>{$row['name']}</td>
            <td>{$row['email']}</td>
            <td>{$row['phone']}</td>
            <td><img src='{$row['profile_pic']}' width='50' alt='Profile Pic'></td>
            <td>
                <button class='editCustomerBtn' data-id='{$row['id']}'>Edit</button>
                <button class='delete-btn deleteCustomerBtn' data-id='{$row['id']}'>Delete</button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='5'>No customers found.</td></tr>";
}

$conn->close();
?>
