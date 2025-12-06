<?php
require_once __DIR__ . '/../db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    // echo $password;
    // print_r($user['password']);
    if(password_verify($password, $user['password'])){
      echo 'here';
    }
    if ($user && password_verify($password, $user['password'])) {
      
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
// $newPassword = 'admin123!';
// $options = [
//     'cost' => 12, // Adjust the cost as needed, 10-12 is standard.
// ];
// $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT, $options);

// echo "New Hashed Password: " . $hashedPassword
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="container">
    <div class="container-card col-md-5 mx-auto">
      <h3 class="mb-3">Admin Login</h3>
      <?php if($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
      <form method="post">
        <div class="mb-2">
          <label class="form-label">Username</label>
          <input name="username" class="form-control" required>
        </div>
        <div class="mb-2">
          <label class="form-label">Password</label>
          <input name="password" type="password" class="form-control" required>
        </div>
        <button class="btn btn-primary">Login</button>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
