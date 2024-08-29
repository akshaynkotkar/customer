<?php
include 'connection.php';

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

        
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile_pic = $dest_path; 
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
      
        $query = "SELECT profile_pic FROM customers WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($oldProfilePic);
        $stmt->fetch();
        $stmt->close();
        if ($profile_pic && file_exists($oldProfilePic)) {
            unlink($oldProfilePic);
        }

        if ($profile_pic) {
            $stmt = $conn->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, profile_pic = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("ssssi", $name, $email, $phone, $profile_pic, $id);
        } else {
            $stmt = $conn->prepare("UPDATE customers SET name = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?");
            $stmt->bind_param("sssi", $name, $email, $phone, $id);
        }
        $stmt->execute();
        $stmt->close();
        echo 'Customer updated successfully!';
    } else {
        $stmt = $conn->prepare("INSERT INTO customers (name, email, phone, profile_pic, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->bind_param("ssss", $name, $email, $phone, $profile_pic);
        $stmt->execute();
        $stmt->close();
        echo 'Customer added successfully!';
    }
}
?>
