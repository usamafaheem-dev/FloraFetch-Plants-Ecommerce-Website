<?php
include("../includes/db_connect.php");
include("includes/header.php");

$message = "";

// Handle Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn) {
        $del_query = "DELETE FROM plants WHERE id = $id";
        if (mysqli_query($conn, $del_query)) {
            $message = "<div class='alert alert-success border-0 bg-success text-white'><i class='fa-solid fa-circle-check me-2'></i>Plant deleted successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger border-0 bg-danger text-white'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error deleting plant.</div>";
        }
    }
}

// Handle Update Plant
if (isset($_POST['update_plant'])) {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $botanical_name = mysqli_real_escape_string($conn, $_POST['botanical_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = (float)$_POST['price'];
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $sunlight = mysqli_real_escape_string($conn, $_POST['sunlight']);
    $watering = mysqli_real_escape_string($conn, $_POST['watering']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $care_instructions = mysqli_real_escape_string($conn, $_POST['care_instructions']);
    $stock = (int)$_POST['stock'];
    
    $update_query = "UPDATE plants SET name='$name', botanical_name='$botanical_name', category='$category', price='$price', size='$size', sunlight='$sunlight', watering='$watering', care_guide='$description', stock_quantity='$stock' WHERE id=$id";
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/plants/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = 'assets/images/plants/' . $filename;
            $update_query = "UPDATE plants SET name='$name', botanical_name='$botanical_name', category='$category', price='$price', size='$size', sunlight='$sunlight', watering='$watering', care_guide='$description', stock_quantity='$stock', image='$image_path' WHERE id=$id";
        }
    }

    if ($conn && mysqli_query($conn, $update_query)) {
        $message = "<div class='alert alert-success border-0 bg-success text-white'><i class='fa-solid fa-circle-check me-2'></i>Plant updated successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger border-0 bg-danger text-white'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error updating plant.</div>";
    }
}

// Handle Add Plant
if (isset($_POST['add_plant'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $botanical_name = mysqli_real_escape_string($conn, $_POST['botanical_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $price = (float)$_POST['price'];
    $size = mysqli_real_escape_string($conn, $_POST['size']);
    $sunlight = mysqli_real_escape_string($conn, $_POST['sunlight']);
    $watering = mysqli_real_escape_string($conn, $_POST['watering']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $care_instructions = mysqli_real_escape_string($conn, $_POST['care_instructions']);
    $stock = (int)$_POST['stock'];
    
    // Default image if upload fails
    $image_path = 'assets/images/plants/default.jpg'; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        // Simple file upload logic
        $target_dir = "../assets/images/plants/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = 'assets/images/plants/' . $filename;
        }
    }

    if ($conn) {
        $insert = "INSERT INTO plants (name, botanical_name, category, price, size, sunlight, watering, care_guide, image, stock_quantity) 
                   VALUES ('$name', '$botanical_name', '$category', '$price', '$size', '$sunlight', '$watering', '$description', '$image_path', '$stock')";
        
        if (mysqli_query($conn, $insert)) {
            $message = "<div class='alert alert-success border-0 bg-success text-white'><i class='fa-solid fa-circle-check me-2'></i>Plant added successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger border-0 bg-danger text-white'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error adding plant.</div>";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="admin-page-title mb-0">Manage Plants</h2>
    <button class="btn" style="background: var(--primary); color: white; font-weight: 500;" data-bs-toggle="modal" data-bs-target="#addPlantModal">
        <i class="fa-solid fa-plus me-1"></i> Add New Plant
    </button>
</div>

<?php echo $message; ?>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 80px;">Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($conn) {
                    $result = mysqli_query($conn, "SELECT * FROM plants ORDER BY created_at DESC");
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $img = !empty($row['image']) ? '../' . $row['image'] : '../assets/images/plants/default.jpg';
                            echo "<tr>
                                    <td><img src='{$img}' alt='plant' style='width: 45px; height: 45px; object-fit: cover; border-radius: 8px;'></td>
                                    <td style='font-weight: 600; color: var(--text-primary);'>{$row['name']}</td>
                                    <td><span class='badge' style='background: #e0f2fe; color: #0284c7; padding: 6px 10px; font-weight: 500;'>{$row['category']}</span></td>
                                    <td style='font-weight: 600;'>Rs " . number_format($row['price']) . "</td>
                                    <td class='text-end'>
                                        <button type='button' class='btn btn-sm btn-light text-primary border me-1 edit-plant-btn' 
                                            data-id='{$row['id']}'
                                            data-name='" . htmlspecialchars($row['name'], ENT_QUOTES) . "'
                                            data-botanical='" . htmlspecialchars($row['botanical_name'] ?? '', ENT_QUOTES) . "'
                                            data-category='" . htmlspecialchars($row['category'], ENT_QUOTES) . "'
                                            data-price='{$row['price']}'
                                            data-stock='{$row['stock_quantity']}'
                                            data-size='" . htmlspecialchars($row['size'] ?? '', ENT_QUOTES) . "'
                                            data-sunlight='" . htmlspecialchars($row['sunlight'] ?? '', ENT_QUOTES) . "'
                                            data-watering='" . htmlspecialchars($row['watering'] ?? '', ENT_QUOTES) . "'
                                            data-desc='" . htmlspecialchars($row['care_guide'], ENT_QUOTES) . "'
                                            data-bs-toggle='modal' data-bs-target='#editPlantModal'>
                                            <i class='fa-solid fa-pen'></i>
                                        </button>
                                        <a href='?delete={$row['id']}' class='btn btn-sm btn-light text-danger border' onclick=\"return confirm('Are you sure you want to delete this plant?');\"><i class='fa-solid fa-trash'></i></a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center py-4 text-secondary'>No plants found.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center py-4 text-warning'>Database not connected.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Plant Modal -->
<div class="modal fade" id="addPlantModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius-lg);">
      <div class="modal-header border-bottom">
        <h5 class="modal-title" style="font-family: var(--font-heading); font-weight: 700; color: var(--text-primary);">Add New Plant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="" enctype="multipart/form-data">
          <div class="modal-body p-4">
              <div class="row g-4">
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Plant Name</label>
                      <input type="text" name="name" class="form-control admin-form-control" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Botanical Name</label>
                      <input type="text" name="botanical_name" class="form-control admin-form-control" placeholder="e.g. Monstera Deliciosa">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Category</label>
                      <select name="category" class="form-select admin-form-control" required>
                          <option value="Indoor">Indoor</option>
                          <option value="Outdoor">Outdoor</option>
                          <option value="Succulents">Succulents</option>
                          <option value="Herbs">Herbs</option>
                      </select>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Price (Rs)</label>
                      <input type="number" name="price" class="form-control admin-form-control" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Stock Quantity</label>
                      <input type="number" name="stock" class="form-control admin-form-control" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Size</label>
                      <select name="size" class="form-select admin-form-control">
                          <option value="Small">Small</option>
                          <option value="Medium" selected>Medium</option>
                          <option value="Large">Large</option>
                      </select>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Sunlight Needs</label>
                      <input type="text" name="sunlight" class="form-control admin-form-control" placeholder="e.g. Full Sun, Bright Indirect">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Watering Frequency</label>
                      <input type="text" name="watering" class="form-control admin-form-control" placeholder="e.g. Weekly, Every 2 Weeks">
                  </div>
                  <div class="col-md-12">
                      <label class="form-label text-secondary fw-semibold">Plant Image</label>
                      <input type="file" name="image" class="form-control admin-form-control" accept="image/*">
                  </div>
                  <div class="col-md-12">
                      <label class="form-label text-secondary fw-semibold">Description</label>
                      <textarea name="description" class="form-control admin-form-control" rows="3" required></textarea>
                  </div>
                  <div class="col-md-12">
                      <label class="form-label text-secondary fw-semibold">Care Instructions (Watering, Light)</label>
                      <textarea name="care_instructions" class="form-control admin-form-control" rows="2"></textarea>
                  </div>
              </div>
          </div>
          <div class="modal-footer border-top p-4">
            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="add_plant" class="btn" style="background: var(--primary); color: white;">Save Plant</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Plant Modal -->
<div class="modal fade" id="editPlantModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="background: #ffffff; border: 1px solid var(--border); border-radius: var(--radius-lg);">
      <div class="modal-header border-bottom">
        <h5 class="modal-title" style="font-family: var(--font-heading); font-weight: 700; color: var(--text-primary);">Edit Plant</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="" enctype="multipart/form-data">
          <input type="hidden" name="id" id="edit_id">
          <div class="modal-body p-4">
              <div class="row g-4">
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Plant Name</label>
                      <input type="text" name="name" id="edit_name" class="form-control admin-form-control" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Botanical Name</label>
                      <input type="text" name="botanical_name" id="edit_botanical" class="form-control admin-form-control" placeholder="e.g. Monstera Deliciosa">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Category</label>
                      <select name="category" id="edit_category" class="form-select admin-form-control" required>
                          <option value="Indoor">Indoor</option>
                          <option value="Outdoor">Outdoor</option>
                          <option value="Succulents">Succulents</option>
                          <option value="Herbs">Herbs</option>
                          <option value="Flowering">Flowering</option>
                      </select>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Price (Rs)</label>
                      <input type="number" name="price" id="edit_price" class="form-control admin-form-control" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Stock Quantity</label>
                      <input type="number" name="stock" id="edit_stock" class="form-control admin-form-control" required>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Size</label>
                      <select name="size" id="edit_size" class="form-select admin-form-control">
                          <option value="Small">Small</option>
                          <option value="Medium">Medium</option>
                          <option value="Large">Large</option>
                      </select>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Sunlight Needs</label>
                      <input type="text" name="sunlight" id="edit_sunlight" class="form-control admin-form-control" placeholder="e.g. Full Sun, Bright Indirect">
                  </div>
                  <div class="col-md-6">
                      <label class="form-label text-secondary fw-semibold">Watering Frequency</label>
                      <input type="text" name="watering" id="edit_watering" class="form-control admin-form-control" placeholder="e.g. Weekly, Every 2 Weeks">
                  </div>
                  <div class="col-md-12">
                      <label class="form-label text-secondary fw-semibold">Plant Image (Leave blank to keep current)</label>
                      <input type="file" name="image" class="form-control admin-form-control" accept="image/*">
                  </div>
                  <div class="col-md-12">
                      <label class="form-label text-secondary fw-semibold">Description</label>
                      <textarea name="description" id="edit_desc" class="form-control admin-form-control" rows="3" required></textarea>
                  </div>
                  <div class="col-md-12">
                      <label class="form-label text-secondary fw-semibold">Care Instructions</label>
                      <textarea name="care_instructions" class="form-control admin-form-control" rows="2"></textarea>
                  </div>
              </div>
          </div>
          <div class="modal-footer border-top p-4">
            <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" name="update_plant" class="btn" style="background: var(--primary); color: white;">Update Plant</button>
          </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editBtns = document.querySelectorAll('.edit-plant-btn');
    editBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_id').value = this.getAttribute('data-id');
            document.getElementById('edit_name').value = this.getAttribute('data-name');
            document.getElementById('edit_botanical').value = this.getAttribute('data-botanical');
            document.getElementById('edit_category').value = this.getAttribute('data-category');
            document.getElementById('edit_price').value = this.getAttribute('data-price');
            document.getElementById('edit_stock').value = this.getAttribute('data-stock');
            document.getElementById('edit_size').value = this.getAttribute('data-size');
            document.getElementById('edit_sunlight').value = this.getAttribute('data-sunlight');
            document.getElementById('edit_watering').value = this.getAttribute('data-watering');
            document.getElementById('edit_desc').value = this.getAttribute('data-desc');
        });
    });
});
</script>

<?php include("includes/footer.php"); ?>
