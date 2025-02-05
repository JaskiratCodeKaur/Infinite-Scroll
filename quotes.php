<?php
/**
 * @author  Jaskirat Kaur
 * @version 202335.00
 * @package COMP 10260 Assignment 4
 * 
 * I Jaskirat Kaur, 000904397 , certify that this material is my original work.
 * No other person's work has been used without suitable acknowledgment and I have not made my work available to anyone else.
 * 
 */

try {
    // To include database connection file for database connection.
    $pdo = new PDO("mysql:host=localhost;dbname=sa000904397", "sa000904397", "Sa_20031112");

} catch (Exception $e) {
    //To handle if any error happen in the connection
    echo ("Unable to connect with database due to $e->getMessage()");
}

// To validate and sanitize the page parameter
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);


//For setting the page parameter to 1 , in case of the negative values.
$page = ($page === false || $page < 1) ? 1 : $page;


//To set the limit of quotations per page
$limit = 20;

//To calculate the offset based on the current page
$offset = ($page - 1) * $limit;

//To retrieve quotations from the database using the query
$query = "SELECT quotes.quote_text, authors.author_name
        FROM quotes
        JOIN authors ON quotes.author_id = authors.author_id
        LIMIT :per_page
        OFFSET :offset 
        ";
// To validate the query
$stmt = $pdo->prepare($query);
$stmt->bindParam(':per_page', $limit,PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset,PDO::PARAM_INT);

//To execute the query
$success = $stmt->execute();

// To fetch  all quotations from the result set
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);



/**
 * This function createCard is to generate the HTML string for the Bootstrap 5 card with the quote and author.
 * @param string $quote   The quotation text.
 * @param string $author  The author's name of the quotation.
 * @return string the HTML string  for the Bootstrap 5 card with the quote and author.
 */
function createCard($quote, $author)
{
    $html_element = '<div class="card mb-3 a4card w-100">
                <div class="card-header">' . $author . '</div>
                <div class="card-body d-flex align-items-center">
                    <p class="card-text w-100">' . $quote . '</p>
                </div>
            </div>';

    return $html_element;
}

//To store HTML strings for each quotation
$html_tags = [];

// To generate HTML strings for each quotation
foreach ($result as $quotes) {
    $html_tags[] = createCard($quotes['quote_text'], $quotes['author_name']);
}

// To output the JSON-encoded array of HTML strings
echo json_encode($html_tags);