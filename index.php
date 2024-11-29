<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMBC Campus Food Review</title>
    <link rel="stylesheet" href="styles.css">
    <script src="scripts.js" defer></script>
    <script src="dom-manipulation.js" defer></script>

</head>
<body>
    <?php
    session_start(); //Starts a new session 

     //Connect to the UMBC databae
    $db = mysqli_connect("studentdb-maria.gl.umbc.edu", "leont1", "leont1", "leont1");
    if (!$db) {
        die("Connection failed: " . mysqli_connect_error()); //If the database does not connect
    }

    $message = ''; // To store the message to be displayed

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $restaurant = $db->real_escape_string($_POST['restaurant']);
        $reviewer_name = $db->real_escape_string($_POST['reviewer_name']);
        $review_text = $db->real_escape_string($_POST['review_text']);
        $rating = intval($_POST['rating']); // Convert the rating to integer

        $query = "INSERT INTO reviews (restaurant_id, reviewer_name, review_text, rating) VALUES ((SELECT id FROM restaurants WHERE name = '$restaurant'), '$reviewer_name', '$review_text', $rating)";

        if ($db->query($query) === TRUE) {
            $message = "New review added successfully";
        } else {
            $message = "Error: " . $db->error;
        }
    }

    // Gets the 2 most recent reviews from the database
    $review_query = "SELECT r.name AS restaurant_name, re.reviewer_name, re.review_text, re.rating, re.review_date 
                     FROM reviews re 
                     JOIN restaurants r ON re.restaurant_id = r.id 
                     ORDER BY re.review_date DESC 
                     LIMIT 2";
    $reviews = $db->query($review_query);

    // Prepares and executes a SQL query to gain the information about all restaurants
    $restaurant_query = "SELECT r.id, r.name, AVG(re.rating) as avg_rating 
                         FROM restaurants r 
                         LEFT JOIN reviews re ON r.id = re.restaurant_id 
                         GROUP BY r.id"; //This stores each review in an array
    $restaurants = $db->query($restaurant_query);

    // Close database connection
    mysqli_close($db);
    ?>

    <!-- Header section with the logo, navigation menu, and a search form-->
    <header>
        <div class="header-container">
            <img src="https://styleguide.umbc.edu/wp-content/uploads/sites/113/2019/03/UMBC-retriever-social-media.png" alt="UMBC Logo" class="logo">
            <h1 class="header-title">UMBC Campus Food Review</h1>
            <nav class="menu-dropdown">
                <ul class="menu-list">
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="search-screen.php">Search/Filter</a></li>
                    <li><a href="#">Options</a></li>
                </ul>
            </nav>
        </div>

        <!--   Search form to find restaurants  -->
        <form class="search-form">
            <input type="search" placeholder="Search Restaurant">
            <button type="submit">Search</button>
        </form>
    </header>

    <!-- Main content section with the recent reviews and a form to submit a new review which will be stored in the database -->
    <main class="content">
        <div class="review-container">
            <section class="recent-reviews">
                <h2>Recent Reviews</h2>
                <?php if ($reviews->num_rows > 0): ?>
                    <?php while ($review = $reviews->fetch_assoc()): ?>
                        <div class="review">

                             <!-- Review input section, the htmlspecialchars fucntion helps PHP convert special characters to HTML styles. It also helps prevent HTML Injection attcks. -->
                            <p><strong><?= htmlspecialchars($review['reviewer_name']) ?></strong> reviewed <strong><?= htmlspecialchars($review['restaurant_name']) ?></strong> on <?= date('F j, Y, g:i a', strtotime($review['review_date'])) ?></p>
                            <blockquote>"<?= htmlspecialchars($review['review_text']) ?>"</blockquote>
                            <p>Rating: <?= str_repeat("★", (int)$review['rating']) . str_repeat("☆", 5 - (int)$review['rating']) ?></p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No recent reviews found.</p>
                <?php endif; ?>

                <!--  Link to see all reviews   -->
                <a href="customer-reviews.php" class="view-reviews">View All Customer Reviews</a>
            </section>
            <section class="add-review">
                <!-- Form to submit a new review -->

                <h2>Write a Review</h2>
                <?php if (!empty($messages)): ?>
                    <p class = "alert"><?= $message; ?></p>
                <?php endif; ?>
                <form method="POST" id = "reviewForm">
                    <label for="reviewer_name">Your Name:</label>
                    <input type="text" id="reviewer_name" name="reviewer_name" required>
                    <label for="restaurant-select">Choose a Restaurant:</label>
                    <select id="restaurant-select" name="restaurant">
                        <option value="True Grits">True Grits</option>
                        <option value="Halal Shack">Halal Shack</option>
                        <option value="Wild Greenes">Wild Greenes</option>
                        <option value="Copperhead Jacks">Copperhead Jacks</option>
                        <option value="2mato Italian Kitchen">2mato Italian Kitchen</option>
                        <option value="Chick-fil-A">Chick-fil-A</option>
                        <option value="Starbucks">Starbucks</option>
                        <option value="Admin Shoppe">Admin Shoppe</option>
                        <option value="Sushi Do">Sushi Do</option>
                        <option value="Dunkin Donuts">Dunkin Donuts</option>
                        <option value="Indian Kitchen">Indian Kitchen</option>
                        <option value="Einstein Bros Bagels">Einstein Bros Bagels</option>
                    </select>

                    <!-- Review description -->
                    <label for="review-text">Your review:</label>
                    <textarea id="review-text" name="review_text" rows="4" required></textarea>
                    <label for="rating">Rating:</label>
                    <select id="rating" name="rating">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                    <button type="submit">Submit Review</button>
                </form>
            </section>
        </div>

        <!--Sidebar for displaying restaurant ratings -->
        <aside class="sidebar">
            <section class="ratings">

                 <!--  Tables with each restaurant & rating    -->
                <h2><a href="restaurant-info.php" class="view-restaurants">UMBC Restaurants</a></h2>
                <div class="scrollable-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Restaurant</th>
                                <th>Avg. Rating</th>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Displays each restaurant with its average rating using stars, dynamic table which updates as more reviews are written, the ratings round up to the nearest whole amount -->
                            <?php while ($restaurant = $restaurants->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($restaurant['name']) ?></td>
                                <td><?= str_repeat("★", (int)round($restaurant['avg_rating'])) . str_repeat("☆", 5 - (int)round($restaurant['avg_rating'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>
            <a href="dining-map.php"><img src="https://mcac.umbc.edu/wp-content/uploads/sites/160/2015/12/UMBC-General-Campus-Map_website-2.jpg" alt="Campus Map" class="campus-map"></a>
        </aside>
    </main>

    <footer>
        <p>© UMBC Dining Services</p>
    </footer>

</body>
</html>
