<?php
// Start session
session_start();
ob_start();



// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'music_db';

// Define MusicManager class
class MusicManager
{
    private $conn;

    public function __construct($host, $username, $password, $dbname)
    {
        $this->conn = new mysqli($host, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }

    public function addMusic($name, $album, $browser, $ip)
    {
        $stmt = $this->conn->prepare("INSERT INTO music (name, album, browser, ip) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $album, $browser, $ip);
        $stmt->execute();
        $stmt->close();
    }

    public function getAllMusic()
    {
        $sql = "SELECT * FROM music";
        $result = $this->conn->query($sql);

        $musicList = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $musicList[] = $row;
            }
        }
        return $musicList;
    }

    public function deleteMusic($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM music WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    public function updateMusic($id, $name, $album)
    {
        $stmt = $this->conn->prepare("UPDATE music SET name = ?, album = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $album, $id);
        $stmt->execute();
        $stmt->close();
    }

    public function __destruct()
    {
        $this->conn->close();
    }
}

// Instantiate MusicManager
$musicManager = new MusicManager($host, $username, $password, $dbname);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $browser = $_SERVER['HTTP_USER_AGENT'];
    $ip = $_SERVER['REMOTE_ADDR'];

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $album = trim($_POST['album'] ?? '');

        if (!empty($name) && !empty($album)) {
            $musicManager->addMusic($name, $album, $browser, $ip);
        }
    } elseif ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        if ($id > 0) {
            $musicManager->deleteMusic($id);
        }
    } elseif ($action === 'edit') {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $album = trim($_POST['album'] ?? '');

        if ($id > 0 && !empty($name) && !empty($album)) {
            $musicManager->updateMusic($id, $name, $album);
        }
    }
}

// Retrieve music list
$musicList = $musicManager->getAllMusic();
$editMusic = null;

// Check if editing a specific row
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    foreach ($musicList as $music) {
        if ($music['id'] == $editId) {
            $editMusic = $music;
            break;
        }
    }
}
function setCookieValue($name, $value, $expireDays) {
    setcookie($name, $value, time() + (86400 * $expireDays), "/");
}

// Fungsi untuk mendapatkan cookie
function getCookieValue($name) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
}

// Fungsi untuk menghapus cookie
function deleteCookie($name) {
    setcookie($name, "", time() - 3600, "/");
}

// Menetapkan cookie secara manual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cookie_name']) && isset($_POST['cookie_value'])) {
    $cookieName = $_POST['cookie_name'];
    $cookieValue = $_POST['cookie_value'];
    setCookieValue($cookieName, $cookieValue, 7); // Cookie akan berlaku selama 7 hari
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Music List</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #F4EFEA; /* Earthtone base */
            color: #4A403A; /* Earthtone text */
        }
        header {
            background-color: #8C6A5A;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
        table {
            width: 90%;
            margin: 2rem auto;
            border-collapse: collapse;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #D6CDBA;
            padding: 0.75rem;
            text-align: center;
        }
        th {
            background-color: #A67C68;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #EDE5DA;
        }
        tr:hover {
            background-color: #D6CDBA;
        }
        button {
            background-color: #8C6A5A;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            margin: 1rem 0;
        }
        button:hover {
            background-color: #6E5044;
        }
        .cookie-banner {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #8C6A5A;
            color: white;
            text-align: center;
            padding: 1rem 0;
        }
        .cookie-banner button {
            margin-left: 1rem;
        }
        .add-music, .update-music {
            text-align: center;
            margin: 2rem 0;
        }
        .add-music input, .update-music {
            padding: 0.5rem;
            margin: 0.5rem;
            border: 1px solid #D6CDBA;
            border-radius: 4px;
        }
        .add-music button, .update-music button {
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>

    <form class="cookie-banner" method="POST" action="">
        <label for="cookie_name">Cookie Name:</label>
        <input type="text" name="cookie_name" id="cookie_name" required>
        <br>
        <label for="cookie_value">Cookie Value:</label>
        <input type="text" name="cookie_value" id="cookie_value" required>
        <br>
        <button type="submit">Set Cookie</button>
    </form>
    <script>
        // Contoh penyimpanan data ke localStorage
        document.getElementById('cookie_name').addEventListener('blur', function() {
            saveToLocalStorage('cookie_name', this.value);
        });

        document.getElementById('cookie_value').addEventListener('blur', function() {
            saveToLocalStorage('cookie_value', this.value);
        });
    </script>

    <h1 style="text-align:center;">Music List</h1>


    <form class="add-music" method="POST">
        <input type="hidden" name="action" value="add">
        <input type="text" name="name" placeholder="Song Name" required>
        <input type="text" name="album" placeholder="Album Name" required>
        <button type="submit">Add New Music</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>Album</th>
                <th>Operation</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($musicList as $index => $music): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($music['name']) ?></td>
                    <td><?= htmlspecialchars($music['album']) ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= $music['id'] ?>">
                            <button type="submit">Delete</button>
                        </form>
                        <form method="GET" style="display:inline;">
                            <input type="hidden" name="edit" value="<?= $music['id'] ?>">
                            <button type="submit">Edit</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if ($editMusic): ?>
        <form class="update-music" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= $editMusic['id'] ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($editMusic['name']) ?>" required>
            <input type="text" name="album" value="<?= htmlspecialchars($editMusic['album']) ?>" required>
            <button type="submit">Update Music</button>
        </form>
    <?php endif; ?>
</body>
</html>
