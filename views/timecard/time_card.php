<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../autoload.php';
require_once __DIR__ . '/../../database/DatabaseHandler.php';
require_once __DIR__ . '/../../controllers/TimeCardController.php';
require_once __DIR__ . '/../../controllers/EmployeeController.php';

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$timeCardController = new TimeCardController($dbHandler);
$employeeController = new EmployeeController($dbHandler);

$timeCards = $timeCardController->getAllTimeCards();


$title = 'Time Cards';
include __DIR__ . '/../components/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Time Cards</h1>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Clock In Date</th>
                    <th>Clock In Time</th>
                    <th>Clock Out Date</th>
                    <th>Clock Out Time</th>
                    <th>Reported Hours</th>
                    <th>OT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($timeCards as $timeCard):
                    $reportedHours = '';
                    $overtime = '';
                    if ($timeCard->getClockInTime() && $timeCard->getClockOutTime()) {
                        $clockInDateTime = new DateTime($timeCard->getClockInDate() . ' ' . $timeCard->getClockInTime());
                        $clockOutDateTime = new DateTime($timeCard->getClockOutDate() . ' ' . $timeCard->getClockOutTime());
                        $interval = $clockInDateTime->diff($clockOutDateTime);
                        $reportedHours = $interval->format('%h hours %i minutes');
                        $totalMinutes = ($interval->h * 60) + $interval->i;
                        if ($totalMinutes > 480) { // 480 minutes = 8 hours
                            $overtimeMinutes = $totalMinutes - 480;
                            $overtime = floor($overtimeMinutes / 60) . ' hours ' . ($overtimeMinutes % 60) . ' minutes';
                        } else {
                            $overtime = '0 hours 0 minutes';
                        }
                        $employee = $employeeController->getEmployeeById($timeCard->getEmployeeId());
                    }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($timeCard->getEmployeeId()); ?></td>
                        <td><?php echo htmlspecialchars($employee['employee_name']); ?></td>
                        <td><?php echo htmlspecialchars($timeCard->getClockInDate()); ?></td>
                        <td><?php echo htmlspecialchars($timeCard->getClockInTime()); ?></td>
                        <td><?php echo htmlspecialchars($timeCard->getClockOutDate()); ?></td>
                        <td><?php echo htmlspecialchars($timeCard->getClockOutTime()); ?></td>
                        <td><?php echo htmlspecialchars($reportedHours); ?></td>
                        <td><?php echo htmlspecialchars($overtime); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function clearFilters() {
        document.getElementById('search_name').value = '';
        document.getElementById('date_from').value = '';
        document.getElementById('date_to').value = '';
        document.forms[0].submit();
    }
</script>

<?php include __DIR__ . '/../components/footer.php'; ?>