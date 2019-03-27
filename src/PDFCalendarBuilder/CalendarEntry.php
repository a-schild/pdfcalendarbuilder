<?php
declare(strict_types=1);


namespace PDFCalendarBuilder;

/**
 * Description of CalendarEntry
 *
 * @author andre
 */
class CalendarEntry {
    
    private $day;
    private $startDate;
    private $endDate;
    private $message;
    private $textColor;
    private $bgColor;
    
    function __construct(int $day, \DateTime $startDate, ?\DateTime $endDate,
            string $message, array $textColor,
            array $bgColor)
    {
        $this->day= $day;
        $this->startDate= $startDate;
        $this->endDate= $endDate;
        $this->message= $message;
        $this->textColor= $textColor;
        $this->bgColor= $bgColor;
    }
    
    public function getDay() :int
    {
        return $this->day;
    }
    
    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }
    
    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }
    
    public function getMessage(): string
    {
        return $this->message;
    }
    
    public function getTextColor():array
    {
        return $this->textColor;
    }
    
    public function getBackgroundColor(): array
    {
        return $this->bgColor;
    }
            
}
