<?php
/*

    Wraps the DateTime class

    Makes sure dates are actually valid...

*/
class Date {

    const SUNDAY = 0;
    const MONDAY = 1;
    const TUESDAY = 2;
    const WEDNESDAY = 3;
    const THURSDAY = 4;
    const FRIDAY = 5;
    const SATURDAY = 6;

    const JANUARY = 1;
    const FEBRUARY = 2;
    const MARCH = 3;
    const APRIL = 4;
    const MAY = 5;
    const JUNE = 6;
    const JULY = 7;
    const AUGUST = 8;
    const SEPTEMBER = 9;
    const OCTOBER = 10;
    const NOVEMBER = 11;
    const DECEMBER = 12;

    const MIN_YEAR = 0;
    const MAX_YEAR = 9999;

    const MIN_MONTH = 1;
    const MAX_MONTH = 12;

    const MIN_DAY = 1;

    private $weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    private $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
        'October', 'November', 'December');
    private $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    private $daysInMonthLeap = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    private $valid = true;
    private $year;
    private $month;
    private $day;
    private $date;

    public function __construct($year, $month, $day = 1) {
        $this->date = new DateTime();
        $this->setDate($year, $month, $day);
    }

    public function copy() {
        return new Date($this->year, $this->month, $this->day);
    }

    public function setDate($year, $month, $day) {
        $year = intval($year);
        $month = intval($month);
        $day = intval($day);

        if($this->isValidDate()) {
            $this->year = $year;
            $this->month = $month;
            $this->day = $day;
            $this->date->setDate($year, $month, $day);
            $this->valid = true;
        } else {
            $this->valid = false;
        }
    }

    private function update() {
        $this->setDate($this->year, $this->month, $this->day);
    }

    public function setYear($year) {
        if($this->isValidDate($year, $this->month, $this->day)) {
            $this->year = $year;
            return true;
        }

        return false;
    }

    public function setMonth($month) {
        if($this->isValidDate($this->year, $month, $this->day)) {
            $this->month = $month;
            return true;
        }

        return false;
    }

    public function setDay($day) {
        if($this->isValidDate($this->year, $this->month, $day)) {
            $this->day = $day;
            return true;
        }

        return false;
    }

    public function isValid() {
        return $this->valid;
    }

    public function getYear() {
        return $this->year;
    }

    public function getMonth() {
        return $this->month;
    }

    public function getDay() {
        return $this->day;
    }

    public function isLeapYear() {
        return intval($this->date->format('L')) == 1;
    }

    public function getWeekDay() {
        return intval($this->date->format('w'));
    }

    public function getMonthName() {
        return $this->months[$this->month - 1];
    }

    public function getDaysInMonth() {
        return $this->isLeapYear() ?
            $this->daysInMonth[$this->month - 1] : $this->daysInMonthLeap[$this->month - 1];
    }

    private function isValidDate() {
        return $this->year >= MIN_YEAR && $this->year <= MAX_YEAR &&
            $this->month >= MIN_MONTH && $this->month <= MAX_MONTH &&
            $this->day >= MIN_DAY && $this->day <= $this->getDaysInMonth();
    }

    public function nextDay() {
        if($this->day == $this->getDaysInMonth()) {
            $this->setDay(1);
            $this->addMonth();
        } else {
            $this->setDay($this->day + 1);
        }

        $this->update();
        return $this;
    }

    public function nextMonth() {
        if($this->month == MAX_MONTH) {
            $this->setMonth(MIN_MONTH);
            $this->setYear($this->year + 1);
        } else {
            $this->setMonth($this->month + 1);
        }

        $this->update();
        return $this;
    }

    public function nextYear() {
        if($this->year < MAX_YEAR) {
            $this->setYear($this->year + 1);
        }

        $this->update();
        return $this;
    }

    public function previousDay() {
        if($this->day == MIN_DAY) {
            $this->previousMonth();
            $this->setDay($this->getDaysInMonth());
        } else {
            $this->setDay($this->day - 1);
        }

        $this->update();
        return $this;
    }

    public function previousMonth() {
        if($this->month == MIN_MONTH) {
            $this->setMonth(MAX_MONTH);
            $this->setYear($this->year - 1);
        } else {
            $this->setMonth($this->month - 1);
        }

        $this->update();
        return $this;
    }

    public function previousYear() {
        if($this->day == MIN_DAY) {
            $this->previousMonth();
            $this->setDay($this->getDaysInMonth());
        } else {
            $this->setDay($this->day - 1);
        }

        $this->update();
        return $this;
    }

    public function equals($date) {
        return $this->year == $date->getYear() &&
            $this->month == $date->getMonth() &&
            $this->day == $date->getDay();
    }

    public function fixNumber($number) {
        if($number < 10) {
            return '0' . $number;
        }
        return $this;
    }

    public function toMYSQLString() {
        return $this->year . '-' . $this->fixNumber($this->month) . '-' . $this->fixNumber($this->day);
    }

}


?>