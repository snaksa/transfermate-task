<?php

class BookRepository
{
    use DateUtils;

    const TABLE = 'books';
    private DbManager $dbManager;

    /**
     * BookRepository constructor.
     * @param array $dbConfig
     */
    public function __construct(array $dbConfig)
    {
        $this->dbManager = new DbManager();
        $this->dbManager->connect($dbConfig);
    }

    /**
     * @param string $where
     * @param array $params
     * @return Book[]
     */
    public function search(string $where = null, array $params = []): array
    {
        // find records and parse them
        $records = $this->dbManager->search(self::TABLE, $where, $params);

        $result = [];
        foreach ($records as $record) {
            $result[] = $this->parse($record);
        }

        return $result;
    }

    public function save(Book $book): Book
    {
        // set fields to be inserted/updated
        $fields = [
            [
                'field' => 'title',
                'value' => $book->getTitle()
            ],
            [
                'field' => 'author',
                'value' => $book->getAuthor()
            ],
            [
                'field' => 'created',
                'value' => $this->formatDate($book->getCreated())
            ],
            [
                'field' => 'file',
                'value' => $book->getFile()
            ],
        ];

        // if id exists we need to update the record
        // if not we need to insert new one
        if (!$book->getId()) {
            $id = $this->dbManager->insert(self::TABLE, $fields);
            $book->setId($id);
        } else {
            $this->dbManager->update(self::TABLE, $book->getId(), $fields);
        }

        return $book;
    }

    private function parse(array $record): Book
    {
        // parse record after fetching it from the db
        return new Book(
            $record['id'],
            $record['title'],
            $record['author'],
            $record['file'],
            $this->createFromFormat($record['created'])
        );
    }
}
