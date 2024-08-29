<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="container">
    <h1>Customer Management</h1>

    <button id="addCustomerBtn">Add Customer</button>

    

    <table  id="customerTable">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Profile Picture</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
          
        </tbody>
    </table>

    </div>
   
    <div id="customerModal" class="hidden" class="container" title="Add/Edit Customer">
        <form id="customerForm" enctype="multipart/form-data">
            <input type="hidden" id="customerId" name="id" value="">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Phone Number:</label>
            <input type="text" id="phone" name="phone" required>
            <img id="currentProfilePic" src="#" alt="Profile Picture" style="display:none;"><br>

            <label for="profile_pic">Profile Picture:</label>
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*">

            <button type="submit" id="saveCustomerBtn">Save</button>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            loadCustomers(); 
            $('#addCustomerBtn').click(function() {
                openModal(); 
            });
            $('#customerForm').submit(function(e) {
                e.preventDefault();
                var phone = $('#phone').val();

              if (!/^\d{10}$/.test(phone)) {
                alert('Phone number must be 10 digits long.');
                return;
                }
                var formData = new FormData(this);
                $.ajax({
                    url: 'add_customer.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        alert(response);
                        $('#customerModal').dialog('close');
                        loadCustomers();
                    }
                });
            });
            function loadCustomers() {
                $.ajax({
                    url: 'get_customers.php',
                    type: 'GET',
                    success: function(data) {
                        $('#customerTable tbody').html(data); 
                    }
                });
            }
            function openModal(customer = {}) {
                $('#customerId').val(customer.id || '');
                $('#name').val(customer.name || '');
                $('#email').val(customer.email || '');
                $('#phone').val(customer.phone || '');
                if (customer.profile_pic) {
                    $('#currentProfilePic').attr('src', customer.profile_pic).show();
                } else {
                    $('#currentProfilePic').hide();
                }

                $('#profile_pic').val('');
                $('#customerModal').dialog({
                    modal: true,
                    width: 400,
                    height: 500 // Adjusted for image
                });
            }
            $(document).on('click', '.editCustomerBtn', function() {
                var customerId = $(this).data('id');
                $.ajax({
                    url: 'get_customer.php',
                    type: 'GET',
                    data: { id: customerId },
                    success: function(data) {
                        var customer = JSON.parse(data);
                        openModal(customer);
                    }
                });
            });
            $(document).on('click', '.deleteCustomerBtn', function() {
        if (confirm('Are you sure you want to delete this customer?')) {
            var customerId = $(this).data('id');
            $.ajax({
                url: 'delete_customer.php',
                type: 'POST',
                data: { id: customerId },
                success: function(response) {
                    alert(response);
                    loadCustomers();
                }
            });
        }
    });
        });
    </script>
</body>
</html>
