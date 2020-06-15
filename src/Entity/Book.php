<?php

class Book {
    use DateUtils;

    private ?int $id;
    private string $title;
    private string $author;
    private DateTime $created;
    private string $file;

    public function __construct(?int $id, string $title, string $author, string $file, DateTime $created = null)
    {
        $this->setId($id);
        $this->setTitle($title);
        $this->setAuthor($author);
        $this->setFile($file);
        $this->setCreated($created);
    }

    public function __toString()
    {
        $data = [$this->getId() ?? '-', $this->getTitle(), $this->getAuthor(), $this->getCreated()->format('Y-m-d H:i:s')];
        return implode(' | ', $data);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Book
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Book
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @param string $author
     * @return Book
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     * @return Book
     * @throws Exception
     */
    public function setCreated(?DateTime $created = null): self
    {
        $this->created = $created ?? $this->getCurrentDateTime();

        return $this;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * @param string $file
     * @return Book
     */
    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }
}
