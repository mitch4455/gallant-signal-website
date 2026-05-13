<?php
declare(strict_types=1);

// ─── CONFIG ───────────────────────────────────────────────────────
// Where proposal submissions land. Multiple recipients allowed
// (comma-separated). Edit this if your inbox changes.
$RECIPIENT = 'mitchgallant20@gmail.com';

// "From" address. Must be on a domain hosted on this server for best
// deliverability (Hostinger requires same-domain From for outbound mail).
$FROM_EMAIL = 'forms@gallantsignal.com';
$FROM_NAME  = 'Gallant Signal Forms';

// Where to redirect on success / failure (relative URLs on this site).
$REDIRECT_OK   = '/?submitted=1#proposal';
$REDIRECT_FAIL = '/?error=1#proposal';
// ──────────────────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Location: /');
    exit;
}

// Honeypot — bots fill this hidden field, humans don't see it.
if (!empty($_POST['website'] ?? '')) {
    header('Location: ' . $REDIRECT_OK);
    exit;
}

$first = trim((string)($_POST['first_name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));

if ($first === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ' . $REDIRECT_FAIL);
    exit;
}

$last    = trim((string)($_POST['last_name']     ?? ''));
$company = trim((string)($_POST['company']       ?? ''));
$phone   = trim((string)($_POST['phone']         ?? ''));
$notes   = trim((string)($_POST['notes']         ?? ''));
$biz     = trim((string)($_POST['business_type'] ?? ''));
$goal    = trim((string)($_POST['goal']          ?? ''));
$budget  = trim((string)($_POST['budget']        ?? ''));

$BIZ_LABELS = [
    'local'  => 'Local Manitoba business',
    'dealer' => 'Automotive dealership',
    'brand'  => 'National / multi-location brand',
    'other'  => 'Something else',
];
$GOAL_LABELS = [
    'visibility' => 'Get found online',
    'leads'      => 'Generate more leads',
    'ai'         => 'Get AI-ready',
    'full'       => 'Full marketing partner',
];
$BUDGET_LABELS = [
    'under1' => 'Under $1,000/mo',
    '1to3'   => '$1,000 – $3,000/mo',
    '3to7'   => '$3,000 – $7,000/mo',
    'over7'  => '$7,000+/mo',
];

$bizPretty    = $BIZ_LABELS[$biz]      ?? ($biz    ?: '(not selected)');
$goalPretty   = $GOAL_LABELS[$goal]    ?? ($goal   ?: '(not selected)');
$budgetPretty = $BUDGET_LABELS[$budget] ?? ($budget ?: '(not selected)');

$fullName = trim($first . ' ' . $last);
$ip       = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$ua       = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$ts       = date('Y-m-d H:i:s T');

$subject = sprintf(
    'New proposal request — %s%s',
    $fullName,
    $company !== '' ? " ($company)" : ''
);

$body  = "New proposal request from gallantsignal.com\n";
$body .= str_repeat('-', 50) . "\n\n";
$body .= "Name:     $fullName\n";
$body .= "Email:    $email\n";
if ($phone   !== '') { $body .= "Phone:    $phone\n";   }
if ($company !== '') { $body .= "Company:  $company\n"; }
$body .= "\n";
$body .= "Business type:  $bizPretty\n";
$body .= "Goal:           $goalPretty\n";
$body .= "Budget:         $budgetPretty\n";
$body .= "\n";
if ($notes !== '') {
    $body .= "Notes:\n$notes\n\n";
}
$body .= str_repeat('-', 50) . "\n";
$body .= "Submitted:  $ts\n";
$body .= "IP:         $ip\n";
$body .= "User agent: $ua\n";

$encodedFromName = '=?UTF-8?B?' . base64_encode($FROM_NAME) . '?=';
$headers = implode("\r\n", [
    "From: $encodedFromName <$FROM_EMAIL>",
    "Reply-To: $email",
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: gallantsignal.com/submit.php',
]);

$ok = @mail(
    $RECIPIENT,
    '=?UTF-8?B?' . base64_encode($subject) . '?=',
    $body,
    $headers,
    '-f' . $FROM_EMAIL
);

header('Location: ' . ($ok ? $REDIRECT_OK : $REDIRECT_FAIL));
