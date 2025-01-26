<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../database/DatabaseHandler.php';
require_once __DIR__ . '/../controllers/ClockingController.php';
require_once __DIR__ . '/../controllers/EmployeeController.php';

$dbHandler = new DatabaseHandler(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$clockingController = new ClockingController($dbHandler);
$employeeController = new EmployeeController($dbHandler);

$errors = [];
$successMessage = '';

$searchName = isset($_GET['search_name']) ? $_GET['search_name'] : '';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$clockings = $clockingController->getAllClockings();
$filteredClockings = [];

foreach ($clockings as $clocking) {
    $employee = $employeeController->getEmployeeById($clocking['employee_id']);
    if ($searchName && stripos($employee['employee_name'], $searchName) === false) {
        continue;
    }
    if ($dateFrom && $clocking['clocking_date'] < $dateFrom) {
        continue;
    }
    if ($dateTo && $clocking['clocking_date'] > $dateTo) {
        continue;
    }
    $clocking['employee_name'] = $employee['employee_name'];
    $filteredClockings[] = $clocking;
}

// Sort clockings by employee and date/time
usort($filteredClockings, function($a, $b) {
    if ($a['employee_id'] == $b['employee_id']) {
        return strtotime($a['clocking_date'] . ' ' . $a['clocking_time']) - strtotime($b['clocking_date'] . ' ' . $b['clocking_time']);
    }
    return $a['employee_id'] - $b['employee_id'];
});

// Sort clockings by date/time in descending order
usort($filteredClockings, function($a, $b) {
    return strtotime($b['clocking_date'] . ' ' . $b['clocking_time']) - strtotime($a['clocking_date'] . ' ' . $a['clocking_time']);
});

// Group clockings by employee and shift
$groupedClockings = [];
foreach ($filteredClockings as $clocking) {
    $employeeId = $clocking['employee_id'];
    $clockingDate = $clocking['clocking_date'];
    $clockingTime = $clocking['clocking_time'];
    $clockingType = $clocking['clocking_type'];

    if (!isset($groupedClockings[$employeeId])) {
        $groupedClockings[$employeeId] = [];
    }

    // Find the appropriate shift for the clocking entry
    $shiftFound = false;
    foreach ($groupedClockings[$employeeId] as &$shift) {
        if ($shift['clock_out_time'] == '' && $clockingType == 'Out') {
            $shift['clock_out_time'] = $clockingTime;
            $shift['clock_out_date'] = $clockingDate;
            $shiftFound = true;
            break;
        } elseif ($shift['clock_in_time'] == '' && $clockingType == 'In') {
            $shift['clock_in_time'] = $clockingTime;
            $shift['clock_in_date'] = $clockingDate;
            $shiftFound = true;
            break;
        }
    }

    // If no appropriate shift is found, create a new shift entry
    if (!$shiftFound) {
        $groupedClockings[$employeeId][] = [
            'employee_id' => $employeeId,
            'employee_name' => $clocking['employee_name'],
            'clock_in_date' => $clockingType == 'In' ? $clockingDate : '',
            'clock_out_date' => $clockingType == 'Out' ? $clockingDate : '',
            'clock_in_time' => $clockingType == 'In' ? $clockingTime : '',
            'clock_out_time' => $clockingType == 'Out' ? $clockingTime : '',
        ];
    }
}



$title = 'Time Card';
include __DIR__ . '/components/header.php';
?>

<div class="container mt-4">
    <h1 class="mb-4">Time Card</h1>

    <form method="get" action="time_card.php" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="text" id="search_name" name="search_name" class="form-control" placeholder="Search by name" value="<?php echo htmlspecialchars($searchName); ?>">
        </div>
        <div class="col-md-3">
            <input type="date" id="date_from" name="date_from" class="form-control" placeholder="Date from" value="<?php echo htmlspecialchars($dateFrom); ?>">
        </div>
        <div class="col-md-3">
            <input type="date" id="date_to" name="date_to" class="form-control" placeholder="Date to" value="<?php echo htmlspecialchars($dateTo); ?>">
        </div>
        <div class="col-md-3 d-flex justify-content-end">
            <button type="submit" class="btn btn-primary me-2">Search</button>
            <button type="button" class="btn btn-secondary" onclick="clearFilters()">Clear</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Clock Date</th>
                    <th>Clock In Time</th>
                    <th>Clock Out Time</th>
                    <th>Reported Hours</th>
                    <th>OT</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($groupedClockings as $employeeClockings):
                    foreach ($employeeClockings as $clocking):
                        $reportedHours = '';
                        if ($clocking['clock_in_time'] && $clocking['clock_out_time']) {
                            $clockInDateTime = new DateTime($clocking['clock_in_date'] . ' ' . $clocking['clock_in_time']);
                            $clockOutDateTime = new DateTime($clocking['clock_out_date'] . ' ' . $clocking['clock_out_time']);
                            $interval = $clockInDateTime->diff($clockOutDateTime);
                            $reportedHours = $interval->format('%h hours %i minutes');
                            $reportedHours = $interval->format('%h hours %i minutes');
                            $totalMinutes = ($interval->h * 60) + $interval->i;
                            if ($totalMinutes > 480) { // 480 minutes = 8 hours
                                $overtimeMinutes = $totalMinutes - 480;
                                $overtime = floor($overtimeMinutes / 60) . ' hours ' . ($overtimeMinutes % 60) . ' minutes';
                            }else{
                                $overtime = '0 hours 0 minutes';
                            }
                        }
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($clocking['employee_id']); ?></td>
                            <td><?php echo htmlspecialchars($clocking['employee_name']); ?></td>
                            <td title="Shift started on <?php echo htmlspecialchars($clocking['clock_in_date']); ?>">
                                <?php echo htmlspecialchars($clocking['clock_in_date']); ?>
                            </td>
                            <td><?php echo htmlspecialchars($clocking['clock_in_time']); ?></td>
                            <td><?php echo htmlspecialchars($clocking['clock_out_time']); ?></td>
                            <td><?php echo htmlspecialchars($reportedHours); ?></td>
                            <td><?php echo htmlspecialchars($overtime); ?></td>
                        </tr>
                    <?php endforeach; ?>
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

<?php include __DIR__ . '/components/footer.php'; ?>