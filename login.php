<?php
// login.php
if (session_status() === PHP_SESSION_NONE)
    session_start();

// Already logged in? Go to index
if (!empty($_SESSION['user_id'])) {
    header("Location:index.php");
    exit;
}

// DB connect (safer absolute path)
require_once __DIR__ . '/includes/db.php';

$error = '';
$postedEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    $postedEmail = $email; // to retain on error

    if ($email === '' || $password === '') {
        $error = 'Please enter email and password.';
    } else {
        $sql = "SELECT id, name, email, password FROM adminusers WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows === 1) {
                $stmt->bind_result($uid, $uname, $uemail, $hash);
                $stmt->fetch();

                if (password_verify($password, $hash)) {
                    // Rotate session ID to prevent fixation
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = (int) $uid;
                    $_SESSION['user_name'] = (string) $uname;
                    $_SESSION['user_email'] = (string) $uemail;

                    // Allow only internal paths for "next"
                    $next = $_GET['next'] ?? 'index.php';
                    $next = (is_string($next) && str_starts_with($next, '/')) ? $next : 'index.php';

                    header("Location: {$next}");
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'Invalid email or password.';
            }
            $stmt->close();
        } else {
            // Optional: log $conn->error somewhere, but don't show to user
            $error = 'Something went wrong. Try again.';
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>HouzzHunt — Login</title>

    <!-- Boxicons CDN -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="assets/images/logo/favicon.png" type="image/x-icon">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');

        :root {
            --bg: #f5f6f7;
            --card: #fff;
            --muted: #8b8b8b;
            --accent1: #004a44;
            --accent2: #004a44;
            --input-bg: #fbfbfb;
            --shadow: 0 6px 18px rgba(14, 14, 14, 0.06);
            font-family: "Inter", "Segoe UI", Roboto, Arial, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            color: #fff;
            overflow: hidden;
            background-color: #004a44;
            background-image: url("./assets/images/texture.jpg");
            background-repeat: repeat;
            background-size: cover;
            background-blend-mode: multiply;
            font-family: "Roboto", sans-serif;
        }

        .wrap {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: var(--card);
            color: #111;
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(0, 0, 0, .04);
            overflow: hidden;
        }

        .card .top {
            padding: 24px 30px;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, .03);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
        }

        .brand img {
            width: 200px;
        }

        .lead {
            margin-top: 10px;
            color: var(--muted);
            font-size: 15px;
        }

        .card .body {
            padding: 24px 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #222;
        }

        .input-wrap {
            position: relative;
            width: 100%;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #9aa0a6;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px 14px 46px;
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, .06);
            background: var(--input-bg);
            font-size: 15px;
            outline: none;
        }

        input[type="text"] {
            width: 100%;
            padding: 14px 16px 14px 46px;
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, .06);
            background: var(--input-bg);
            font-size: 15px;
            outline: none;
        }

        input:focus {
            border-color: rgba(0, 74, 68, .25);
        }

        .eye-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #9aa0a6;
            cursor: pointer;
            background: transparent;
            border: none;
        }

        .cta {
            display: block;
            width: 100%;
            padding: 14px;
            text-align: center;
            border-radius: 10px;
            background: linear-gradient(180deg, var(--accent1), var(--accent2));
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .support {
            text-align: center;
            padding: 20px;
            color: var(--muted);
            border-top: 1px solid rgba(0, 0, 0, .03);
            font-size: 14px;
        }

        .support a {
            color: var(--accent1);
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 6px;
        }

        footer.small-foot {
            text-align: center;
            color: #c9c9c9;
            padding: 12px;
            font-size: 13px;
        }

        /* error box (added) */
        .error {
            background: #ffe9e9;
            color: #a40000;
            border: 1px solid #f5c2c7;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 14px;
            transition: opacity .6s ease, transform .6s ease, max-height .6s ease,
                margin .6s ease, padding .6s ease;
        }

        .error.fade-out {
            opacity: 0;
            transform: translateY(-6px);
            max-height: 0;
            margin: 0;
            padding: 0 12px;
            /* horizontal padding rakha so layout jump kam ho */
        }

        @media (max-width:500px) {
            .card {
                border-radius: 12px;
                margin: 0 12px;
            }

            .lead {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card">
            <div class="top">
                <div class="brand">
                    <img src="assets/images/logo/logo.svg" alt="Reliant">
                </div>
                <p class="lead">Sign in to access the Area Details <br> Report Generation Portal</p>
            </div>

            <div class="body">
                <?php if ($error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <div class="input-wrap">
                            <i class='bx bx-envelope input-icon'></i>
                            <input id="email" type="email" name="email" required placeholder="Enter your email address"
                                value="<?= htmlspecialchars($postedEmail) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <i class='bx bx-lock input-icon'></i>
                            <input id="password" type="password" name="password" required
                                placeholder="Enter your password">
                            <button type="button" class="eye-toggle" id="togglePwd">
                                <i class='bx bx-hide' id="eyeIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button class="cta" type="submit" name="login">Login</button>
                </form>
            </div>

            <div class="support">
                Need access to the valuation portal? <br>
                <a href="#">Contact System Administrator</a>
            </div>
        </div>
    </div>

    <footer class="small-foot">
        © 2024 Reliant Surveyors. All rights reserved. <br />
        Secure portal for authorized personnel only
    </footer>

    <script>
        const pwd = document.getElementById('password');
        const toggle = document.getElementById('togglePwd');
        const eyeIcon = document.getElementById('eyeIcon');

        toggle.addEventListener('click', () => {
            if (pwd.type === 'password') {
                pwd.type = 'text';
                eyeIcon.className = 'bx bx-show';
            } else {
                pwd.type = 'password';
                eyeIcon.className = 'bx bx-hide';
            }
        });
    </script>

    <script>
        // Auto-hide error after 10s
        window.addEventListener('load', () => {
            const err = document.querySelector('.error');
            if (err) {
                setTimeout(() => {
                    err.classList.add('fade-out');
                    // optional: thoda baad DOM se hata do
                    setTimeout(() => err.remove(), 700);
                }, 10000); // 10 seconds
            }
        });
    </script>

</body>

</html>