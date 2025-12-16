<?php
if (session_id() == '') {
    session_start();
}

include_once("../include/lanai/class.system.php");
$sys_lanai = new Systems();

if (empty($_SESSION['lang'])) {
    require_once("language/lang-english.php");
} else {
    require_once("language/lang-" . $_SESSION['lang'] . ".php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Setup</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .setup-container {
            max-width: 800px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin: 60px auto;
            padding: 40px;
        }
        .setup-logo {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }
        .setup-footer {
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: auto;
            padding: 20px 0;
        }
    </style>
</head>
<body>

    <div class="container setup-container">
        <div class="setup-logo">
            <img src="images/logo.gif" alt="Setup Logo" class="img-fluid" style="max-height:80px;">
        </div>

        <div class="content">
            <?php
            if (empty($_REQUEST['step'])) {
                include_once("step_a.php");
            } else {
                if (file_exists("step_" . $_REQUEST['step'] . ".php")) {
                    include_once("step_" . $_REQUEST['step'] . ".php");
                } else {
                    ?>
                    <div class="alert alert-warning text-center" role="alert">
                        <img src="../theme/default/images/worning.gif" alt="Warning" class="me-2 align-middle">
                        <strong>ขออภัยไม่พบไฟล์ที่ใช้ในการติดตั้งละหน่ายซีเอ็มเอ็ส!</strong>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>

    <footer class="setup-footer">
        &reg; La Nai Content Management System.
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
