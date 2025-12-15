<?php
if (!eregi("setting.php", $_SERVER['PHP_SELF'])) {
    die("You can't access this file directly...");
}
?>
<div class="container mt-5">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Success!</strong> Your changes have been saved.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <a href="setting.php?modname=config" class="btn btn-primary">Return</a>
</div>
