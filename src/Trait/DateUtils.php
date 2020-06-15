<?php

trait DateUtils
{
    /**
     * @return DateTime
     * @throws Exception
     */
    public function getCurrentDateTime(): DateTime
    {
        // get current date and time in UTC
        return new DateTime('now', $this->getUTCTimeZone());
    }

    /**
     * @param string $date
     * @return DateTime
     */
    public function createFromFormat(string $date): DateTime
    {
        return DateTime::createFromFormat('Y-m-d H:i:s', $date, $this->getUTCTimeZone());
    }

    /**
     * @param DateTime $date
     * @return string
     */
    public function formatDate(DateTime $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }

    /**
     * @return DateTimeZone
     */
    private function getUTCTimeZone(): DateTimeZone
    {
        return new DateTimeZone('UTC');
    }
}
