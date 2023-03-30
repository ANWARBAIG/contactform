<?php
// Establish MySQL connection
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "contact_form";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Validate form data
  $errors = array();
  $title = trim($_POST['title']);
  $firstname = trim($_POST['firstname']);
  $lastname = trim($_POST['lastname']);
  $dob = trim($_POST['dob']);
  $tel = trim($_POST['tel']);
  $email = trim($_POST['email']);
  $message = trim($_POST['message']);

  // Check mandatory fields
  if (empty($title)) {
    $errors[] = 'Title is required.';
  }
  if (empty($firstname)) {
    $errors[] = 'First name is required.';
  }
  if (empty($lastname)) {
    $errors[] = 'Last name is required.';
  }
  if (empty($tel)) {
    $errors[] = 'Telephone number is required.';
  }
  if (empty($email)) {
    $errors[] = 'Email address is required.';
  }
  if (empty($message)) {
    $errors[] = 'Message is required.';
  }

  // Check email format
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
  }

  // Check that the file was uploaded successfully before trying to access its properties. You can do this by checking the 'error' field in the $_FILES array. A value of 'UPLOAD_ERR_OK' means that the file was uploaded successfully:
  if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
    // File was uploaded successfully, access its properties
    $file = fopen($_FILES['filename']['tmp_name'], 'rb');
    $filename = $_FILES['filename']['name'];
        $file_contents = fread($file, $_FILES['filename']['size']);
        fclose($file);

} else {
    // There was an error uploading the file
    echo "Error uploading file: ";
}

  // Display errors or insert data into database
  if (count($errors) > 0) {
    echo '<div class="error">';
    foreach ($errors as $error) {
      echo '<p>' . $error . '</p>';
    }
    echo '</div>';
  } else {
    // Insert data into database
    $sql = "INSERT INTO contacts(title, firstname, lastname, dob, tel, file, email, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssss", $title, $firstname, $lastname, $dob, $tel, $file_contents, $email, $message);
    mysqli_stmt_execute($stmt);

    // Display success message
    $fullname = $title . ' ' . $firstname . ' ' . $lastname;
    echo '<div class="success">';
    echo '<p>Thank you ' . $fullname . '</p>';
    echo '<p>You have submitted the following details:</p>';
    echo '<ul>';
    echo '<li>Date of Birth: ' . $dob . '</li>';
    echo '<li>Telephone: ' . $tel . '</li>';
    echo '<li>File Uploaded: ' . $filename . '</li>';
    echo '<li>Message: ' . $message . '</li>';
    echo '</ul>';
    echo '</div>';
  }
}

// Close MySQL connection
mysqli_close($conn);
