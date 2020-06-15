<?php

class Scanner {
    private array $records = [];

    /**
     * @param string $directory
     * @return $this
     */
    public function scan(string $directory = 'files'): self {
        // return if directory does not exist
        if(!file_exists($directory) || !is_dir($directory)) {
            echo "Directory \"$directory\" does not exist\n";
            return $this;
        }

        // get directory content
        $list = array_diff(scandir($directory), array('..', '.'));
        libxml_use_internal_errors(true); // hide warnings on XML parse errors

        foreach ($list as $item) {
            $file = "$directory/$item";

            if(is_dir($file)) {
                // if the current item is directory jump inside it
                $this->scan("$directory/$item");
            }
            else {
                // if it is a file read it's content
                $xmlData = simplexml_load_string(file_get_contents($file));
                if ($xmlData === false) {
                    // if can not parse the file continue with the next one
                    echo "Can not parse: $file\n";
                    continue;
                } else {
                    // fetch records and parse them to Book object
                    foreach ($xmlData as $data) {
                        $title = $data->name ? trim($data->name) : null;
                        $author = $data->author ? trim($data->author) : null;

                        // if title ot author are not provided skip the record
                        if($title && strlen($title) > 0 && $author && strlen($author) > 0) {
                            $this->records[] = new Book(null, $title, $author, $file);
                        }
                        else {
                            echo "Invalid record found in $file \n";
                        }
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     * @return Book[]
     */
    public function getResult(): array {
        // return scanned results
        return $this->records;
    }
}
