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
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

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
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }
        .setup-logo .brand {
            font-size: 2.25rem;
            font-weight: 700;
            line-height: 1;
        }
        .setup-logo .brand .brand-lanai {
            color: #fd7e14;
        }
        .setup-logo .brand .brand-cms {
            color: #212529;
        }
        .setup-logo .version {
            margin-top: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #6c757d;
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
            <div class="brand"><span class="brand-lanai">LANAI</span> <span class="brand-cms">CMS</span></div>
            <div class="version">Version 3.2a</div>
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
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
