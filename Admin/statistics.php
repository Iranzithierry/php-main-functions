<?php

session_start();

include "../DB/conn.php";
if (isset($_SESSION['username']) && $_SESSION['admin_permission'] == true) {
} else {
    header("location: admin_login.php");
}
$stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM users");
$stmt->execute();
$result = $stmt->get_result();
while($row = $result->fetch_assoc()) {
    $total_users = $row['total_users'];
}
$stmt_in_january = $conn->prepare("SELECT COUNT(*) as january FROM users WHERE MONTH(joined_at) = 1");
$stmt_in_january->execute();
$result_in_january = $stmt_in_january->get_result();
$row_in_january = $result_in_january->fetch_assoc();
$users_in_january = $row_in_january['january'];

$stmt_in_february = $conn->prepare("SELECT COUNT(*) as february FROM users WHERE MONTH(joined_at) = 2");
$stmt_in_february->execute();
$result_in_february = $stmt_in_february->get_result();
$row_in_february = $result_in_february->fetch_assoc();
$users_in_february = $row_in_february['february'];

$stmt_in_march = $conn->prepare("SELECT COUNT(*) as march FROM users WHERE MONTH(joined_at) = 3");
$stmt_in_march->execute();
$result_in_march = $stmt_in_march->get_result();
$row_in_march = $result_in_march->fetch_assoc();
$users_in_march = $row_in_march['march'];

$stmt_in_april = $conn->prepare("SELECT COUNT(*) as april FROM users WHERE MONTH(joined_at) = 4");
$stmt_in_april->execute();
$result_in_april = $stmt_in_april->get_result();
$row_in_april = $result_in_april->fetch_assoc();
$users_in_april = $row_in_april['april'];

$stmt_in_may = $conn->prepare("SELECT COUNT(*) as may FROM users WHERE MONTH(joined_at) = 5");
$stmt_in_may->execute();
$result_in_may = $stmt_in_may->get_result();
$row_in_may = $result_in_may->fetch_assoc();
$users_in_may = $row_in_may['may'];

$stmt_in_june = $conn->prepare("SELECT COUNT(*) as june FROM users WHERE MONTH(joined_at) = 6");
$stmt_in_june->execute();
$result_in_june = $stmt_in_june->get_result();
$row_in_june = $result_in_june->fetch_assoc();
$users_in_june = $row_in_june['june'];

$stmt_in_july = $conn->prepare("SELECT COUNT(*) as july FROM users WHERE MONTH(joined_at) = 7");
$stmt_in_july->execute();
$result_in_july = $stmt_in_july->get_result();
$row_in_july = $result_in_july->fetch_assoc();
$users_in_july = $row_in_july['july'];

$stmt_in_august = $conn->prepare("SELECT COUNT(*) as august FROM users WHERE MONTH(joined_at) = 8");
$stmt_in_august->execute();
$result_in_august = $stmt_in_august->get_result();
$row_in_august = $result_in_august->fetch_assoc();
$users_in_august = $row_in_august['august'];

$stmt_in_september = $conn->prepare("SELECT COUNT(*) as september FROM users WHERE MONTH(joined_at) = 9");
$stmt_in_september->execute();
$result_in_september = $stmt_in_september->get_result();
$row_in_september = $result_in_september->fetch_assoc();
$users_in_september = $row_in_september['september'];

$stmt_in_october = $conn->prepare("SELECT COUNT(*) as october FROM users WHERE MONTH(joined_at) = 10");
$stmt_in_october->execute();
$result_in_october = $stmt_in_october->get_result();
$row_in_october = $result_in_october->fetch_assoc();
$users_in_october = $row_in_october['october'];

$stmt_in_november = $conn->prepare("SELECT COUNT(*) as november FROM users WHERE MONTH(joined_at) = 11");
$stmt_in_november->execute();
$result_in_november = $stmt_in_november->get_result();
$row_in_november = $result_in_november->fetch_assoc();
$users_in_november = $row_in_november['november'];

$stmt_in_december = $conn->prepare("SELECT COUNT(*) as december FROM users WHERE MONTH(joined_at) = 12");
$stmt_in_december->execute();
$result_in_december = $stmt_in_december->get_result();
$row_in_december = $result_in_december->fetch_assoc();
$users_in_december = $row_in_december['december'];

?>


<h1>Total users:<?php  echo $total_users?></h1>

<style></style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.2.1/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="../FONT/font.css">
<div class="bar-chart">
    <canvas id="myChart" height="220px" width="660px"></canvas>
</div>


<script id="rendered-js">
    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'USERS',
                data: [<?php echo $users_in_january; ?>, <?php echo $users_in_february; ?>, <?php echo $users_in_march; ?>, <?php echo $users_in_april; ?>, <?php echo $users_in_may; ?>, <?php echo $users_in_june; ?>, <?php echo $users_in_july; ?>, <?php echo $users_in_august; ?>, <?php echo $users_in_september; ?>, <?php echo $users_in_october; ?>, <?php echo $users_in_november ?>, <?php echo $users_in_december; ?>],
                borderWidth: 1,
                borderRadius: 30,
                barThickness: 12,
                backgroundColor: [
                    'rgba(114, 92, 255, 1)'
                ],

                borderColor: [
                    'rgba(114, 92, 255, 1)'
                ],

                hoverBackgroundColor: [
                    'rgba(28, 30, 35, 1)'
                ],

                hoverBorderColor: [
                    'rgba(28, 30, 35, 1)'
                ]
            }]
        },



        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value, index, ticks) {
                            return 'Joined ' + value + ' Users';
                        },
                        stepSize: 5
                    }
                },


                x: {
                    grid: {
                        display: false
                    }
                }
            },



            plugins: {
                legend: {
                    display: false,
                    labels: {
                        font: {
                            size: 12,
                            family: "'Poppins', sans-serif",
                            lineHeight: 18,
                            weight: 600
                        }
                    }
                }
            }
        }
    });
</script>