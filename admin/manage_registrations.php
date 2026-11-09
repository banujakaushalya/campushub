<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

// Check role permission: super_admin or admin
$curr_role = $_SESSION['role'] ?? 'guest';
if ($curr_role !== 'super_admin' && $curr_role !== 'admin') {
    echo "<script>window.location.href = '../student/dashboard.php';</script>";
    exit;
}

// Fetch all registrations
$registrations = [];
$sql = "SELECT r.registration_id, r.reg_date,u.first_name, u.last_name, u.email, u.phone, e.title as event_title, e.event_date FROM registrations r JOIN users u ON r.users_user_id = u.user_id JOIN events e ON r.events_event_id = e.event_id
        ORDER BY r.reg_date DESC";

$res = Database::search($sql);
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $registrations[] = $row;
    }
}
?>
    <main class="page-container">
        <style>
            .page-container {
                --ink: #171b2a;
                --muted: #687083;
                --line: #e4e7ed;
                width: 100%;
                padding: 0 24px 64px;
                box-sizing: border-box;
            }

            .header-bar,
            .table-container {
                width: 100%;
                max-width: 1200px;
                margin-right: auto;
                margin-left: auto;
            }

            .header-bar {
                margin-top: 36px;
                margin-bottom: 24px;
            }

            .header-bar h1 {
                margin: 0;
                color: var(--ink);
                font-size: 1.8rem;
            }

            .table-container {
                background: #ffffff;
                border: 1px solid var(--line);
                border-radius: 12px;
                overflow-x: auto;
                box-shadow: 0 8px 24px rgba(23, 27, 42, 0.05);
            }

            table {
                width: 100%;
                border-collapse: collapse;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                font-size: 0.9rem;
            }

            th {
                background: #faf8ff;
                text-align: left;
                padding: 14px 20px;
                font-weight: 700;
                color: var(--ink);
                border-bottom: 1px solid var(--line);
            }

            td {
                padding: 14px 20px;
                border-bottom: 1px solid #f2f4f7;
                color: #333d52;
            }
        </style>

        <div class="header-bar">
            <h1>All Event Registrations</h1>
            <p style="margin: 4px 0 0; color: var(--muted); font-family: 'Cabin Sketch', cursive; font-size: 1.15rem;">
                Monitor student participant rosters.
            </p>
        </div>

        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Contact Info</th>
                    <th>Registered Event</th>
                    <th>Event Date</th>
                    <th>Registration Date</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($registrations)): ?>
                    <?php foreach ($registrations as $r): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></strong>
                            </td>
                            <td>
                                <small style="color: var(--muted); display:block;"><?php echo htmlspecialchars($r['email']); ?></small>
                                <small style="color: var(--muted);"><?php echo htmlspecialchars($r['phone']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($r['event_title']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($r['event_date'])); ?></td>
                            <td><?php echo date('M d, Y g:i A', strtotime($r['reg_date'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 20px;">No registrations found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
<?php
require_once '../includes/footer.php';
?>