<?php

session_start();

if(!isset($_SESSION['user_code']))
{
    header("Location: login.php");
    exit();
}

include "db.php";

$userCode = $_SESSION['user_code'];
$search = "";

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string($conn, $_GET['search']);
}

$userSql =
"SELECT *
FROM users
WHERE user_code='$userCode'";

$userResult = $conn->query($userSql);
$user = $userResult->fetch_assoc();

if(isset($_GET['standard']))
{
    $_SESSION['selected_standard'] = $_GET['standard'];
}

if(isset($_SESSION['selected_standard']))
{
    $selectedStandard = $_SESSION['selected_standard'];
}
else
{
    $selectedStandard = $user['standard'];
}

$selectedSubject = "";
$selectedChapter = "";

if(isset($_GET['subject']))
{
    $selectedSubject = $_GET['subject'];
}

if(isset($_GET['chapter']))
{
    $selectedChapter = $_GET['chapter'];
}

$videos = [];

if($selectedSubject != "" && $selectedChapter != "")
{
    if($selectedChapter == "All")
    {
        $videoSql =
        "SELECT *
        FROM learning_videos
        WHERE standard='$selectedStandard'
        AND subject='$selectedSubject'
        ORDER BY chapter ASC";
    }
    else
    {
        $videoSql =
        "SELECT *
        FROM learning_videos
        WHERE standard='$selectedStandard'
        AND subject='$selectedSubject'
        AND chapter='$selectedChapter'
        ORDER BY chapter ASC";
    }

    $videoResult = $conn->query($videoSql);

    while($row = $videoResult->fetch_assoc())
    {
        $videos[] = $row;
    }
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

<title>Video Space Academy</title>

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
    background:
    radial-gradient(circle at 8% 10%, #ffeaf7 0, transparent 28%),
    radial-gradient(circle at 90% 8%, #dffaff 0, transparent 30%),
    radial-gradient(circle at 50% 100%, #f1e7ff 0, transparent 35%),
    linear-gradient(180deg, #fbfdff, #eef8ff);
    display: flex;
    color: #26345f;
}

.sidebar
{
    width: 260px;
    min-height: 100vh;
    background: linear-gradient(180deg, #6c63ff, #ff8ed4);
    color: white;
    padding: 25px 18px;
    position: fixed;
    overflow: hidden;
}

.sidebar::before
{
    content: "🪐";
    position: absolute;
    top: 95px;
    right: 18px;
    font-size: 34px;
}

.sidebar::after
{
    content: "🚀";
    position: absolute;
    bottom: 20px;
    left: 25px;
    font-size: 42px;
}

.logoBox
{
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 35px;
    position: relative;
    z-index: 2;
}

.logoBox img
{
    width: 55px;
    height: 55px;
    border-radius: 50%;
    background: white;
    padding: 5px;
    object-fit: contain;
}

.logoBox h2
{
    font-size: 22px;
}

.logoBox p
{
    font-size: 13px;
    color: #f7edff;
}

.nav a
{
    display: block;
    color: white;
    text-decoration: none;
    padding: 14px 18px;
    margin-bottom: 10px;
    border-radius: 25px;
    font-size: 16px;
    font-weight: bold;
    transition: 0.3s;
    position: relative;
    z-index: 2;
}

.nav a:hover,
.active
{
    background: linear-gradient(135deg, #52e5d8, #ffd93d);
    color: #3b0764;
    transform: translateX(5px);
}

.quoteBox
{
    margin-top: 40px;
    background: rgba(255,255,255,0.20);
    padding: 20px;
    border-radius: 25px;
    text-align: center;
    line-height: 1.6;
    border: 2px dashed rgba(255,255,255,0.55);
    position: relative;
    z-index: 2;
}

.main
{
    margin-left: 260px;
    width: calc(100% - 260px);
    padding: 30px 45px;
}

.topbar
{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.searchBox
{
    width: 450px;
    padding: 15px 25px;
    border-radius: 35px;
    border: 2px solid #d8f5ff;
    font-size: 15px;
    outline: none;
    box-shadow: 0 8px 20px rgba(82,229,216,0.15);
}

.profileMini
{
    display: flex;
    align-items: center;
    gap: 12px;
    background: white;
    padding: 10px 15px;
    border-radius: 25px;
    box-shadow: 0px 8px 20px rgba(0,0,0,0.08);
    border: 2px solid #d8f5ff;
}

.profileMini img
{
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.hero
{
    background:
    linear-gradient(135deg, #fff9d8, #ffeaf7, #e5fbff);
    padding: 40px;
    border-radius: 38px;
    margin-bottom: 25px;
    box-shadow: 0 12px 25px rgba(108,99,255,0.12);
    border: 3px dashed #ffd93d;
    position: relative;
    overflow: hidden;
}

.hero::before
{
    content: "🎥";
    position: absolute;
    top: 28px;
    right: 42px;
    font-size: 58px;
}

.hero::after
{
    content: "⭐ 🪐 🛸";
    position: absolute;
    bottom: 22px;
    right: 35px;
    font-size: 22px;
}

.hero h1
{
    color: #3b0764;
    font-size: 42px;
}

.hero p
{
    color: #344062;
    font-size: 20px;
    margin-top: 10px;
    line-height: 1.6;
}

.section
{
    background: white;
    padding: 30px;
    border-radius: 35px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    margin-bottom: 25px;
    border: 2px solid #e0f7ff;
}

.section h2
{
    color: #3b0764;
    margin-bottom: 20px;
}

.standardGrid
{
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 15px;
}

.standardCard
{
    padding: 20px;
    border-radius: 30px;
    text-align: center;
    text-decoration: none;
    color: #3b0764;
    border: 2px solid #d8f5ff;
    transition: 0.3s;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.standardCard:nth-child(1){background:#fff7cc;}
.standardCard:nth-child(2){background:#e0f7ff;}
.standardCard:nth-child(3){background:#ffeaf7;}
.standardCard:nth-child(4){background:#e8ffe8;}
.standardCard:nth-child(5){background:#f0e6ff;}
.standardCard:nth-child(6){background:#fff0d8;}

.standardCard:hover
{
    transform: translateY(-8px) scale(1.04);
    border-color: #52e5d8;
}

.activeStandard
{
    border-color: #6c63ff !important;
    background: #eef0ff !important;
    box-shadow: 0 0 18px rgba(108,99,255,0.25);
}

.standardCard h1
{
    color: #6c63ff;
    font-size: 34px;
}

.standardCard p
{
    font-weight: bold;
    margin-top: 5px;
}

.subjects
{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
}

.subjectCard
{
    padding: 35px;
    border-radius: 35px;
    border: 3px solid transparent;
    text-align: center;
    transition: 0.3s;
    background: white;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    position: relative;
    overflow: hidden;
}

.subjectCard:hover
{
    transform: translateY(-8px);
}

.subjectCard h3
{
    font-size: 30px;
    color: #3b0764;
    margin-bottom: 12px;
}

.subjectCard p
{
    color: #555;
    margin-bottom: 20px;
    line-height: 1.6;
}

.subjectCard a
{
    display: inline-block;
    padding: 13px 24px;
    color: white;
    border-radius: 25px;
    text-decoration: none;
    font-weight: bold;
    box-shadow: 0 8px 18px rgba(0,0,0,0.12);
}

.math
{
    border-top: 8px solid #6c63ff;
}

.math::after
{
    content: "🚀";
    position: absolute;
    right: 28px;
    top: 22px;
    font-size: 38px;
}

.math a
{
    background: linear-gradient(135deg, #52e5d8, #6c63ff);
}

.science
{
    border-top: 8px solid #52e5d8;
}

.science::after
{
    content: "🛸";
    position: absolute;
    right: 28px;
    top: 22px;
    font-size: 38px;
}

.science a
{
    background: linear-gradient(135deg, #52e5d8, #34d399);
}

.chapterGrid
{
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}

.chapterBtn
{
    display: block;
    text-align: center;
    padding: 20px;
    border-radius: 28px;
    text-decoration: none;
    color: #3b0764;
    border: 2px solid #d8f5ff;
    background: #fbfdff;
    transition: 0.3s;
    font-weight: bold;
    box-shadow: 0 8px 18px rgba(0,0,0,0.06);
}

.chapterBtn:hover
{
    transform: translateY(-8px);
    border-color: #52e5d8;
}

.activeChapter
{
    border-color: #6c63ff;
    background: #eef0ff;
}

.videoGrid
{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}

.chapterCard
{
    border: 2px solid #d8f5ff;
    padding: 25px;
    border-radius: 30px;
    background: white;
    transition: 0.3s;
    text-align: center;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

.chapterCard:hover
{
    transform: translateY(-8px);
    border-color: #52e5d8;
    box-shadow: 0 0 18px rgba(82,229,216,0.22);
}

.chapterCard h3
{
    color: #6c63ff;
    margin-bottom: 8px;
}

.chapterCard p
{
    color: #555;
    margin-bottom: 15px;
}

.chapterCard button
{
    padding: 12px 20px;
    border: none;
    border-radius: 25px;
    background: linear-gradient(135deg, #52e5d8, #6c63ff);
    color: white;
    cursor: pointer;
    font-weight: bold;
}

.videoBox
{
    display: none;
    background:
    linear-gradient(135deg, #fff9d8, #ffeaf7, #e5fbff);
    padding: 30px;
    border-radius: 35px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    text-align: center;
    border: 3px dashed #ffd93d;
}

.videoBox h2
{
    color: #3b0764;
}

video
{
    width: 70%;
    max-height: 430px;
    border-radius: 25px;
    background: black;
}

.backBtn
{
    margin-top: 20px;
    padding: 13px 24px;
    background: linear-gradient(135deg, #52e5d8, #6c63ff);
    color: white;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    font-weight: bold;
}

.noVideo
{
    text-align: center;
    color: #555;
    padding: 30px;
    font-size: 18px;
}

@media(max-width:900px)
{
    .standardGrid,
    .subjects,
    .chapterGrid,
    .videoGrid
    {
        grid-template-columns: 1fr;
    }

    .searchBox
    {
        width: 280px;
    }
}

</style>

</head>

<body>

<div class="sidebar">

    <div class="logoBox">
        <img src="Logo.png">

        <div>
            <h2>OneStepAhead</h2>
            <p>Learn • Practice • Achieve</p>
        </div>
    </div>

    <div class="nav">
        <a href="home.php">🏠 Home</a>
        <a href="worksheet.php">📝 Worksheet</a>
        <a href="video.php" class="active">🎥 Video Lessons</a>
        <a href="progress.php">📊 Progress</a>
        <a href="leaderboard.php">🏆 Leaderboard</a>
        <a href="profile.php">👤 Profile</a>
        <a href="certificate.php">📜 Certificate</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="quoteBox">
        🎥<br><br>
        Watch lessons,<br>
        explore galaxies,<br>
        and learn brightly! ✨
    </div>

</div>

<div class="main">

    <div class="topbar">

        <form method="GET" action="video.php">
    <input
    type="text"
    name="search"
    class="searchBox"
    placeholder="🔍 Search videos, topics, chapters..."
    value="<?php echo $search; ?>">
</form>

        <a href="profile.php" style="text-decoration:none;color:inherit;">

            <div class="profileMini">
                <img src="<?php echo $avatar; ?>">

                <div>
                    Hello, <b><?php echo $user['username']; ?> ⭐</b><br>
                    <small><?php echo $selectedStandard; ?> Explorer</small>
                </div>
            </div>

        </a>

    </div>
	<?php if($search != "") { ?>

<div class="section">

    <h2>🔍 Search Results for "<?php echo $search; ?>"</h2>

    <?php
    $searchSql =
    "SELECT *
    FROM learning_videos
    WHERE title LIKE '%$search%'
    OR subject LIKE '%$search%'
    OR standard LIKE '%$search%'
    OR chapter LIKE '%$search%'
    ORDER BY standard ASC, subject ASC, chapter ASC";

    $searchResult = $conn->query($searchSql);

    if($searchResult->num_rows > 0)
    {
        echo "<div class='videoGrid'>";

        while($row = $searchResult->fetch_assoc())
        {
            echo "<div class='chapterCard'>";
            echo "<h3>📺 ".$row['standard']." - ".$row['subject']."</h3>";
            echo "<p>Chapter ".$row['chapter'].": ".$row['title']."</p>";

            echo "<button onclick=\"playVideo('".$row['video_file']."','".$row['subject']." - Chapter ".$row['chapter'].": ".$row['title']."')\">";
            echo "▶ Watch Video";
            echo "</button>";

            echo "</div>";
        }

        echo "</div>";
    }
    else
    {
        echo "<div class='noVideo'>No videos found for this search 🪐</div>";
    }
    ?>

</div>

<?php } ?>

    <div class="hero">

        <h1>🎥 Space Video Academy</h1>

        <p>
            Choose your standard galaxy, select a subject mission, pick a chapter,
            and watch fun learning videos before answering quizzes.
        </p>

    </div>

    <div class="section">

        <h2>🪐 Step 1 : Choose Your Standard Galaxy</h2>

        <div class="standardGrid">

            <?php
            for($i = 1; $i <= 6; $i++)
            {
                $std = "Standard " . $i;
            ?>

            <a
            href="video.php?standard=<?php echo $std; ?>"
            class="standardCard <?php if($selectedStandard == $std){ echo 'activeStandard'; } ?>">

                <h1>🪐 <?php echo $i; ?></h1>
                <p><?php echo $std; ?></p>

            </a>

            <?php
            }
            ?>

        </div>

    </div>

    <div class="section">

        <h2>🚀 Step 2 : Choose Your Subject Mission</h2>

        <div class="subjects">

            <div class="subjectCard math">
                <h3>🧮 Math Galaxy Videos</h3>
                <p>
                    Watch Math videos about numbers, operations, patterns and problem solving.
                </p>

                <a href="video.php?standard=<?php echo $selectedStandard; ?>&subject=Math">
                    Launch Math Videos →
                </a>
            </div>

            <div class="subjectCard science">
                <h3>🔬 Science Space Videos</h3>
                <p>
                    Watch Science videos about living things, earth, weather and discoveries.
                </p>

                <a href="video.php?standard=<?php echo $selectedStandard; ?>&subject=Science">
                    Enter Science Videos →
                </a>
            </div>

        </div>

    </div>

    <?php if($selectedSubject != "") { ?>

    <div class="section">

        <h2>🌈 Step 3 : Choose Your Chapter</h2>

        <div class="chapterGrid">

            <a
            href="video.php?standard=<?php echo $selectedStandard; ?>&subject=<?php echo $selectedSubject; ?>&chapter=1"
            class="chapterBtn <?php if($selectedChapter == '1'){ echo 'activeChapter'; } ?>">
                📘 Chapter 1
            </a>

            <a
            href="video.php?standard=<?php echo $selectedStandard; ?>&subject=<?php echo $selectedSubject; ?>&chapter=2"
            class="chapterBtn <?php if($selectedChapter == '2'){ echo 'activeChapter'; } ?>">
                📗 Chapter 2
            </a>

            <a
            href="video.php?standard=<?php echo $selectedStandard; ?>&subject=<?php echo $selectedSubject; ?>&chapter=3"
            class="chapterBtn <?php if($selectedChapter == '3'){ echo 'activeChapter'; } ?>">
                📙 Chapter 3
            </a>

            <a
            href="video.php?standard=<?php echo $selectedStandard; ?>&subject=<?php echo $selectedSubject; ?>&chapter=All"
            class="chapterBtn <?php if($selectedChapter == 'All'){ echo 'activeChapter'; } ?>">
                🌟 Display All
            </a>

        </div>

    </div>

    <?php } ?>

    <?php if($selectedSubject != "" && $selectedChapter != "") { ?>

    <div class="section">

        <h2>
            🎬 Step 4 : Watch & Learn -
            <?php echo $selectedSubject; ?>

            <?php
            if($selectedChapter == "All")
            {
                echo " All Chapters";
            }
            else
            {
                echo " Chapter " . $selectedChapter;
            }
            ?>
        </h2>

        <?php if(count($videos) > 0) { ?>

        <div class="videoGrid">

            <?php
            foreach($videos as $video)
            {
            ?>

            <div class="chapterCard">

                <h3>
                    📺 Chapter <?php echo $video['chapter']; ?>
                </h3>

                <p>
                    <?php echo $video['title']; ?>
                </p>

                <button
                onclick="playVideo('<?php echo $video['video_file']; ?>','<?php echo $selectedSubject; ?> - Chapter <?php echo $video['chapter']; ?>: <?php echo $video['title']; ?>')">

                    ▶ Watch Video

                </button>

            </div>

            <?php
            }
            ?>

        </div>

        <?php } else { ?>

        <div class="noVideo">
            No videos available for this standard, subject, and chapter yet 🪐
        </div>

        <?php } ?>

    </div>

    <?php } ?>

    <div class="videoBox" id="videoBox">

        <h2 id="videoTitle">🎥 Video Lesson</h2>

        <br>

        <video id="videoPlayer" controls>
            <source id="videoSource" src="" type="video/mp4">
        </video>

        <br>

        <button class="backBtn" onclick="closeVideo()">
            Back to Chapter List
        </button>

    </div>

</div>

<script>

function playVideo(file, title)
{
    document.getElementById("videoTitle").innerHTML =
    title;

    document.getElementById("videoSource").src =
    file;

    document.getElementById("videoPlayer").load();

    document.getElementById("videoBox").style.display =
    "block";

    document.getElementById("videoBox").scrollIntoView
    ({
        behavior: "smooth"
    });
}

function closeVideo()
{
    document.getElementById("videoBox").style.display =
    "none";

    document.getElementById("videoPlayer").pause();
}

</script>

</body>
</html>