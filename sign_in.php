<?php
include 'inc/conn.php';
session_start();

// Auto-login for development
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['student_id'] = 1;
    header('Location: index.php');
    exit();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signin'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password!';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password_hash, name, role FROM user WHERE username = ?");
            $stmt->execute([$username]);
            
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $user['password_hash'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Redirect to dashboard
                    header('Location: index.php');
                    exit();
                } else {
                    $error = 'Invalid password!';
                }
            } else {
                $error = 'User not found!';
            }
        } catch(PDOException $e) {
            $error = 'Login failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Stitch Design - Sign In</title>

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

    .back-btn {
        color: var(--dark-text);
        background: none;
        border: none;
        padding: 8px;
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

        .back-btn {
            color: white;
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
                    <div class="d-flex align-items-center mb-4">
                        <button class="back-btn me-3" onclick="history.back()">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 18l-6-6 6-6"></path>
                            </svg>
                        </button>
                        <h1 class="h3 mb-0 fw-bold flex-grow-1 text-center">Sign In</h1>
                    </div>

                    <!-- Error Message -->
                    <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Sign In Form -->
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label for="username" class="form-label fw-medium">Username</label>
                            <input type="text" class="form-control form-control-lg" id="username" name="username"
                                placeholder="your username"
                                value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label fw-medium">Password</label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password"
                                placeholder="••••••••" required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="signin" class="btn btn-primary-custom btn-lg">
                                Sign In
                            </button>
                        </div>
                    </form>

                    <!-- Sign Up Link -->
                    <div class="text-center mt-4 pt-3">
                        <p class="mb-0">Don't have an account?
                            <a href="index.php" class="link-custom">Sign Up</a>
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