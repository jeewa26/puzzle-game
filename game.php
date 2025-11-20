<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$difficulty = $_GET['difficulty'] ?? 'easy'; 
$difficultyClass = 'difficulty-' . strtolower($difficulty);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>🍌 Puzzle Game</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="guest-game-style.css">
  <script>
    let difficulty = "<?php echo $difficulty; ?>";
  </script>
  <script src="game.js"></script>
</head>
<body>
  <div class="game-container">
    <h1>🍌 Banana Puzzle Game</h1>
    <div style="text-align: center; margin-bottom: 20px;">
      <span class="difficulty-badge <?php echo $difficultyClass; ?>">
        <?php echo ucfirst($difficulty); ?> Mode
      </span>
    </div>
    
    <div class="stats-bar">
      <div class="stat-box">
        <div class="stat-label">Time</div>
        <div class="stat-value" id="timer">60</div>
      </div>
      <div class="stat-box">
        <div class="stat-label">Score</div>
        <div class="stat-value" id="score-value">0</div>
      </div>
    </div>

    <div id="gameArea">
      <div class="puzzle-container">
        <div id="puzzle">
          <div class="loading"></div>
          <p class="loading-text">Loading puzzle...</p>
        </div>
      </div>
      
      <input type="number" id="answer" placeholder="Enter your answer (0-9)" min="0" max="9">
      
      <button id="submit-btn" onclick="submitAnswer()">
        Submit Answer ✓
      </button>
      
      <p id="message"></p>
    </div>

    <div class="back-link">
      <a href="dashboard.php">← Back to Dashboard</a>
    </div>
  </div>
  
  <div id="notification"></div>
</body>
</html>