<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./AdminHome.css" />
    <title>Admin Home</title>
</head>

<body>
    <div id="root">
        <div class="App">
            <div class="StaffLayout_wrapper__CegPk">
                <?php require_once "./ManagerHeader.php" ?>
                <div class="Manager_wrapper__vOYy">
                    <?php require_once "./ManagerMenu.php" ?>
                    <?php require_once "./TestQLTaiKhoan.php" ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>