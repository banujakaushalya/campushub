<?php
require_once '../includes/header.php';
require_once '../includes/db.php';

$students = [];
$res = Database::search("SELECT u.*, (SELECT COUNT(*) FROM registrations r WHERE r.users_user_id = u.user_id) as reg_count FROM users u WHERE u.role = 'student' ORDER BY u.created_at DESC");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $students[] = $row;
    }
} else {
    $students = [
        [
            'user_id' => 2,
            'first_name' => 'Kamal',
            'last_name' => 'Perera',
            'email' => 'kamal@student.edu',
            'phone' => '0719876543',
            'gender' => 'male',
            'student_id' => 'JI-2024-042',
            'institution' => 'Java Institute',
            'reg_count' => 2
        ],
        [
            'user_id' => 3,
            'first_name' => 'Nimali',
            'last_name' => 'Silva',
            'email' => 'nimali@student.edu',
            'phone' => '0761122334',
            'gender' => 'female',
            'student_id' => 'JI-2024-089',
            'institution' => 'Java Institute',
            'reg_count' => 1
        ]
    ];
}
?>
<main class="page-container">
  <style>
    .page-container {
      --ink: #171b2a;
      --muted: #687083;
      --line: #e4e7ed;
      --coral: #FF6B4A;
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

    .badge-count {
      background: rgba(255, 107, 74, 0.12);
      color: var(--coral);
      font-weight: bold;
      padding: 4px 10px;
      border-radius: 12px;
    }
  </style>

  <div class="header-bar">
    <h1>Registered Students Directory</h1>
    <p style="margin: 4px 0 0; color: var(--muted); font-family: 'Cabin Sketch', cursive; font-size: 1.15rem;">
      View student user accounts, academic IDs, and campus engagement records.
    </p>
  </div>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Student ID</th>
          <th>Full Name</th>
          <th>Email Address</th>
          <th>Contact Phone</th>
          <th>Gender</th>
          <th>Institution</th>
          <th>Event Passes</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($students as $stu): ?>
          <tr>
            <td><strong><?php echo htmlspecialchars($stu['student_id'] ?? 'JI-STU'); ?></strong></td>
            <td><?php echo htmlspecialchars($stu['first_name'] . ' ' . $stu['last_name']); ?></td>
            <td><?php echo htmlspecialchars($stu['email']); ?></td>
            <td><?php echo htmlspecialchars($stu['phone']); ?></td>
            <td><?php echo ucfirst(htmlspecialchars($stu['gender'] ?? 'other')); ?></td>
            <td><?php echo htmlspecialchars($stu['institution'] ?? 'Java Institute'); ?></td>
            <td><span class="badge-count"><?php echo htmlspecialchars($stu['reg_count'] ?? 0); ?> Events</span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php
require_once '../includes/footer.php';
?>
