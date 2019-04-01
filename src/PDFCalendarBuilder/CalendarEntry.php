<?php
declare(strict_types=1);


namespace aschild\PDFCalendarBuilder;

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
    private $hideStartTime= false;
    private $hideEndTime= false;
    private $originalEntryStartDate= null; // When did the original entry start? (Used for sorting)
    private $continuationEntry= false; // Is this a entry which has been inserted by day-spanning entries?

    function __construct(\DateTime $startDate, ?\DateTime $endDate,
            string $message, array $textColor,
            array $bgColor)
    {
        $this->day= intval($startDate->format("d"));
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
        
    /**
     * 
     * @return bool true when the entry does not end on the same day
     */
    public function isSpanningDays() : bool
    {
        if ($this->endDate != null)
        {
            return $this->endDate->format("Y-m-d") !== $this->startDate->format("Y-m-d") ;
        }
        else
        {
            return false;
        }
    }

    public function setHideStartTime(bool $hideStartTime) :void
    {
        $this->hideStartTime= $hideStartTime;
    }

    public function isHideStartTime():bool
    {
        return $this->hideStartTime;
    }

    public function setHideEndTime(bool $hideEndTime) :void
    {
        $this->hideEndTime= $hideEndTime;
    }

    public function isHideEndTime():bool
    {
        return $this->hideEndTime;
    }

    public function setMessage(string $newMessage):void
    {
        $this->message= $newMessage;
    }

    public function setOriginalEntryStartDate(\DateTime $originalEntryStartDate) :void
    {
        $this->continuationEntry= true;
        $this->originalEntryStartDate= $originalEntryStartDate;
    }

    public function isContinuationEntry():bool
    {
        return $this->continuationEntry;
    }

    public function getOriginalEntryStartDate():?\DateTime
    {
        return $this->originalEntryStartDate;
    }
}
