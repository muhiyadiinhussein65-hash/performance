<?php
include 'inc/conn.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $school = trim($_POST['school']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($name) || empty($address) || empty($school) || empty($username) || empty($password)) {
        $error = 'All fields are required!';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match!';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long!';
    } else {
        try {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM user WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() > 0) {
                $error = 'Username already exists!';
            } else {
                // Hash password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert into user table
                $stmt = $pdo->prepare("INSERT INTO user (name, username, password_hash, role, school_id) VALUES (?, ?, ?, 'student', NULL)");
                $stmt->execute([$name, $username, $password_hash]);
                
                $user_id = $pdo->lastInsertId();
                
                // Insert into students table
                $stmt = $pdo->prepare("INSERT INTO students (name, address, school) VALUES (?, ?, ?)");
                $stmt->execute([$name, $address, $school]);
                
                $success = 'Registration successful! You can now sign in.';
                header("location: signin.php");
                exit();
                // Clear form
                $_POST = array();
            }
        } catch(PDOException $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Stitch Design - Sign Up</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Fonts -->
    <link crossorigin="" href="https://fonts.gstatic.com/" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;700;800&amp;display=swap"
        rel="stylesheet" />

    <!-- Custom Styles -->
    <style>
    :root {
        --primary: #FAB440;
        --secondary: #4E47C6;
        --dark-text: #07143F;
        --background-light: #f6f7f8;
        --background-dark: #07143F;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: var(--background-light);
        min-height: 100vh;
        display: flex;
        align-items: center;
    }

    .card-custom {
        border-radius: 1rem;
        border: none;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        max-height: 90vh;
        overflow-y: auto;
    }

    .btn-primary-custom {
        background-color: var(--primary);
        border-color: var(--primary);
        color: var(--dark-text);
        font-weight: 700;
        padding: 12px;
    }

    .btn-primary-custom:hover {
        background-color: #e9a73a;
        border-color: #e9a73a;
        color: var(--dark-text);
    }

    .form-control:focus {
        border-color: var(--secondary);
        box-shadow: 0 0 0 0.25rem rgba(78, 71, 198, 0.25);
    }

    .link-custom {
        color: var(--secondary);
        font-weight: 600;
        text-decoration: none;
    }

    .link-custom:hover {
        color: #3d37a5;
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        body {
            background-color: var(--background-dark);
            color: white;
        }

        .card-custom {
            background-color: #1a1f36;
            color: white;
        }

        .form-control {
            background-color: #2d3748;
            border-color: #4a5568;
            color: white;
        }

        .form-control:focus {
            background-color: #2d3748;
            color: white;
        }
    }

    /* Custom scrollbar for the card */
    .card-custom::-webkit-scrollbar {
        width: 6px;
    }

    .card-custom::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .card-custom::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }

    .card-custom::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }

    /* Dark mode scrollbar */
    @media (prefers-color-scheme: dark) {
        .card-custom::-webkit-scrollbar-track {
            background: #2d3748;
        }

        .card-custom::-webkit-scrollbar-thumb {
            background: #4a5568;
        }

        .card-custom::-webkit-scrollbar-thumb:hover {
            background: #718096;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card card-custom p-4 p-md-5">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h1 class="h3 mb-0 fw-bold">Sign Up</h1>
                        <p class="text-muted mt-2">Create your Stitch Design account</p>
                    </div>

                    <!-- Error and Success Messages -->
                    <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Sign Up Form -->
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="name" class="form-label fw-medium">Full Name</label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name"
                                    placeholder="Your full name"
                                    value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                    required>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="address" class="form-label fw-medium">Address</label>
                                <input type="text" class="form-control form-control-lg" id="address" name="address"
                                    placeholder="Your address"
                                    value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>"
                                    required>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="school" class="form-label fw-medium">School</label>
                                <input type="text" class="form-control form-control-lg" id="school" name="school"
                                    placeholder="Your school"
                                    value="<?php echo isset($_POST['school']) ? htmlspecialchars($_POST['school']) : ''; ?>"
                                    required>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="username" class="form-label fw-medium">Username</label>
                                <input type="text" class="form-control form-control-lg" id="username" name="username"
                                    placeholder="Choose a username"
                                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                    required>
                            </div>

                            <div class="col-12 col-md-6 mb-3">
                                <label for="password" class="form-label fw-medium">Password</label>
                                <input type="password" class="form-control form-control-lg" id="password"
                                    name="password" placeholder="••••••••" required>
                            </div>

                            <div class="col-12 col-md-6 mb-4">
                                <label for="confirm_password" class="form-label fw-medium">Confirm Password</label>
                                <input type="password" class="form-control form-control-lg" id="confirm_password"
                                    name="confirm_password" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="d-grid mt-2">
                            <button type="submit" name="signup" class="btn btn-primary-custom btn-lg">
                                Create Account
                            </button>
                        </div>
                    </form>

                    <!-- Sign In Link -->
                    <div class="text-center mt-4 pt-3">
                        <p class="mb-0">Already have an account?
                            <a href="sign_in.php" class="link-custom">Sign In</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>