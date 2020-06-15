<?php

require_once './config.php';
require_once './src/Service/Scanner.php';
require_once './src/Trait/DateUtils.php';
require_once './src/Service/DbManager.php';
require_once './src/Entity/Book.php';
require_once './src/Entity/BookRepository.php';

$directory = 'files';
if ($argc > 1) {
    $directory = $argv[1]; // path
}

// scan files
echo "\nScanning...\n";
$scanner = new Scanner();
$scannedRecords = $scanner->scan($directory)->getResult();
echo "Scanning finished...\n\n";

echo count($scannedRecords) > 0 ? "Scanned records:\n" : "No records scanned\n";
// find existing duplicated records
$bookRepository = new BookRepository($dbConfig);
$dbRecords = findDuplicates($scannedRecords, $bookRepository);

// insert or update records
saveRecords($scannedRecords, $dbRecords, $bookRepository);

$recordsCount = count($scannedRecords);
echo "\nScript finished. {$recordsCount} records inserted/updated. \n\n";

function saveRecords(array $scannedRecords, array $dbRecords, BookRepository $bookRepository)
{
    // if scanned record exists in the db updated the date
    foreach ($scannedRecords as $record) {
        foreach ($dbRecords as $book) {
            if (strtolower($book->getTitle()) === strtolower($record->getTitle())
                && strtolower($book->getAuthor()) === strtolower($record->getAuthor())
                && $book->getFile() === $record->getFile()
            ) {
                // record exists in the DB - update date and time
                $book->setCreated();
                $record = $book;
                break;
            }
        }

        $bookRepository->save($record);
        $dbRecords[] = $record;
    }
}

function findDuplicates(array $records, BookRepository $bookRepository)
{
    $whereClauses = [];
    $params = [];
    $count = 1;
    foreach ($records as $key => $record) {
        echo "$record\n";
        $whereClauses[] = "(LOWER(title) = :title{$count} AND LOWER(author) = :author{$count} AND file = :file{$count})";
        $params[":title{$count}"] = strtolower($record->getTitle());
        $params[":author{$count}"] = strtolower($record->getAuthor());
        $params[":file{$count}"] = $record->getFile();

        $count++;
    }

    return $bookRepository->search(implode(' OR ', $whereClauses), $params);
}
