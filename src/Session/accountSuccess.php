<?php
session_start();
include_once("../Shared/config.php");

$supportLink = "mailto:" . MAIL_USERNAME; 
$homeLink    = "../Session/loginPhase.php";  


$success = $_SESSION['auth_success'] ?? null;
unset($_SESSION['auth_success']);

$allowed = ['password_reset', 'signup'];

if (
    !is_array($success)
    || !in_array($success['type'] ?? '', $allowed, true)
    || (time() - ($success['time'] ?? 0)) > 600
) {
    header("Location: loginPhase.php");
    exit();
}

$type = $success['type'];

if ($type === 'password_reset') {
    session_destroy();
}

function detectDevice(string $ua): string
{
    $browser = 'Browser';
    if (preg_match('/Edg\//', $ua))            $browser = 'Edge';
    elseif (preg_match('/OPR\/|Opera/', $ua))  $browser = 'Opera';
    elseif (preg_match('/Firefox\//', $ua))    $browser = 'Firefox';
    elseif (preg_match('/Chrome\//', $ua))     $browser = 'Chrome';
    elseif (preg_match('/Safari\//', $ua))     $browser = 'Safari';

    $os = 'unknown device';
    if (preg_match('/Windows/', $ua))                $os = 'Windows';
    elseif (preg_match('/Android/', $ua))            $os = 'Android';
    elseif (preg_match('/iPhone|iPad/', $ua))        $os = 'iOS';
    elseif (preg_match('/Mac OS X|Macintosh/', $ua)) $os = 'macOS';
    elseif (preg_match('/Linux/', $ua))              $os = 'Linux';

    return "$browser on $os";
}

function maskEmail(string $email): string
{
    $parts = explode('@', $email, 2);
    $user = $parts[0];
    $domain = $parts[1] ?? '';
    return substr($user, 0, 2) . str_repeat('*', max(strlen($user) - 2, 1)) . ($domain ? "@$domain" : '');
}

if ($type === 'password_reset') {
    $title = "Password updated";
    $text = "Your password has been reset. You can now sign in with your new password.";
    $statusTitle = "Account secured";
    $statusMeta = detectDevice($_SERVER['HTTP_USER_AGENT'] ?? '') . " • Just now";
    $showSupport = true;
} else {
    $title = "Account created";
    $text = "Your email is verified and your student account is ready. Sign in to get started.";
    $statusTitle = "Email verified";
    $statusMeta = maskEmail($success['email'] ?? '');
    $showSupport = false;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>

    <link rel="stylesheet" href="../../public/css/otpPhase.css?v=124">
    <link rel="stylesheet" href="../../public/css/resetFlow.css?v=2">
</head>

<body class="reset-body">

    <div class="otp-wrap">
        <div class="otp-container">

            <div class="otp-icon success pop">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
            </div>

            <h2><?= htmlspecialchars($title) ?></h2>
            <p class="success-text"><?= htmlspecialchars($text) ?></p>

            <div class="status-row">
                <span class="status-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                        <path d="m9 12 2 2 4-4" />
                    </svg>
                </span>
                <div class="status-main">
                    <div class="status-title"><?= htmlspecialchars($statusTitle) ?></div>
                    <div class="status-meta"><?= htmlspecialchars($statusMeta) ?></div>
                </div>
                <span class="pill">Verified</span>
            </div>

            <a href="loginPage.php#log-container" class="btn-continue">
                Continue to sign in
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                </svg>
            </a>

            <?php if ($showSupport && $supportLink): ?>
                <div class="support-row">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 16v-4" />
                        <path d="M12 8h.01" />
                    </svg>
                    Didn't request this change?
                    <a href="<?= htmlspecialchars($supportLink) ?>">Contact support</a>
                </div>
            <?php endif; ?>

        </div>

        <?php if ($homeLink): ?>
            <a href="<?= htmlspecialchars($homeLink) ?>" class="back-login">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m12 19-7-7 7-7" />
                    <path d="M19 12H5" />
                </svg>
                Back to homepage
            </a>
        <?php endif; ?>
    </div>

</body>

</html>