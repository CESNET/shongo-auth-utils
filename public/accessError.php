<?php
$logFile = '/tmp/shib-access-error.log';
$contactEmail = 'shongo-admin@cesnet.cz';

$serverVars = $_SERVER;
$timeString = date('c', time());
$uniqueId = uniqid();

$msg = sprintf("%s [%s] Access Error:\n-----\n", $timeString, $uniqueId);
foreach ($serverVars as $key => $value) {
    $msg .= sprintf("    [%s] --> [%s]\n", $key, $value);
}
$msg .= "-----\n";

if (file_put_contents($logFile, $msg, FILE_APPEND) === false) {
    error_log(sprintf("Cannot write to file '%s'", $logFile));
}

?>

<?php include __DIR__ . '/../lib/includes/header.inc.php'; ?>

<div class="container">
    <h1>Access error</h1>

    <p>
    Thank you for helping us diagnose the problem.
    </p>
    <p>The error has been assigned a reference string <code><?php echo $uniqueId; ?></code>.
    Please send this reference in an email to <a href="mailto:<?php echo $contactEmail; ?>"><?php echo $contactEmail?></a>.
    </p>
</div>

<?php include __DIR__ . '/../lib/includes/footer.inc.php'; ?>