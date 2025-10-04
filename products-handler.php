<?php
ob_start(); // Clear any accidental output
include('config.php');

// Handle Create or Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $id = $_POST['id'] ?? '';

    $imageName = '';
    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/products/' . $imageName);
    }

    if ($id) {
        // If new image uploaded, delete the old one
        if ($imageName) {
            $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $oldImage = $stmt->fetchColumn();
            if ($oldImage && file_exists("uploads/products/" . $oldImage)) {
                unlink("uploads/products/" . $oldImage);
            }

            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, image=? WHERE id=?");
            $stmt->execute([$name, $desc, $price, $imageName, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=? WHERE id=?");
            $stmt->execute([$name, $desc, $price, $id]);
        }
    } else {
        // Insert new product
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $desc, $price, $imageName]);
    }

    echo "OK";
    exit;
}

// Handle DELETE
if (!empty($_POST['delete_id'])) {
    // Delete image file first
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);
    $image = $stmt->fetchColumn();

    if ($image && file_exists("uploads/products/" . $image)) {
        unlink("uploads/products/" . $image);
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_POST['delete_id']]);

    echo "Deleted";
    exit;
}
?>
