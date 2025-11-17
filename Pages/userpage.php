<?php
//session_start();
require_once __DIR__ . '/../ClassAutoLoad.php';
require_once __DIR__ . '/../DBConnection.php';

// Redirect if not logged in
/*if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}*/

//$user_id = $_SESSION['user_id'];
//$username = $_SESSION['username'] ?? "Guest User";
$current_date = date('D, d F Y');

// Default values
$profile_name = "Default Profile";
$profile_email = "default@strathmart.com";
$avatar_small = "https://i.pravatar.cc/30?img=1";
$avatar_large = "https://i.pravatar.cc/65?img=1";
$full_name_val = '';
$nick_name_val = '';
$gender_val = '';
$email_time_ago = 'Unknown time';

//$db = new Database();
//$conn = $db->connect();

// Fetch user data
/*$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $username = $row['username'];
    $profile_name = $row['full_name'];
    $nick_name_val = $row['nick_name'];
    $gender_val = $row['gender'];
    $profile_email = $row['email'];
    $avatar_large = $row['avatar_url'] ?: "https://i.pravatar.cc/65?img=1";
    $avatar_small = $row['avatar_url'] ?: "https://i.pravatar.cc/30?img=1";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['fullName'];
    $nick_name = $_POST['nickName'];
    $gender = $_POST['gender'];

    $sql_update = "UPDATE users SET full_name=?, nick_name=?, gender=? WHERE id=?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("sssi", $full_name, $nick_name, $gender, $user_id);
    $stmt->execute();

    echo "<script>alert('Profile updated successfully!'); window.location.href = 'profile.php';</script>";
    exit();
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StrathMart Profile</title>
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ======== BASIC STYLING (from your version) ======== */
        :root {
            --bg-light: #f4f6fa;
            --card-bg: #ffffff;
            --sidebar-bg: #ffffff;
            --primary-blue: #007bff;
            --text-dark: #333333;
            --text-medium: #777777;
            --border-light: #eeeeee;
            --input-bg: #f5f6fa;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); }
        .app-container { display: grid; grid-template-columns: 80px 1fr; min-height: 100vh; }
        .sidebar { background: var(--sidebar-bg); display: flex; flex-direction: column; align-items: center; padding-top: 20px; border-right: 1px solid var(--border-light); }
        .sidebar-icon { width: 48px; height: 48px; display: flex; justify-content: center; align-items: center; color: var(--text-medium); font-size: 1.2rem; margin-bottom: 20px; cursor: pointer; border-radius: 12px; transition: 0.2s; }
        .sidebar-icon.active { background: #e6f0ff; color: var(--primary-blue); }
        .content-wrapper { display: flex; flex-direction: column; }
        .app-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background: var(--card-bg); box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .profile-settings-card { background: var(--card-bg); margin: 30px; padding: 40px; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); min-height: 80vh; }
        .profile-header-details { display: flex; justify-content: space-between; align-items: center; background: linear-gradient(to right, #e6f0ff, #fff8e1); border-radius: 15px; padding: 20px; margin: 20px 0 30px 0; }
        .large-avatar { width: 65px; height: 65px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 0 2px var(--primary-blue); }
        .edit-button { background: var(--primary-blue); color: white; border: none; padding: 10px 25px; border-radius: 10px; font-weight: 600; cursor: pointer; }
        .edit-button:hover { background: #0056b3; }
        .profile-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; }
        .form-group input, .form-group select { width: 100%; padding: 12px 15px; border: 1px solid var(--border-light); border-radius: 8px; background: var(--input-bg); }
        .email-section { margin-top: 40px; border-top: 1px solid var(--border-light); padding-top: 20px; }
        @media (max-width: 1024px){ .app-container{grid-template-columns:1fr;} .sidebar{display:none;} .profile-form-grid{grid-template-columns:1fr;} }
    </style>
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="sidebar-icon active"><i class="fas fa-user"></i></div>
        <div class="sidebar-icon"><i class="fas fa-cog"></i></div>
        <div class="sidebar-icon bottom-icon" onclick="window.location.href='../logout.php'"><i class="fas fa-sign-out-alt"></i></div>
    </aside>

    <div class="content-wrapper">
        <header class="app-header">
            <h1 class="page-title">Profile Settings</h1>
            <div class="header-right">
                <div class="profile-avatar-small"><img src="<?php echo $avatar_small; ?>" alt="User Avatar"></div>
            </div>
        </header>

        <main class="profile-settings-card">
            <div class="welcome-section">
                <h2>Welcome</h2>
                <p><?php echo $current_date; ?></p>
            </div>

            <div class="profile-header-details">
                <div class="user-profile-summary">
                    <img src="<?php echo $avatar_large; ?>" alt="Avatar" class="large-avatar">
                    <div class="user-info">
                        <h3><?php echo htmlspecialchars($profile_name); ?></h3>
                        <p><?php echo htmlspecialchars($profile_email); ?></p>
                    </div>
                </div>
                <button class="edit-button" type="button" id="editToggle">Edit</button>
            </div>

            <form class="profile-form-grid" method="POST" action="">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($full_name_val); ?>" placeholder="Your Full Name" disabled>
                </div>

                <div class="form-group">
                    <label for="nickName">Nick Name</label>
                    <input type="text" id="nickName" name="nickName" value="<?php echo htmlspecialchars($nick_name_val); ?>" placeholder="Your Nick Name" disabled>
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" disabled>
                        <option value="">Select Gender</option>
                        <option value="Male" <?php echo ($gender_val == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($gender_val == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($gender_val == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group" style="grid-column:1/-1;">
                    <button type="submit" class="edit-button" id="saveBtn" style="display:none;">Save Changes</button>
                </div>
            </form>

            <div class="email-section">
                <h4>My Email Address</h4>
                <div class="email-item">
                    <i class="fas fa-envelope"></i>
                    <div class="email-info">
                        <p><?php echo htmlspecialchars($profile_email); ?></p>
                        <span><?php echo htmlspecialchars($email_time_ago); ?></span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
const editBtn = document.getElementById("editToggle");
const saveBtn = document.getElementById("saveBtn");
editBtn.addEventListener("click", () => {
    document.querySelectorAll("input, select").forEach(el => el.disabled = false);
    editBtn.style.display = "none";
    saveBtn.style.display = "inline-block";
});
</script>
</body>
</html>
