<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../config.php';
    require_once '../src/Trait/DateUtils.php';
    require_once '../src/Service/DbManager.php';
    require_once '../src/Entity/Book.php';
    require_once '../src/Entity/BookRepository.php';

    $searchText = $_POST['searchText'];
    $searchText = $searchText ? trim($searchText) : '';

    // fetch records
    $bookRepository = new BookRepository($dbConfig);
    $books = $bookRepository->search("LOWER(author) LIKE LOWER(:searchText)", ['searchText' => "%{$searchText}%"]);

    // generate table
    $table = '<table border="1">
<head>
<th>ID</th>
<th>Title</th>
<th>Author</th>
<th>Date</th>
<th>File</th>
</head>';
    foreach ($books as $book) {
        $title = htmlspecialchars($book->getTitle());
        $author = htmlspecialchars($book->getAuthor());
        $file = htmlspecialchars($book->getFile());

        $table .= "<tr>
    <td>{$book->getId()}</td>
    <td>{$title}</td>
    <td>{$author}</td>
    <td>{$book->getCreated()->format('Y-m-d H:i:s')}</td>
    <td>{$file}</td>
    </tr>";
    }

    $table .= '</table>';
}
?>

<html>
<head>
    <title>Search page</title>
</head>
<body>
<div>
    <form method="post">
        <label for="searchText">
            Search for:
            <input type="text" name="searchText"/>
        </label>
        <input type="submit" value="Search"/>
    </form>
</div>

<div><?php echo isset($books) ? count($books) > 0 ? $table : 'No records found' : '' ?></div>
</body>
</html>