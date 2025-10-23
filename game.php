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
  <title>Game - Puzzle</title>
  <link rel="stylesheet" href="style.css">
  <script>

    let difficulty = "<?php echo $difficulty; ?>";
  </script>
  <script src="game.js"></script>
</head>
<body>
  <div class="container">
    <div class="card">
      <h1>🧩 Puzzle Game</h1>
      <div style="text-align: center; margin-bottom: 20px;">
        <span class="difficulty-badge <?php echo $difficultyClass; ?>">
          <?php echo ucfirst($difficulty); ?> Mode
        </span>
      </div>
      
      <div style="text-align: center;">
        <p id="timer" style="display: inline-block;">Time: 60</p>
        <div class="score-display" style="margin-left: 20px;">
          Score: <span id="score-value">0</span>
        </div>
      </div>
      
      <div id="gameArea">
        <div id="puzzle">
          <div class="loading"></div>
          <p style="color: var(--text-secondary); margin-top: 10px;">Loading puzzle...</p>
        </div>
        
        <input type="number" id="answer" placeholder="Enter your answer (0-9)" 
               style="max-width: 300px; margin: 20px auto; display: block; text-align: center; font-size: 1.2em;">
        
        <button onclick="submitAnswer()" style="padding: 15px 50px;">
          Submit Answer ✓
        </button>
        
        <p id="message"></p>
      </div>
    </div>
    
    <div style="text-align: center; margin-top: 20px;">
      <a href="dashboard.php" style="color: var(--text-secondary);">← Back to Dashboard</a>
    </div>
  </div>
  
  <div id="notification"></div>
</body>
</html>