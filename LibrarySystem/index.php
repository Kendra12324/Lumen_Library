<?php
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/model/User.php";

$db = new Database();
$user = new User($db);
$loginError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($user->login($username, $password)) {
        echo "<script>alert('Login Successful! Redirecting...'); window.location='Dashboard.php';</script>";
        exit;
    } else {
        $loginError = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumen Lore Library</title>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>

<header>
    <div class="header-bar-top"></div>
    <div class="header-bar-middle">
        <div class="menu-icon">&#9776;</div>
        <h1 class="library-title">Lumen Lore Library</h1>
    </div>
    <div class="header-bar-bottom"></div>
</header>

<div class="sub-header">
    <div class="sub-header-left">
        <span class="welcome-text-subheader">Welcome to Lumen Lore Library <br>where stories come alive.</span>
    </div>
    <div class="nav-search-wrapper">
        <nav class="main-navigation">
            <ul>
                <li><a href="#">Books</a></li>
                <li><a href="#">Information</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="view/register.php">Register</a></li>
                <li><a href="view/login.php">Log in</a></li>

            </ul>
        </nav>
        <div class="search-container">
        <form action="search.php" method="GET" class="hero-search-box">
            <input type="text" name="search" placeholder="Search by title, author, or ISBN..." required>
            <button type="submit">SEARCH</button>
        </form>
    </div>
    </div>
</div>

<main>
    
    <section class="forest-section">
        <div class="forest-overlay">
            <i class="fas fa-tree fa-3x"></i>
            <blockquote>
                "A library is not a luxury but one of the necessities of life.<br>
                It is a forest where ideas grow."
            </blockquote>
            <cite>- Henry Ward Beecher</cite>
        </div>
    </section>




    <section class="announcement-section">
        <h2 class="section-title">
            <i class="fas fa-magic" style="color:#00bcd4;"></i> Latest Chronicles
        </h2>
        <p class="section-subtitle">Important updates from the grand library archives</p>

        <div class="announcement-grid">
            
            <div class="news-card important">
                <div class="card-icon"><i class="fas fa-calendar-times"></i></div>
                <div class="card-content">
                    <span class="news-date">Dec 20 - Jan 5</span>
                    <h3>Holiday Break</h3>
                    <p>The library gates will be sealed for the festive season. Online borrowing remains active.</p>
                </div>
            </div>

            <div class="news-card">
                <div class="card-icon"><i class="fas fa-wifi"></i></div>
                <div class="card-content">
                    <span class="news-date">Dec 10 • 10 PM</span>
                    <h3>System Upgrade</h3>
                    <p>We are polishing our crystal servers. Expect brief interruptions during the witching hour.</p>
                </div>
            </div>

            <div class="news-card">
                <div class="card-icon"><i class="fas fa-book-sparkles"></i></div>
                <div class="card-content">
                    <span class="news-date">New Arrival</span>
                    <h3>Harry Potter Vol. 1</h3>
                    <p>The illustrated edition has arrived! Reserve your copy before it vanishes.</p>
                </div>
            </div>

            <div class="news-card">
                <div class="card-icon"><i class="fas fa-users"></i></div>
                <div class="card-content">
                    <span class="news-date">Every Friday</span>
                    <h3>Reading Club</h3>
                    <p>Join fellow wizards and scholars at the Grand Hall for our weekly reading session.</p>
                </div>
            </div>

        </div>
    </section>






    <section class="section-container">
        <h2 class="section-title">Frequently Asked Questions</h2>
        
        <div class="faq-container">
            
            <div class="faq-item">
                <button class="faq-question">
                    <span>How many books can I borrow?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Students can borrow up to <strong>3 books</strong> Every Semester. Teachers can borrow unlimited books for 14 days.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>What happens if I return a book late?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>A penalty fee of <strong>₱50.00 per</strong> will be applied to your account for every overdue book. You must settle your fines before borrowing again.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>Can I reserve a book online?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Yes! Simply log in to your account, browse our collection, and click the <strong>"Reserve"</strong> button. You have 24 hours to pick up the book.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>What if I lost a book?</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <p>Please report it to the Librarian immediately. You will be required to replace the book with the same edition or pay the full price of the book plus a processing fee.</p>
                </div>
            </div>

        </div>
    </section>


    
    
</main>



<footer>
    <p>&copy; 2025 Lumen Lore Library. All Rights Reserved.</p>
</footer>



<script>
        const questions = document.querySelectorAll(".faq-question");

        questions.forEach(question => {
            question.addEventListener("click", function() {
                
                this.classList.toggle("active");

                const answer = this.nextElementSibling;
                if (answer.style.maxHeight) {
                    answer.style.maxHeight = null; // Close
                } else {
                    answer.style.maxHeight = answer.scrollHeight + "px"; // Open
                }
            });
        });
    </script>


</body>
</html>
