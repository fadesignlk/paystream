<?php
$title = 'View Payslips';
include __DIR__ . '/../components/header.php';

$payslipDir = __DIR__ . '/../../output/';
$payslipUrlPath = '/paystream/output/';
$payslips = [];

if (is_dir($payslipDir)) {
    $files = scandir($payslipDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'pdf') {
            $payslips[] = $file;
        }
    }
}
?>

<div class="container mt-4">
    <h1 class="mb-4">View Payslips</h1>
    <?php if (!empty($payslips)): ?>
        <div class="list-group">
            <?php foreach ($payslips as $payslip): ?>
                <a href="<?php echo htmlspecialchars($payslipUrlPath . $payslip, ENT_QUOTES); ?>" class="list-group-item list-group-item-action" target="_blank">
                    <?php echo htmlspecialchars($payslip, ENT_QUOTES); ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No payslips found.</div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../components/footer.php'; ?>