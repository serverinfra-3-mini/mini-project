<?php
// Configuration (DB connection settings from docker-compose.yml)
// **중요: DB_HOST는 컨테이너 이름인 'db'를 사용해야 합니다.**
define('DB_HOST', 'db');
define('DB_USER', 'root');
define('DB_PASS', 'your_strong_password!'); // 설정한 비밀번호로 변경
define('DB_NAME', 'blog_data');

// Establish Connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    // DB 연결 실패 시 에러 메시지 출력
    die("Connection failed: " . $conn->connect_error . "<br>DB 설정이 올바른지 확인해주세요.");
}

// --- 1. POST Request Handling (Handling new posts) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    $sql = "INSERT INTO posts (title, content, created_at) VALUES ('$title', '$content', NOW())";
    
    if ($conn->query($sql) === TRUE) {
        // Redirect to prevent duplicate submission on refresh
        header("Location: index.php"); 
        exit();
    } else {
        echo "<p style='color:red;'>게시글 저장 오류: " . $conn->error . "</p>";
    }
}

// --- 2. Create Table if Not Exists ---
// posts 테이블이 없으면 생성합니다. (초기 1회만 실행됨)
$sql_check_table = "CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    created_at DATETIME
)";
if (!$conn->query($sql_check_table)) {
    echo "<p style='color:red;'>테이블 생성 오류: " . $conn->error . "</p>";
}

// --- 3. HTML Output Start ---
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>간단 Docker 게시판</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4; }
        .container { max-width: 800px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; color: #333; }
        form { margin-bottom: 30px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background: #fafafa; }
        input[type="text"], textarea { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        textarea { resize: vertical; height: 80px; }
        button { background-color: #5cb85c; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; float: right; }
        button:hover { background-color: #4cae4c; }
        .post-list { border-top: 2px solid #333; margin-top: 20px; }
        .post-item { padding: 10px 0; border-bottom: 1px dashed #ccc; }
        .post-title { font-weight: bold; font-size: 1.1em; color: #007bff; }
        .post-content { color: #666; margin-top: 5px; }
        .post-meta { font-size: 0.8em; color: #999; text-align: right; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Docker & MySQL 연동 게시판 (PHP)</h1>
        
        <!-- 게시글 작성 폼 -->
        <form method="POST">
            <h3>새 게시글 작성</h3>
            <input type="text" name="title" placeholder="제목을 입력하세요" required>
            <textarea name="content" placeholder="내용을 입력하세요" required></textarea>
            <button type="submit">작성하기</button>
            <div style="clear:both;"></div>
        </form>

        <!-- 게시글 목록 -->
        <div class="post-list">
            <h2>게시글 목록</h2>
            <?php
            // 게시글 조회
            $result = $conn->query("SELECT id, title, content, created_at FROM posts ORDER BY created_at DESC");

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<div class='post-item'>";
                    echo "<div class='post-title'>" . htmlspecialchars($row["title"]) . "</div>";
                    echo "<div class='post-content'>" . htmlspecialchars($row["content"]) . "</div>";
                    echo "<div class='post-meta'>작성일: " . $row["created_at"] . "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>아직 작성된 게시글이 없습니다.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>

