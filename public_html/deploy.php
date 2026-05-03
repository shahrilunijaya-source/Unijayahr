<?php
// Simple deployment script - pull latest code from GitHub
$output = '';
$return_var = 0;

// Change to app directory
chdir('/home/u958029070/public_html');

// Pull latest changes
exec('git pull origin main 2>&1', $output, $return_var);

header('Content-Type: text/plain');
echo "Git pull executed\n";
echo "Return code: " . $return_var . "\n";
echo "Output:\n";
echo implode("\n", $output);
?>
