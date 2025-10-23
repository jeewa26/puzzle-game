let timeLeft = 30;
let timerInterval;
let currentPuzzle = {};
let score = 0;

function startGame() {
    score = 0;
    updateScoreDisplay();

    // set time based on difficulty passed from PHP
    if (difficulty === "easy") {
        timeLeft = 60;
    } else if (difficulty === "medium") {
        timeLeft = 40;
    } else if (difficulty === "hard") {
        timeLeft = 20;
    }

    document.getElementById("timer").innerText = "Time: " + timeLeft;
    document.getElementById("message").innerText = "";
    getPuzzle();

    clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        timeLeft--;
        document.getElementById("timer").innerText = "Time: " + timeLeft;
        
        // Add warning color when time is low
        const timerEl = document.getElementById("timer");
        if (timeLeft <= 10) {
            timerEl.style.color = "var(--error)";
            timerEl.style.borderColor = "rgba(239, 68, 68, 0.5)";
            timerEl.style.background = "rgba(239, 68, 68, 0.1)";
        }
        
        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            endGame();
        }
    }, 1000);
}

function getPuzzle() {
    fetch("https://marcconrad.com/uob/banana/api.php")
        .then(res => res.json())
        .then(data => {
            currentPuzzle = data;
            document.getElementById("puzzle").innerHTML =
                `<img src="${data.question}" alt="puzzle" width="300">`;
        })
        .catch(err => {
            document.getElementById("puzzle").innerHTML = 
                "<p style='color: var(--error);'>⚠️ Failed to load puzzle. Please refresh the page.</p>";
            console.error(err);
        });
}

function submitAnswer() {
    let userAnswer = document.getElementById("answer").value;
    
    if (userAnswer === "") {
        showMessage("❌ Please enter an answer!", "error");
        return;
    }
    
    const messageEl = document.getElementById("message");
    
    if (userAnswer == currentPuzzle.solution) {
        score += 10;
        timeLeft += 5;
        updateScoreDisplay();
        
        messageEl.style.color = "var(--success)";
        messageEl.style.background = "rgba(16, 185, 129, 0.1)";
        messageEl.innerText = "✅ Correct! +10 points, +5 seconds";
        
        showNotification("✅ Correct Answer! +10 points", "success");
    } else {
        timeLeft -= 5;
        
        messageEl.style.color = "var(--error)";
        messageEl.style.background = "rgba(239, 68, 68, 0.1)";
        messageEl.innerText = "❌ Wrong! -5 seconds (Correct answer: " + currentPuzzle.solution + ")";
        
        showNotification("❌ Wrong Answer! -5 seconds", "error");
    }
    
    document.getElementById("answer").value = "";
    document.getElementById("answer").focus();
    getPuzzle();
}

function updateScoreDisplay() {
    document.getElementById("score-value").innerText = score;
}

function showNotification(message, type) {
    const notif = document.getElementById("notification");
    if (!notif) {
        const newNotif = document.createElement("div");
        newNotif.id = "notification";
        document.body.appendChild(newNotif);
    }
    
    const notification = document.getElementById("notification");
    notification.innerText = message;
    notification.className = "show";
    
    if (type === "success") {
        notification.style.background = "linear-gradient(135deg, #10b981 0%, #059669 100%)";
    } else if (type === "error") {
        notification.style.background = "linear-gradient(135deg, #ef4444 0%, #dc2626 100%)";
    }
    
    setTimeout(() => {
        notification.className = "";
    }, 3000);
}

function showMessage(message, type) {
    const messageEl = document.getElementById("message");
    messageEl.innerText = message;
    
    if (type === "error") {
        messageEl.style.color = "var(--error)";
        messageEl.style.background = "rgba(239, 68, 68, 0.1)";
    }
}

function endGame() {
    clearInterval(timerInterval);
    
    const finalMessage = `🎮 Game Over!\n\n` +
                        `Your Score: ${score}\n` +
                        `Difficulty: ${difficulty.charAt(0).toUpperCase() + difficulty.slice(1)}`;
    
    alert(finalMessage);

    fetch("save_score.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "score=" + score + "&difficulty=" + difficulty
    }).then(() => {
        window.location.href = "leadboard.php";
    }).catch(err => {
        console.error("Error saving score:", err);
        window.location.href = "leadboard.php";
    });
}

// Handle Enter key press on answer input
document.addEventListener("DOMContentLoaded", function() {
    const answerInput = document.getElementById("answer");
    if (answerInput) {
        answerInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                submitAnswer();
            }
        });
    }
});

// auto-start when page loads
window.onload = startGame;