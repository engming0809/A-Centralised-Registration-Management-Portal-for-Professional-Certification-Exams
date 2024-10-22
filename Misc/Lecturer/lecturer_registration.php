<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'lecturer') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swinburne University - Registration Overview</title>
    <link rel="stylesheet" href="../dashboard-simple/style/style.css">

</head>
<body>
    <header>
        <div class="logo">
            <img src="swinburne-logo.png" alt="Swinburne University of Technology">
        </div>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Services</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h1>Registration Overview</h1>
        
        <nav class="breadcrumb">
            <a href="#">Home</a> &gt;
            <a href="#">Certification Overview</a> &gt;
            <span>Registration Overview</span>
        </nav>
        
        <table>
            <thead>
                <tr>
                    <th>Certificate Name</th>
                    <th>Payment Invoice</th>
                    <th>Transaction Slip</th>
                    <th>Payment Receipt</th>
                    <th>Exam Confirmation Letter</th>
                    <th>Exam Results</th>
                    <th>Certificate</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Malware Analyst</td>
                    <td>
                        Available
                        <button>Download</button>
                    </td>
                    <td>
                        Submitted on 10/21/2024, 9:35AM
                        <button>Submit</button>
                    </td>
                    <td>
                        Available
                        <button>Download</button>
                    </td>
                    <td>
                        Available
                        <button>Download</button>
                    </td>
                    <td>Pass</td>
                    <td>
                        <button>Download</button>
                    </td>
                </tr>
                <tr>
                    <td>Nuclear Weapon Tester</td>
                    <td>
                        Available
                        <button>Download</button>
                    </td>
                    <td>
                        Not Submitted
                        <button>Submit</button>
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </main>

    <footer>
        <p>&copy; 2024 My Website. All rights reserved.</p>
    </footer>
</body>
</html>