<?php
include('../config.php');

// Fetch products
$stmt = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>🛒 Product Management</h2>

<form id="productForm" enctype="multipart/form-data" method="POST">
    <input type="hidden" name="id" id="product-id">
    <input type="text" name="name" id="product-name" placeholder="Product Name" required><br>
    <textarea name="description" id="product-description" placeholder="Description" required></textarea><br>
    <input type="number" step="0.01" name="price" id="product-price" placeholder="Price" required><br>
    <input type="file" name="image"><br>
    <button type="submit">Save Product</button>
</form>

<hr>

<h3>📋 Product List</h3>
<table border="1" width="100%">
    <tr><th>Image</th><th>Name</th><th>Description</th><th>Price</th><th>Actions</th></tr>
    <tbody id="product-list">
    <?php foreach ($products as $p): ?>
        <tr data-id="<?= $p['id'] ?>">
            <td>
                <?php if ($p['image']): ?>
                    <img src="/dashboard/uploads/products/<?= htmlspecialchars($p['image'] ?? '') ?>"
     width="60" height="60"
     style="border-radius: 12px; object-fit: cover; box-shadow: 0 2px 5px rgba(0,0,0,0.2); border: 1px solid #ccc;">

                <?php else: ?>❌<?php endif; ?>
            </td>
            <td><?= htmlspecialchars($p['name'] ?? '') ?></td>
<td><?= htmlspecialchars($p['description'] ?? '') ?></td>

            <td>₱<?= $p['price'] ?></td>
            <td>
                <button class="edit-btn">✏️</button>
                <button class="delete-btn">🗑️</button>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
$(document).ready(function(){
  $('#productForm').submit(function(e){
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
        url: 'products-handler.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log("FORM RESPONSE:", response); // 🔍 Debug
            $("#content").load("ajax-pages/products.php", function(html, status, xhr) {
                if (status === "error") {
                    console.error("Load error:", xhr.responseText);
                }
            });
        }
    });
});
    $('.edit-btn').click(function(){
        const row = $(this).closest('tr');
        $('#product-id').val(row.data('id'));
        $('#product-name').val(row.find('td:eq(1)').text());
        $('#product-description').val(row.find('td:eq(2)').text());
        $('#product-price').val(row.find('td:eq(3)').text().replace("₱",""));
    });

  $('.delete-btn').click(function(){
    const id = $(this).closest('tr').data('id');
    if (confirm("Delete this product?")) {
        $.ajax({
            url: 'products-handler.php',
            type: 'POST',
            data: { delete_id: id },
            success: function(response) {
                console.log("DELETE RESPONSE:", response); // 🔍 Debug
                $("#content").load("ajax-pages/products.php", function(html, status, xhr) {
                    if (status === "error") {
                        console.error("Load error:", xhr.responseText);
                    }
                });
            }
        });
    }
});

</script>
