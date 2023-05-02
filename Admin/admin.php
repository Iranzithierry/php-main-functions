<?php

session_start();

include("../DB/conn.php");
if (isset($_SESSION['username']) && $_SESSION['admin_permission'] = true) {
} else {
    header("location:admin_login.php");
}
// somewhere early in your project's loading, require the Composer autoloader
// see: http://getcomposer.org/doc/00-intro.md
require '../vendor/autoload.php';
// include autoloader
// require_once '../dompdf/autoload.inc.php';
$stmt = $conn->prepare("SELECT * FROM users ORDER BY joined_at ASC");

$stmt->execute();

$result = $stmt->get_result();

$stmt = $conn->prepare("SELECT * FROM  recycle_bin ORDER BY deleted_at DESC");

$stmt->execute();

$result_deleted = $stmt->get_result();
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/style-admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css" />


</head>

<body>
    <div class="container" id="all_table">
        <div class="alert">
            <?php if (isset($_GET['done'])) { ?>
                <div class="done" role="alert">
                    <?php echo htmlspecialchars($_GET['done']); ?>
                </div>
            <?php } ?>
            <?php if (isset($_GET['error'])) { ?>
                <div class="error" role="alert">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php } ?>
        </div>
        <button class="btn-success section active_bar" name="status">Active Users</button>
        <div class="table">
            <div id="table-container">
                <table>
                    <thead>
                        <tr class="active_bar_table_hr">
                            <th>SELECT</th>
                            <th>ID</th>
                            <th>FIRST NAME</th>
                            <th>SECOND NAME</th>
                            <th>MOBILE NUMBER</th>
                            <th>EMAIL</th>
                            <th>USERNAME</th>
                            <th>COUNTRY</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <?php echo "<form method='post' action='delete_selected.php' id='form'>"; ?>
                                <td><?php echo '<input type="checkbox" name="delete[]" value="' . $row["id"] . '">'; ?></td>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['fname']; ?></td>
                                <td><?php echo $row['sname']; ?></td>
                                <td><?php echo $row['mobile_number']; ?></td>
                                <td class="email"><?php echo $row['email']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['country']; ?></td>
                                <td class="btns">
                                    <?php echo '<a href="edit.php?id=' . $row['id'] . ' target="_blank""><button class ="btn btn-success table-btn action_btn">Edit</button></a>'; ?>
                                    <?php echo '<button class ="btn btn-danger table-btn action_btn" onclick="showConfirmation(' . $row['id'] . ')">Delete</button>'; ?>
                                    <?php if ($row['banned_until'] !== NULL && $row['banned_until'] >= date('Y-m-d H:i:s')) { ?>
                                        <?php echo '<button class ="btn btn-danger table-btn action_btn" name="status">Banned</button>'; ?>
                                        <?php echo '<a href="unban.php?id=' . $row['id'] . '"><button class ="btn btn-success table-btn action_btn">Unban</button></a>'; ?>
                                    <?php  } else { ?>
                                        <?php echo '<a href="ban.php?id=' . $row['id'] . '&duration=forever"><button class ="btn btn-danger table-btn action_btn">Ban Forever </button></a>'; ?>
                                        <?php echo '<a href="ban.php?id=' . $row['id'] . '&duration=1month"><button class ="btn btn-warning table-btn action_btn">Ban 1 month</button></a>'; ?>
                                    <?php } ?>
                                </td>
                            </tr>
                            <div id="popup-<?php echo $row['id']; ?>" class="pop-up-menu">
                                <div class="text-area">
                                    <p>Are You Sure You Want To Delete This User?</p>
                                </div>
                                <div class="button-area">
                                    <button class="btn btn-danger action_btn" onclick="deleteUser(<?php echo $row['id']; ?>)">Yes</button>
                                    <button class="btn btn-success action_btn" onclick="hide(<?php echo $row['id']; ?>)">No</button>
                                </div>
                            </div>

                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php echo '<button class="btn_bottom-action btn btn-danger type="submit"">&nbsp;DELETE SELECTED</button></a>' ?>
            <?php echo "</form>"; ?>
            <button class="btn-danger section deleted_bar" title="Hide Active Users">Deleted Users</button>
            <div class="email_send">
                <div id="data"></div>
                <div class="content">
                    <div class="erro_text">

                    </div>
                    <div class="input_field">
                        <span class="close" onclick="closebtn()"><i class="fa-solid fa-xmark"></i></span>
                        <form action="send_mail.php" method="post">
                            <div class="radios">
                                <div class="radio" for="active">
                                    <label for="active">ACTIVE</label>
                                    <input type="checkbox" name="active" id="active">
                                </div>
                                <div class="radio banned" for="banned">
                                    <label for="banned">BANNED</label>
                                    <input type="checkbox" name="banned" id="banned">
                                </div>
                            </div>
                            <textarea name="message" id="message" cols="30" rows="10" placeholder="Enter Message You Want To Send To The Selected E-Mail"></textarea>
                            <button class="btn btn-success" type="submit" id="send">SEND</button>
                        </form>
                        <!-- <script>
                            $(document).ready(function() {
                                $('#send').click(function() {
                                    $.ajax({
                                        type: "POST",
                                        url: 'send_mail_php';
                                        data: {
                                            active: $('#active').val(),
                                            banned: $('#banned').val(),
                                            message: $('#message').val();
                                        };
                                        success: function(data) {
                                            $('#data').html(data);
                                        }
                                    });
                                });
                            });
                        </script> -->
                    </div>
                </div>
            </div>
            <!-- <script>
                function print() {
                    var x = document.getElementsByClassName('btn-success');
                    for (var i = 0; i < x.length; i++) {
                        x[i].style.display = 'none';
                    }
                    var x = document.getElementsByClassName('btn-danger');
                    for (var i = 0; i < x.length; i++) {
                        x[i].style.display = 'none';
                    }
                    var x = document.getElementsByClassName('btn-warning');
                    for (var i = 0; i < x.length; i++) {
                        x[i].style.display = 'none';
                    }
                }
            </script> -->
            <div id="table_deleted">
                <table>
                    <thead>
                        <tr class="deleted_bar_table_hr">
                            <th>ID</th>
                            <th>FIRST NAME</th>
                            <th>SECOND NAME</th>
                            <th>MOBILE NUMBER</th>
                            <th>EMAIL</th>
                            <th>USERNAME</th>
                            <th>COUNTRY</th>
                            <th>DELETED AT</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_deleted->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['fname']; ?></td>
                                <td><?php echo $row['sname']; ?></td>
                                <td><?php echo $row['mobile_number']; ?></td>
                                <td class="email"><?php echo $row['email']; ?></td>
                                <td><?php echo $row['username']; ?></td>
                                <td><?php echo $row['country']; ?></td>
                                <td><?php echo $row['deleted_at']; ?></td>
                                <td class="btns forever">
                                    <?php echo '<a href="restore.php?id=' . $row['id'] . '"><button class ="btn btn-success table-btn-deleted">Restore</button></a>'; ?>
                                    <?php echo '<button class ="btn btn-danger table-btn-deleted" onclick="delete_forever(' . $row['id'] . ')">Delete Forever</button>'; ?>

                                </td>
                            </tr>
                            <div id="popup-<?php echo $row['id']; ?>-forever" class="pop-up-menu">
                                <div class="text-area">
                                    <p>Are You Sure You Want To Delete This User? &nbsp;<u><b>FOREVER</b></u></p>
                                </div>
                                <div class="button-area">
                                    <button class="btn btn-danger action_btn" onclick="deleteUserForever(<?php echo $row['id']; ?>)">Yes</button>
                                    <button class="btn btn-success action_btn" onclick="hide_forever(<?php echo $row['id']; ?>)">No</button>
                                </div>
                            </div>

                        <?php  } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="bottom-action">

        <button class="btn_bottom-action btn-success" onclick="printTable()"><i class="fa-solid fa-print"></i>&nbsp;PRINT ACTIVE USERS TABLE</button>
        <button class="btn_bottom-action btn-success" onclick="printTable_deleted()"><i class="fa-solid fa-print"></i>&nbsp;PRINT DELETED USERS TABLE</button>
        <button class="btn_bottom-action btn-success" onclick="printTableall()"><i class="fa-solid fa-print"></i>&nbsp;PRINT ALL TABLE</button>
        <button class="btn_bottom-action btn-success" onclick="email()"><i class="fa-solid fa-paper-plane"></i>&nbsp;<i class="fa-solid fa-envelope"></i>&nbsp;SEND EMAIL</button>
        <a href="statistics.php"><button class="btn_bottom-action btn-success"></i>&nbsp;&nbsp;STATISTICS</button></a>
        <a href="admin_logout.php"><button class="btn_bottom-action btn btn-danger"><i class="fa-solid fa-right-from-bracket"></i>&nbsp;Logout</button></a>



    </div>
    <script src="../SCRIPT/script_admin.js"></script>
</body>