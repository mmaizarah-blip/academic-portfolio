<?php

session_start();

if(!isset($_SESSION['user_code']))
{
    header("Location: login.php");
    exit();
}

include "db.php";

$userCode = $_SESSION['user_code'];

$userSql =
"SELECT *
FROM users
WHERE user_code='$userCode'";

$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();

if(isset($_GET['standard']))
{
    $studentStandard = $_GET['standard'];
}
elseif(isset($_SESSION['selected_standard']))
{
    $studentStandard = $_SESSION['selected_standard'];
}
else
{
    $studentStandard = $user['standard'];
}

$sql =
"SELECT *
FROM worksheet_questions
WHERE standard='$studentStandard'
AND subject='Science'
ORDER BY question_id ASC";

$result = $conn->query($sql);

$questions = [];

while($row = $result->fetch_assoc())
{
    $questions[] = $row;
}

$avatar = "avatar.jpg";

if(isset($user['avatar']) && $user['avatar'] != "")
{
    $avatar = $user['avatar'];
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Science Space Lab Mission</title>

<style>

*
{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body
{
    font-family: 'Comic Sans MS', Arial, sans-serif;
    min-height: 100vh;
    background:
    linear-gradient(rgba(20, 20, 55, 0.45), rgba(20, 20, 55, 0.65)),
    url("galaxy.gif");
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    color: white;
    overflow-x: hidden;
}

.stars
{
    position: fixed;
    inset: 0;
    pointer-events: none;
    background-image:
    radial-gradient(white 1px, transparent 1px);
    background-size: 40px 40px;
    opacity: .35;
    z-index: 0;
}

.topbar
{
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 25px 45px;
    position: relative;
    z-index: 2;
}

.logoBox
{
    display: flex;
    align-items: center;
    gap: 14px;
    background: rgba(255,255,255,0.12);
    padding: 12px 18px;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.18);
}

.logoBox img
{
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: white;
    padding: 5px;
}

.logoBox h2
{
    font-size: 22px;
}

.logoBox small
{
    color: #dffcff;
}

.profileMini
{
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(255,255,255,0.15);
    padding: 10px 15px;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.18);
}

.profileMini img
{
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.profileMini small
{
    color: #dffcff;
}

.container
{
    max-width: 930px;
    margin: 20px auto;
    text-align: center;
    position: relative;
    z-index: 2;
}

.hero
{
    margin-bottom: 25px;
}

.hero h1
{
    font-size: 52px;
    margin-bottom: 10px;
    text-shadow: 0 5px 18px rgba(0,0,0,0.35);
}

.hero p
{
    font-size: 19px;
    color: #e8ffff;
}

.quizCard
{
    background: rgba(255,255,255,0.96);
    color: #1f2348;
    padding: 40px;
    border-radius: 35px;
    box-shadow:
    0 0 35px rgba(47,230,211,0.35),
    0 15px 35px rgba(0,0,0,0.25);
    position: relative;
    overflow: hidden;
    border: 3px solid rgba(47,230,211,0.7);
}

.quizCard::before
{
    content: "🧪";
    position: absolute;
    left: 25px;
    top: 22px;
    font-size: 28px;
}

.quizCard::after
{
    content: "🪐";
    position: absolute;
    left: 30px;
    bottom: 22px;
    font-size: 34px;
}

.rocket
{
    position: absolute;
    right: 25px;
    top: 20px;
    font-size: 58px;
    animation: floatRocket 3s ease-in-out infinite;
}

@keyframes floatRocket
{
    0%
    {
        transform: translateY(0px);
    }

    50%
    {
        transform: translateY(-12px);
    }

    100%
    {
        transform: translateY(0px);
    }
}

#points
{
    color: #6c63ff;
    font-weight: bold;
    margin-bottom: 8px;
    font-size: 18px;
}

#missionProgress
{
    color: #009e92;
    font-weight: bold;
    margin-bottom: 15px;
    font-size: 16px;
}

#question
{
    font-size: 30px;
    margin: 25px 0;
    color: #1f2348;
}

.answer
{
    display: block;
    width: 72%;
    margin: 13px auto;
    padding: 16px;
    border: 2px solid #dce6ff;
    border-radius: 25px;
    background:
    linear-gradient(135deg, #ffffff, #eef4ff);
    color: #1f2348;
    cursor: pointer;
    font-size: 17px;
    transition: .3s;
    font-weight: bold;
}

.answer:hover
{
    transform: translateY(-5px);
    border-color: #6c63ff;
    box-shadow: 0 10px 20px rgba(108,99,255,.25);
}

.correct
{
    background: linear-gradient(135deg, #caffbf, #9bf6ff) !important;
    border-color: #2fe6d3 !important;
}

.wrong
{
    background: linear-gradient(135deg, #ffd6e7, #ffe5ec) !important;
    opacity: 0.75;
}

#result
{
    margin-top: 20px;
    font-size: 24px;
    font-weight: bold;
}

.btn
{
    margin-top: 20px;
    padding: 14px 26px;
    border: none;
    border-radius: 25px;
    background: linear-gradient(135deg, #2fe6d3, #6c63ff);
    color: white;
    font-size: 16px;
    cursor: pointer;
    font-weight: bold;
    box-shadow: 0 10px 20px rgba(47,230,211,0.25);
    transition: .3s;
}

.btn:hover
{
    transform: translateY(-4px);
}

#nextBtn,
#helpBtn
{
    display: none;
}

.videoPopup
{
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(6,8,30,0.82);
    z-index: 99;
    justify-content: center;
    align-items: center;
    backdrop-filter: blur(5px);
}

.videoContent
{
    background:
    linear-gradient(135deg, #ffffff, #eefaff);
    color: #1f2348;
    width: 70%;
    padding: 30px;
    border-radius: 30px;
    text-align: center;
    position: relative;
    border: 3px solid #2fe6d3;
    box-shadow: 0 0 35px rgba(47,230,211,0.4);
}

.closeBtn
{
    position: absolute;
    right: 25px;
    top: 15px;
    font-size: 28px;
    color: #ff4d6d;
    cursor: pointer;
}

#noteText
{
    padding: 22px;
    background: #f5f7ff;
    border-radius: 20px;
    line-height: 1.8;
    font-size: 17px;
    text-align: left;
    min-height: 120px;
    border: 2px dashed #6c63ff;
}

.backHome
{
    display: inline-block;
    margin-top: 25px;
    color: white;
    text-decoration: none;
    background: rgba(255,255,255,0.18);
    padding: 13px 22px;
    border-radius: 25px;
    border: 2px solid rgba(255,255,255,0.2);
    backdrop-filter: blur(8px);
    font-weight: bold;
    transition: .3s;
}

.backHome:hover
{
    transform: translateY(-4px);
    background: rgba(255,255,255,0.28);
}

@media(max-width:900px)
{
    .topbar
    {
        flex-direction: column;
        gap: 18px;
    }

    .quizCard
    {
        margin: 0 15px;
    }

    .answer
    {
        width: 95%;
    }

    .videoContent
    {
        width: 90%;
    }
}

</style>

</head>

<body>

<div class="stars"></div>

<audio id="correctSound" src="correct.mp3"></audio>
<audio id="wrongSound" src="wrong.mp3"></audio>

<div class="topbar">

    <div class="logoBox">
        <img src="Logo.png">
        <div>
            <h2>OneStepAhead</h2>
            <small>🛸 Science Space Lab</small>
        </div>
    </div>

    <div class="profileMini">
        <img src="<?php echo $avatar; ?>">
        <div>
            <b><?php echo $user['username']; ?> ⭐</b><br>
            <small><?php echo $studentStandard; ?> Explorer</small>
        </div>
    </div>

</div>

<div class="container">

    <div class="hero">
        <h1>🛸 Science Space Lab</h1>
        <p>
            Complete every experiment and collect ⭐ Stars across the galaxy!
        </p>
    </div>

    <div class="quizCard" id="startPage">

        <div class="rocket">🛸</div>

        <h1>🧪 Ready For Experiment?</h1>

        <br>

        <p>
            Mission Subject:
            <b>Science</b>
        </p>

        <p>
            Standard:
            <b><?php echo $studentStandard; ?></b>
        </p>

        <p>
            Total Questions:
            <b><?php echo count($questions); ?></b>
        </p>

        <button class="btn" onclick="startQuiz()">
            🛸 Start Experiment
        </button>

    </div>

    <div class="quizCard" id="quizPage" style="display:none;">

        <div class="rocket">🪐</div>

        <div id="points">
            ⭐ Stars Collected: 0
        </div>

        <div id="missionProgress">
            🪐 Lab Progress: 1 / <?php echo count($questions); ?>
        </div>

        <h2 id="question"></h2>

        <div id="answers"></div>

        <div id="result"></div>

        <button class="btn" id="helpBtn" onclick="openHelp()">
            🛸 Lab Explanation
        </button>

        <br>

        <button class="btn" id="nextBtn" onclick="nextQuestion()">
            Next Question →
        </button>

    </div>

    <a href="worksheet.php" class="backHome">
        ← Back to Worksheet
    </a>

</div>

<div class="videoPopup" id="videoPopup">

    <div class="videoContent">

        <span class="closeBtn" onclick="closeHelp()">✖</span>

        <h2>🛸 Science Lab Explanation</h2>

        <br>

        <div id="noteText">
            Lab explanation will appear here...
        </div>

    </div>

</div>

<script>

let questions =
<?php echo json_encode($questions); ?>;

let currentQuestion = 0;
let totalPoints = 0;
let totalAttempt = 0;
let totalCorrect = 0;

function startQuiz()
{
    if(questions.length == 0)
    {
        alert("No Science questions available for your standard yet.");
        return;
    }

    document.getElementById("startPage").style.display = "none";
    document.getElementById("quizPage").style.display = "block";

    loadQuestion();
}

function loadQuestion()
{
    let q = questions[currentQuestion];

    document.getElementById("missionProgress").innerHTML =
    "🪐 Lab Progress: " +
    (currentQuestion + 1) +
    " / " +
    questions.length;

    document.getElementById("question").innerHTML =
    q.question_text;

    let answers =
    document.getElementById("answers");

    answers.innerHTML = "";

    let options =
    [
        ["A", q.option_a],
        ["B", q.option_b],
        ["C", q.option_c],
        ["D", q.option_d]
    ];

    for(let i = 0; i < options.length; i++)
    {
        answers.innerHTML +=
        "<button class='answer' onclick=\"checkAnswer(this, '" +
        options[i][0] +
        "')\">" +
        options[i][1] +
        "</button>";
    }

    document.getElementById("result").innerHTML = "";
    document.getElementById("nextBtn").style.display = "none";
    document.getElementById("helpBtn").style.display = "none";
}

function checkAnswer(button, answer)
{
    let q = questions[currentQuestion];

    let buttons =
    document.querySelectorAll(".answer");

    buttons.forEach(function(btn)
    {
        btn.disabled = true;

        if(btn.innerHTML == q["option_" + q.correct_answer.toLowerCase()])
        {
            btn.classList.add("correct");
        }
        else
        {
            btn.classList.add("wrong");
        }
    });

    totalAttempt++;

    if(answer == q.correct_answer)
    {
        document.getElementById("result").innerHTML =
        "🧪 Experiment Success! +500 Stars Collected!";

        document.getElementById("result").style.color =
        "#009e92";

        document.getElementById("correctSound").play();

        totalPoints += 500;
        totalCorrect++;
    }
    else
    {
        document.getElementById("result").innerHTML =
        "☄️ Lab Alert! Check the Science Lab Explanation.";

        document.getElementById("result").style.color =
        "#ff4d6d";

        document.getElementById("wrongSound").play();
    }

    document.getElementById("points").innerHTML =
    "⭐ Stars Collected: " + totalPoints;

    document.getElementById("helpBtn").style.display = "inline-block";
    document.getElementById("nextBtn").style.display = "inline-block";
}

function openHelp()
{
    let q = questions[currentQuestion];

    if(q.explanation == "" || q.explanation == null)
    {
        document.getElementById("noteText").innerHTML =
        "No lab explanation has been added for this question yet.";
    }
    else
    {
        document.getElementById("noteText").innerHTML =
        q.explanation;
    }

    document.getElementById("videoPopup").style.display =
    "flex";
}

function closeHelp()
{
    document.getElementById("videoPopup").style.display =
    "none";
}

function nextQuestion()
{
    currentQuestion++;

    if(currentQuestion >= questions.length)
    {
        saveScore();
        return;
    }

    loadQuestion();
}

function saveScore()
{
    fetch("save_score.php",
    {
        method: "POST",
        headers:
        {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
        "points=" + totalPoints +
        "&attempt=" + totalAttempt +
        "&correct=" + totalCorrect +
        "&standard=<?php echo $studentStandard; ?>" +
        "&subject=Science"
    })
    .then(response => response.text())
    .then(data =>
    {
        let accuracy = 0;

        if(totalAttempt > 0)
        {
            accuracy = Math.round((totalCorrect / totalAttempt) * 100);
        }

        document.getElementById("quizPage").innerHTML =
        `
        <div style="padding:40px">
            <h1 style="font-size:50px">🎉 Mission Complete!</h1>
            <br>
            <div style="font-size:80px">🚀⭐🪐</div>
            <br>
            <h2>Stars Collected: ${totalPoints}</h2>
            <br>
            <p>Mission Accuracy: ${accuracy}%</p>
            <p>Correct Answers: ${totalCorrect}/${totalAttempt}</p>
            <br>
            <button class="btn" onclick="window.location.href='progress.php'">
                📊 View Mission Report
            </button>
            <br><br>
            <button class="btn" onclick="window.location.href='worksheet.php'">
                🚀 New Mission
            </button>
        </div>
        `;
    });
}

</script>

</body>
</html>