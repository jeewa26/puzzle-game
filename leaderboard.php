<?php
session_start();
include("db.php");

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT users.username, scores.score, scores.difficulty, scores.created_at
        FROM scores
        JOIN users ON scores.user_id = users.id
        ORDER BY scores.score DESC, scores.created_at ASC
        LIMIT 10";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leaderboard - Puzzle Game</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>🏆 Leaderboard</h1>
      <h2>Top 10 Players</h2>
      
      <?php if ($result->num_rows > 0) { ?>
        <table>
          <tr>
            <th style="width: 80px; text-align: center;">Rank</th>
            <th>Player</th>
            <th>Difficulty</th>
            <th style="text-align: center;">Score</th>
            <th>Date</th>
          </tr>
          <?php 
          $rank = 1;
          while ($row = $result->fetch_assoc()) { 
              $rankClass = '';
              if ($rank <= 3) {
                  $rankClass = 'rank-' . $rank;
              }
              $difficultyClass = 'difficulty-' . strtolower($row['difficulty']);
              $isCurrentUser = ($row['username'] == $_SESSION['username']);
          ?>
            <tr style="<?php echo $isCurrentUser ? 'background: rgba(99, 102, 241, 0.15); border-left: 3px solid var(--primary);' : ''; ?>">
              <td style="text-align: center;">
                <?php if ($rank <= 3) { ?>
                  <span class="rank-badge <?php echo $rankClass; ?>">
                    <?php echo $rank; ?>
                  </span>
                <?php } else { ?>
                  <strong><?php echo $rank; ?></strong>
                <?php } ?>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                <?php if ($isCurrentUser) echo '<span style="color: var(--primary); margin-left: 5px;">← You</span>'; ?>
              </td>
              <td>
                <span class="difficulty-badge <?php echo $difficultyClass; ?>">
                  <?php echo ucfirst($row['difficulty']); ?>
                </span>
              </td>
              <td style="text-align: center;">
                <strong style="color: var(--success); font-size: 1.2em;">
                  <?php echo $row['score']; ?>
                </strong>
              </td>
              <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
            </tr>
          <?php 
              $rank++;
          } 
          ?>
        </table>
      <?php } else { ?>
        <p style="text-align: center; color: var(--text-secondary); padding: 40px;">
          No scores yet. Be the first to play! 🎯
        </p>
      <?php } ?>
    </div>

    <div class="nav-links">
      <a href="dashboard.php">←  Back to Dashboard</a>
    </div>
  </div>
</body>
</html>