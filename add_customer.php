<?php
include 'connectionl.php'; // Include the database connection file

// Handle Add or Update Customer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $profile_pic = '';

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_pic']['tmp_name'];
        $fileName = $_FILES['profile_pic']['name'];
        $fileSize = $_FILES['profile_pic']['size'];
        $fileType = $_FILES['profile_pic']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Sanitize file name and generate new name to avoid conflicts
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        // Check if file type is allowed
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Directory for upload
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_pic = $dest_path;  // Save path for insertion into database
            } else {
                echo 'There was some error moving the file to upload directory. Please check if the upload directory is writable.';
                exit;
            }
        } else {
            echo 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
            exit;
        }
    }

    if ($id) {
        // Update existing customer
        $query = "SELECT profile_pic FROM customers WHERE id = ?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($oldProfilePic);
        $stmt->fetch();
        $stmt->close();

        // Delete old profile pic if a new one is uploaded
        if ($profile_pic && file_exists($oldProfilePic)) {
            unlink($oldProfilePic);
        }

        if ($profile_pic) {
            $stmt = $mysqli->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, profile_pic = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $profile_pic, $id);
        } else {
            $stmt = $mysqli->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("sssi", $name, $email, $phone, $id);
        }
        $stmt->execute();
        $stmt->close();
        echo 'Customer updated successfully!';
    } else {
        // Add new customer
        $stmt = $mysqli->prepare("INSERT INTO customers (name, email, phone, profile_pic, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("ssss", $name, $email, $phone, $profile_pic);
        $stmt->execute();
        $stmt->close();
        echo 'Customer added successfully!';
    }
}
?>
