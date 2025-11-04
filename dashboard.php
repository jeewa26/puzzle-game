<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT id FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$user_id = $user['id'];

$stmt = $conn->prepare("SELECT difficulty, score, created_at FROM scores WHERE user_id=? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$scores = $stmt->get_result();

$stmt = $conn->prepare("SELECT COUNT(*) as total_games, MAX(score) as best_score, AVG(score) as avg_score FROM scores WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Puzzle Game</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="dashboard-style.css">
</head>
<body>
  <div class="container">
    <div class="welcome-header">
      <h1>🧩 Puzzle Game Dashboard</h1>
      <h2>Welcome back, <?php echo htmlspecialchars($username); ?>! 👋</h2>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value"><?php echo $stats['total_games'] ?? 0; ?></div>
        <div class="stat-label">Games Played</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?php echo $stats['best_score'] ?? 0; ?></div>
        <div class="stat-label">Best Score</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?php echo $stats['avg_score'] ? round($stats['avg_score']) : 0; ?></div>
        <div class="stat-label">Average Score</div>
      </div>
    </div>
    
    <div class="card">
      <h3>🎮 Choose Difficulty</h3>
      <form action="game.php" method="get">
        <select name="difficulty" required>
          <option value="easy">Easy - 60 seconds</option>
          <option value="medium">Medium - 40 seconds</option>
          <option value="hard">Hard - 20 seconds</option>
        </select>
        <button type="submit" style="width: 100%; margin-top: 10px;">Start Game 🚀</button>
      </form>
    </div>

    <div class="card">
      <h3>📊 Your Last 5 Scores</h3>
      <?php if ($scores->num_rows > 0) { ?>
        <table>
          <tr>
            <th>Difficulty</th>
            <th>Score</th>
            <th>Date</th>
          </tr>
          <?php while ($row = $scores->fetch_assoc()) { 
            $difficultyClass = 'difficulty-' . strtolower($row['difficulty']);
          ?>
            <tr>
              <td>
                <span class="difficulty-badge <?php echo $difficultyClass; ?>">
                  <?php echo ucfirst($row['difficulty']); ?>
                </span>
              </td>
              <td><strong><?php echo $row['score']; ?></strong></td>
              <td><?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?></td>
            </tr>
          <?php } ?>
        </table>
      <?php } else { ?>
        <p style="text-align: center; color: var(--text-secondary); padding: 40px;">
          No games played yet. Start your first game! 🎯
        </p>
      <?php } ?>
    </div>

    <div class="nav-links">
      <a href="leaderboard.php">🏆 View Leaderboard</a> | 
      <a href="logout.php">🚪 Logout</a>
    </div>
  </div>
</body>
</html>