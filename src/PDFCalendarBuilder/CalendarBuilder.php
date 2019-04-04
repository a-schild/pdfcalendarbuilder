<?php

declare(strict_types=1);

namespace aschild\PDFCalendarBuilder;

/**
 * CalendarBuilder
 *
 * generate a monthly calendar for the given year and month
 * 
 * Entries can be added to the days of the calendar, optional with categories
 * The calendar has a logic to either change row heights to fit everything
 * on a single page, or add additional pages for the next week rows
 *
 * @author André Schild
 */

class CalendarBuilder {

    // Basic calendar pdf parameters
    private $month; // Month of the calendar
    private $year; // Year of the calendar
    private $title; // Title of calendar
    private $unit;  // Unit to use for dimensions, defaul to mm
    private $orientationLandscape = true; // Page orientation P/L
    private $pageSize = "A4";    // Page format to use
    private $marginLeft = 10;
    private $marginRight = 10;
    private $marginTop = 10;
    private $marginBottom = 10;
    private $legendHeight = 0;   // Stores the legend height if any
    private $legendSpacing = 0.0; // Space between calendar and legend (if printed)
    private $date; // The start date of the calendar
    private $days_in_month; // The number of days for the calendar
    private $weekStarts = 0;    // On which day does the week start? 0=Sunday, 1=Monday
    private $printEndTime = false; // Do we print the end time of the entries?
    private $resizeRowHeightsIfNeeded = true; // Resize the row heights to fitt all entries on one page
    private $shrinkFontSizeIfNeeded = true; // use smaller font for entries unless everything fits on one page
    //private $addAdditionalPagesIfNeeded = true; // Add additional pages when not everything fits on one page
    // Internal values
    private $pdf; // The pdf object onse it is started
    // Default names of weekdays
    private $dayNames = array('Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday');
    // Default names of months
    private $monthNames = array('January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December');
    private $gridIsDrawn = false; // Is the grid drawn?
    private $dayEntries = array(); // Array containing the XY position of each day, the y value is incremented as entries are added to the calendar
    private $rowHeights = array(); // Array holding the row heights of the last grid drawn
    protected $cellWidth;
    protected $gridHeight;  // Height of grid in unit
    protected $gridWidth;
    protected $num_of_rows; // Num of rows in grid
    protected $fontSize;
    protected $fontHeight;
    private $titleFont = 'helvetica';
    private $titleFontSize = 20;
    private $headerFont = 'helvetica';
    private $headerFontSize = 12;
    private $numberFont = 'helvetica';
    private $numberFontSize = 20;
    private $eventFont = 'helvetica';
    private $eventFontSize = 10;
    private $categoryFont = 'helvetica';
    private $categoryFontSize = 10;
    private $footerFont = 'helvetica';
    private $footerFontSize = 8;
    private $shrinkFontSizeFactor = 0.95; // Reduce the font size by 10% and try once more
    private $weekday_of_first;

    /**
     * Create monthly calendar for the given month&year
     * 
     * @param int $month    Generate calendar for this month
     * @param int $year     Generate calendar for this year
     * @param string $title Print this as calendar title
     * @param boolean $orientationLandscape Orientation landscape (Default)
     * @param string $unit Units to use, default is mm
     * @param string $pageSize Paper format to use, default A4
     * 
     */
    function __construct(int $month, int $year, string $title,
            bool $orientationLandscape = true, string $unit = 'mm',
            string $pageSize = 'A4') {
        $this->month = $month;
        $this->year = $year;
        $this->title = $title;
        $this->orientationLandscape = $orientationLandscape;
        $this->unit = $unit;
        $this->pageSize = $pageSize;
        // Build start date of calendar
        $ts = mktime(0, 0, 0, $month, 1, $year);
        $this->date = getDate($ts);
        // Number of days to display in calendar
        $this->days_in_month = date('t', $ts);
        // Prepare array for each day
        for ($day = 0; $day < $this->days_in_month; $day++) {
            $this->dayEntries[$day]["entries"] = [];
        }
    }

    public function startPDF() {
        $this->pdf = new \TCPDF($this->orientationLandscape ? 'L' : 'P', $this->unit, $this->pageSize, true,
                'UTF-8', false);
        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
        $this->pdf->setTitle($this->getTitle($this->title));
        // set default monospaced font
        $this->pdf->SetDefaultMonospacedFont('helvetica');
        $this->pdf->setRightMargin($this->marginRight);
        $this->pdf->setLeftMargin($this->marginLeft);
        $this->pdf->setTopMargin($this->marginTop);
        //$this->pdf->setBottomMargin(0);
        $this->weekday_of_first = ($this->date["wday"] + 7 - $this->weekStarts) % 7;
        $this->num_of_rows = ceil(($this->days_in_month + $this->weekday_of_first) / 7.0);
        
        $this->pdf->SetAutoPageBreak(false);
        $this->pdf->AddPage();
        $this->gridWidth = $this->pdf->getPageWidth() - $this->marginRight - $this->marginLeft;
        $this->cellWidth = $this->gridWidth / 7;
    }

    /**
     * 
     * Draw the grid with the column headers and the day numbers in the cells
     * 
     * @param void|array $rowHeights 1..n use these row heigths instead of the calculated heights
     */
    protected function drawGrid(?array $rowHeights) {

        $this->fontSize = $this->eventFontSize;
        $this->fontHeight = 1;

        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetFont($this->titleFont, 'B', $this->titleFontSize);
        $this->pdf->Cell(0, $this->titleFontSize * 0.7,
                $this->getTitle(),
                0, 0, 'C');
        $this->pdf->Ln();
        $this->pdf->SetFillColor(128, 128, 128);
        $this->pdf->SetTextColor(255, 255, 255);

        /* Render the weekday header */
        $this->pdf->SetFont($this->headerFont, 'B', $this->headerFontSize);
        for ($i = 0; $i < 7; $i++) {
            $this->pdf->Cell($this->cellWidth, $this->headerFontSize * 0.7,
                    $this->dayNames[($i + $this->weekStarts) % 7], 1, 0, 'C', 1);
        }
        $this->pdf->Ln();
        $this->pdf->SetFillColor(128, 128, 128);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->gridHeight = $this->pdf->getPageHeight() - $this->pdf->GetY() - $this->marginBottom - $this->legendHeight - ($this->fontHeight / 2);
        $cellHeight = ($this->gridHeight) / $this->num_of_rows;

        /* Render the grid */
        $this->rowHeights = []; // Reset row heights to empty
        $gridTop = $this->pdf->GetY();
        for ($j = 1; $j <= $this->num_of_rows; $j++) {
            if ($rowHeights != null) {
                $cellHeight = $rowHeights[$j];
            }
            $this->rowHeights[$j] = $cellHeight; // Store row height
            for ($i = 0; $i < 7; $i++) {
                $this->pdf->Cell($this->cellWidth, $cellHeight, '', 1, 0, 'R');
            }
            if ($j <= $this->num_of_rows) {
                $this->pdf->Ln();
            }
        }

        /* Render the day numbers */
        $this->pdf->SetTextColor(200, 200, 200);
        $day = 1;
        $y_offset = $gridTop;
        for ($i = 1; $i <= $this->num_of_rows; $i++) {
            for ($j = 0; $j < 7; $j++) {
                if (($i == 1 && $this->weekday_of_first <= $j) || ($i > 1 && $day <= $this->days_in_month)) {
                    $x_offset = $this->marginLeft + $this->cellWidth * $j;
                    $this->dayEntries[$day - 1]["X"] = $x_offset;
                    $this->dayEntries[$day - 1]["Y"] = $y_offset;
                    $this->dayEntries[$day - 1]["StartY"] = $y_offset;
                    $cellHeight = $this->rowHeights[$i];
                    $this->pdf->SetY($y_offset + $cellHeight - ($this->numberFontSize / 2));
                    $this->pdf->SetX($x_offset);
                    $this->pdf->SetFont($this->numberFont, 'B',
                            $this->numberFontSize);
                    $this->pdf->Cell($this->cellWidth, 1, $day, 0, 0, 'R');
                    $day++;
                }
            }
            $y_offset += $cellHeight;
        }
        $this->gridIsDrawn = true;
    }

    public function addEntry(\DateTime $startDate, \DateTime $endDate, string $message,
            string $htmlcolor = '#000000',
            string $htmlBackgroundColor = '#ffffff'): void {
        $textColor = ColorNames::html2rgb($htmlcolor);
        $bgColor = ColorNames::html2rgb($htmlBackgroundColor);

        $this->storeEntry($startDate, $endDate, $message, $textColor, $bgColor);
    }

    protected function storeEntry(\DateTime $startDate, \DateTime $endDate,
            string $message, array $textColor,
            array $bgColor): void {
        $this->storeFullEntry(new CalendarEntry($startDate, $endDate, $message, $textColor, $bgColor));
    }

    protected function storeFullEntry(CalendarEntry $ce): void {

        $day= $ce->getStartDate()->format("d");
        array_push($this->dayEntries[$day - 1]["entries"], $ce);
    }

    /**
     * Draw the calendarentry at the correct location
     * 
     * @param \PDFCalendarBuilder\CalendarEntry $calendarEntry
     */
    protected function drawEntry(CalendarEntry $calendarEntry) {
        if (!$this->gridIsDrawn) {
            $this->drawGrid(null);
        }
        list($rT, $gT, $bT) = $calendarEntry->getTextColor();
        list($rB, $gB, $bB) = $calendarEntry->getBackgroundColor();

        $this->pdf->SetFillColor($rB, $gB, $bB);
        $this->pdf->SetTextColor($rT, $gT, $bT);
        $this->pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter',
            'dash' => 0, 'color' => array(128, 128, 128)));
        $this->pdf->setCellPaddings(0.5, 0.5, 0.5, 0.5);
        $this->pdf->SetY($this->dayEntries[$calendarEntry->getDay() - 1]["Y"]);
        $this->pdf->SetX($this->dayEntries[$calendarEntry->getDay() - 1]["X"]);
        $this->pdf->SetFont($this->eventFont, '', $this->fontSize);
        if ($calendarEntry->isHideStartTime())
        {
            $txt= "";
        }
        else
        {
            $startDate = $calendarEntry->getStartDate();
            if ($startDate->format('i') == "00") {
                $txt = $startDate->format('G');
            } else {
                $txt = $startDate->format('G:i');
            }
        }
        if ($this->printEndTime && ! $calendarEntry->isHideEndTime()) {
            $endDate = $calendarEntry->getEndDate();
            if ($endDate != null) {
                if ($endDate->format('i') == "00") {
                    $txt .= '-' . $endDate->format('G');
                } else {
                    $txt .= '-' . $endDate->format('G:i');
                }
            }
        }
        if (!$calendarEntry->isHideStartTime()
            || ($this->printEndTime && ! $calendarEntry->isHideEndTime()))
        {
            $txt .= 'h ';
        }
        $txt.=  $calendarEntry->getMessage();
        $this->pdf->MultiCell($this->cellWidth, 1,
                $txt, 1, 'L', true);
        $this->dayEntries[$calendarEntry->getDay() - 1]["Y"] = $this->pdf->GetY();
    }

    /**
     * Add a calendar entry with the given arguments and the given category
     * The colors are taken from the previously added category
     * 
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string $message
     * @param type $categoryID
     * @return void
     */
    function addEntryCategory(\DateTime $startDate, \DateTime $endDate, string $message,
            $categoryID): void {
        $this->storeEntry($startDate, $endDate, $message, $this->categories[$categoryID]["textColor"],
                $this->categories[$categoryID]["bgColor"]);
    }

    protected function drawGridWithEntries(?array $newRowHeights): void {
        $this->drawGrid($newRowHeights);
        foreach ($this->dayEntries as $days) {
            foreach ($days["entries"] as $entry) {
                $this->drawEntry($entry);
            }
        }
    }

    /**
     * Now generate the calendar
     */
    public function buildCalendar() {
        $this->expandDayspanners(); // Expand entries where start+end date are not on the same day
        $this->sortEntries(); // Sort entries according to start date and day spanning stuff
        $this->pdf->startTransaction();  // We do a rollback if needed to change row heights
        $this->drawGridWithEntries(null);
        if (!($this->resizeRowHeightsIfNeeded || $this->shrinkFontSizeIfNeeded) && $this->checkContentInsideDayBoxes()) {
            // OK, everything inside the boxes, or no dynamic adapting
            $this->pdf->commitTransaction();
        } else {
            $this->pdf->rollbackTransaction(true);
            $oneMoreTry = true;
            while ($oneMoreTry) {
                // Now we check the row heights, perhaps we can do something here
                if ($this->resizeRowHeightsIfNeeded) {
                    $newRowHeights = $this->checkRowHeights();
                    if ($newRowHeights != null) {
                        $this->drawGridWithEntries($newRowHeights);
                        $oneMoreTry = false;
                    } else {
                        if ($this->shrinkFontSizeIfNeeded) {
                            $this->pdf->startTransaction();  // We do a rollback if needed to change row heights
                            $this->eventFontSize = $this->eventFontSize * $this->shrinkFontSizeFactor;
                            $this->drawGridWithEntries(null);
                        } else {
                            // No way to fit on page
                            $this->drawGridWithEntries(null);
                            $oneMoreTry = false;
                        }
                    }
                } else {
                    if ($this->shrinkFontSizeIfNeeded) {
                        $this->pdf->startTransaction();  // We do a rollback if needed to change row heights
                        $this->eventFontSize = $this->eventFontSize * $this->shrinkFontSizeFactor;
                        $this->drawGridWithEntries(null);
                    } else {
                        // No way to fit on page
                        $this->drawGridWithEntries(null);
                        $oneMoreTry = false;
                    }
                }
                if ($oneMoreTry) {
                    if ($this->checkContentInsideDayBoxes()) {
                        $this->pdf->commitTransaction();
                        $oneMoreTry = false;
                    } else {
                        $this->pdf->rollbackTransaction(true);
                    }
                }
            }
        }
    }

    /**
     * Now finish the pdf and write it to the destination
     * 
     * @param type $name name of the pdf file
     * @param type $dest output location, use I for internal (Send back to browser)
     */
    function output($name, $dest) {
        $this->pdf->Output($name, $dest);
    }

    public function setDayNames(array $newDayNames): void {
        $this->dayNames = $newDayNames;
    }

    public function setMonthNames(array $newMonthNames): void {
        $this->monthNames = $newMonthNames;
    }

    public function setWeekStarts(int $weekStarts): void {
        $this->weekStarts = $weekStarts;
    }

    /**
     * 
     * @param string $tsMessage
     * @param int $posX
     * @param int $posY
     * @param int $width
     * @return void
     */
    public function writeTimestamp(string $tsMessage, int $posX, int $posY, int $width): void {
        $this->pdf->SetFillColor(255, 255, 255);
        $this->pdf->SetTextColor(0, 0, 0);
        $this->pdf->SetFont($this->footerFont, '', $this->footerFontSize);
        $this->pdf->SetY($posY);
        $this->pdf->SetX($posX);
        $this->pdf->MultiCell($width,
                1, $tsMessage, 0, 'R', true);
    }

    /**
     * Print the calendar legend at the bottom of the page
     * The available height for the calendar is modified
     * 
     * If more that 7 categories are available, we use multiple lines for the
     * legend.
     * 
     * @return void
     */
    public function printCategories(): void {
        $origY = $this->pdf->getY();
        $nCategories = count($this->categories);
        $catCols = min($nCategories, 7);
        $catRows = ceil(count($this->categories) / $catCols); // Number of rows to use for categories legend
        $rowHeights = array();
        for ($i = 1; $i <= $catRows; $i++) {
            $rowHeights[$i] = $this->categoryFontSize / 3;
        }
        $rowHeightsReal = array();
        $legendHeight = 0;
        for ($i = 1; $i <= $catRows; $i++) {
            $rowHeightsReal[$i] = $rowHeights[$i];
            $legendHeight += $rowHeights[$i];
        }
        $colPos = 0;
        $rowPos = 1;
        $rowTop = 0;
        $cellWidth = ($this->pdf->getPageWidth() - $this->marginLeft - $this->marginRight) / $catCols;
        $firstRowPos = $this->pdf->getPageHeight() - $this->marginBottom - $legendHeight;
        $this->pdf->SetFont($this->categoryFont, 'B', $this->categoryFontSize);

        $this->pdf->startTransaction();
        foreach ($this->categories as $category) {
            $catName = $category["name"];
            list($rT, $gT, $bT) = $category["textColor"];
            list($rB, $gB, $bB) = $category["bgColor"];

            $this->pdf->SetFillColor($rB, $gB, $bB);
            $this->pdf->SetTextColor($rT, $gT, $bT);
            $this->pdf->SetY($firstRowPos + $rowTop);
            $this->pdf->SetX($this->marginLeft + ($cellWidth * $colPos));
            $this->pdf->MultiCell($cellWidth, 1, $catName, 1, 'C', true);
            $myY = $this->pdf->getY();
            $myHeight = $myY - ($firstRowPos + $rowTop);
            $rowHeightsReal[$rowPos] = max($rowHeightsReal[$rowPos], $myHeight);
            $colPos++;
            if ($colPos > $catCols - 1) {
                $colPos = 0;
                $rowTop = $rowTop + $rowHeights[$rowPos];
                $rowPos++;
            }
        }
        $needReprint = false;
        for ($i = 1; $i <= $catRows; $i++) {
            $needReprint = $needReprint || ($rowHeights[$i] <> $rowHeightsReal[$i]);
        }
        if ($needReprint) {
            $colPos = 0;
            $rowPos = 1;
            $rowTop = 0;
            $this->pdf->rollbackTransaction(true);
            $firstRowPos = $this->pdf->getPageHeight() - $this->marginBottom;
            for ($i = 1; $i <= $catRows; $i++) {
                $firstRowPos -= ($rowHeightsReal[$i]);
            }
            foreach ($this->categories as $category) {
                $catName = $category["name"];
                list($rT, $gT, $bT) = $category["textColor"];
                list($rB, $gB, $bB) = $category["bgColor"];

                $this->pdf->SetFillColor($rB, $gB, $bB);
                $this->pdf->SetTextColor($rT, $gT, $bT);
                $this->pdf->SetY($firstRowPos + $rowTop);
                $this->pdf->SetX($this->marginLeft + ($cellWidth * $colPos));
                $this->pdf->MultiCell($cellWidth, 1, $catName, 1, 'C', true);
                $myY = $this->pdf->getY();
                $colPos++;
                if ($colPos > $catCols - 1) {
                    $colPos = 0;
                    $rowTop += $rowHeightsReal[$rowPos];
                    $rowPos++;
                }
            }
            $this->legendHeight = $rowTop + $this->legendSpacing;
        } else {
            $this->pdf->commitTransaction();
            $this->legendHeight = $rowTop + $this->legendSpacing;
        }
        $this->pdf->setY($origY);
    }

    public function setTitleFontSize(float $newTitleFontSize): void {
        $this->titleFontSize = $newTitleFontSize;
    }

    public function setHeaderFontSize(float $newHeaderFontSize): void {
        $this->headerFontSize = $newHeaderFontSize;
    }

    public function setNumberFontSize(float $newNumberFontSize): void {
        $this->numberFontSize = $newNumberFontSize;
    }

    public function setEventFontSize(float $newEventFontSize): void {
        $this->eventFontSize = $newEventFontSize;
    }

    public function setCategoryFontSize(float $newCategoryFontSize): void {
        $this->categoryFontSize = $newCategoryFontSize;
    }

    /**
     * Add a new category to the system
     * 
     * @param type $categoryID  id of the category to add (Used later with addEntry() method)
     * @param string $categoryName name of the category (for the legend)
     * @param string $textColor html color of the text, default black
     * @param string $bgColor   html color of the cell background for this category, default white
     * @return void
     */
    public function addCategory($categoryID, string $categoryName,
            string $textColor = '#000000',
            string $bgColor = '#ffffff'): void {
        $this->categories[$categoryID] = array(
            "name" => $categoryName,
            "textColor" => ColorNames::html2rgb($textColor),
            "bgColor" => ColorNames::html2rgb($bgColor));
    }

    /**
     * Set the margings in clock wise order
     * 
     * @param float $top
     * @param float $right
     * @param float $bottom
     * @param float $left
     * @return void
     */
    public function setMargins(float $top, float $right, float $bottom, float $left): void {
        $this->marginTop = $top;
        $this->marginRight = $right;
        $this->marginBottom = $bottom;
        $this->marginLeft = $left;
    }

    /**
     * 
     * @param bool $doResizeRowHeights Resize the row heights to fitt all entries on one page
     * @return void
     */
    public function setResizeRowHeightsIfNeeded(bool $doResizeRowHeights): void {
        $this->resizeRowHeightsIfNeeded = $doResizeRowHeights;
    }

//    /**
//     * 
//     * @param bool $addAdditionalPagesIfNeeded Add additional pages when not everything fits on one page
//     * @return void
//     */
//    public function setAddAdditionalPagesIfNeeded(bool $addAdditionalPagesIfNeeded): void {
//        $this->addAdditionalPagesIfNeeded = $addAdditionalPagesIfNeeded;
//    }

    /**
     * Should the font size of the entries be decreased until everything fits one
     * one page
     * 
     * @param bool $shrinkFontSizeIfNeeded
     * @return void
     */
    public function setShrinkFontSizeIfNeeded(bool $shrinkFontSizeIfNeeded): void {
        $this->shrinkFontSizeIfNeeded = $shrinkFontSizeIfNeeded;
    }

    /**
     * 
     * @param bool $printEndTime Do we print the end time of the entries?
     * @return void
     */
    public function setPrintEndTime(bool $printEndTime): void {
        $this->printEndTime = $printEndTime;
    }

    /**
     * Check if all enries are in the correct row/heigth
     * If some entries overflow the day box, we return false so the 
     * row heigths/font sizes etc. can be adapted
     * 
     * @return bool true when everything OK
     */
    protected function checkContentInsideDayBoxes(): bool {
        $day = 1;
        $rowHeights;
        for ($i = 1; $i <= $this->num_of_rows; $i++) {
            $rowHeights[$i] = 0.0; // Max height of row as for now
            for ($j = 0; $j < 7; $j++) {
                if (($i == 1 && $this->weekday_of_first <= $j) || ($i > 1 && $day <= $this->days_in_month)) {
                    $contentHeight = $this->dayEntries[$day - 1]["Y"] - $this->dayEntries[$day - 1]["StartY"];
                    if ($contentHeight == 0) {
                        $contentHeight = $this->numberFontSize*0.3;
                    }
                    $rowHeights[$i] = max($rowHeights[$i], $contentHeight);
                    $day++;
                }
            }
        }
        $hasBoxOverflow = false;
        $totalHeight = 0;
        for ($i = 1; $i <= $this->num_of_rows; $i++) {
            $hasBoxOverflow = $hasBoxOverflow || ($rowHeights[$i] > $this->rowHeights[$i]);
            $totalHeight += $rowHeights[$i];
        }
        return !$hasBoxOverflow;
    }

    /**
     * Check if all enries are in the correct row/heigth
     * If some entries overflow the day box, we try to adjust the row
     * heights so everything still fits on one page
     * 
     * @return array with the final row heights to use, or null when OK or no chance to do it
     */
    protected function checkRowHeights(): ?array {
        $day = 1;
        $rowHeights;
        for ($i = 1; $i <= $this->num_of_rows; $i++) {
            $rowHeights[$i] = 0.0; // Max height of row as for now
            for ($j = 0; $j < 7; $j++) {
                if (($i == 1 && $this->weekday_of_first <= $j) || ($i > 1 && $day <= $this->days_in_month)) {
                    $contentHeight = $this->dayEntries[$day - 1]["Y"] - $this->dayEntries[$day - 1]["StartY"];
                    if ($contentHeight == 0) {
                        $contentHeight = $this->numberFontSize * 0.5;
                    }
                    $rowHeights[$i] = max($rowHeights[$i], $contentHeight);
                    $day++;
                }
            }
        }
        $originRowHeight = $this->gridHeight / $this->num_of_rows;
        $hasBoxOverflow = false;
        $totalContentHeight = 0;
        foreach ($rowHeights as $rh) {
            $hasBoxOverflow = $hasBoxOverflow || ($rh > $originRowHeight);
            $totalContentHeight += $rh;
        }
        if ($hasBoxOverflow) {
            if ($this->gridHeight >= $totalContentHeight) {
                // We can adapt row heights so everything fits into one page
                $rowHeights= $this->adaptRowHeights($this->gridHeight, $totalContentHeight, $rowHeights);
                return $rowHeights;
            } else {
                // No chance still too height
                return null;
            }
        } else {
            // No need to adapt row heights
            return null;
        }
    }

    /**
     * You can overwrite this method to provide your own implementation
     * of the row height algorithm
     *
     * @param type $gridHeight
     * @param type $totalContentHeight
     * @param type $oldRowHeights
     * @return type
     */
    protected function adaptRowHeights(float $gridHeight, float $totalContentHeight, array $oldRowHeights) :array
    {
        $EPSILON= 0.0001; // We use this to compare floats
        $minRowHeight= $gridHeight; // smallest height
        $minRowCount= 0; // Number of rows with the smallest height
        $min2RowHeight= $gridHeight; // second smallest height
        $maxRowHeight= 0; // largest row height
        foreach($oldRowHeights as $rh)
        {
            $oldMinRowHeight= $minRowHeight;
            $minRowHeight= min($minRowHeight, $rh);
            if (abs($minRowHeight - $oldMinRowHeight) > $EPSILON)
            {
                $min2RowHeight= $oldMinRowHeight;
                $minRowCount= 1;
            }
            else
            {
                if (abs($rh - $minRowHeight ) < $EPSILON)
                {
                    $minRowCount+= 1;
                }
                else if ($min2RowHeight > $rh )
                {
                    $min2RowHeight= $rh;
                }
            }
            $maxRowHeight= max($maxRowHeight, $rh);
        }
        $spaceToDistribute = $gridHeight - $totalContentHeight;
        $maxExpand= ($min2RowHeight - $minRowHeight);
        $minExpandHeight = $spaceToDistribute / $minRowCount;
        $minExpand2= min($maxExpand, $minExpandHeight);
        for ($i = 1; $i <= $this->num_of_rows; $i++) {
            if (abs($oldRowHeights[$i] - $minRowHeight) < $EPSILON)
            {
                $newRowHeights[$i] = $oldRowHeights[$i]+$minExpand2;
            }
            else {
                $newRowHeights[$i] = $oldRowHeights[$i];
            }
        }
        $newTotalContentHeight= 0;
        foreach ($newRowHeights as $rh) {
            $newTotalContentHeight += $rh;
        }
        if (abs($newTotalContentHeight - $gridHeight) > $EPSILON)
        {
            // Recursive call
            $newRowHeights= $this->adaptRowHeights($gridHeight, $newTotalContentHeight, $newRowHeights);
        }
        return $newRowHeights;
    }

    /**
     * Return the width of the ouput pdf
     * The startPDF method must already have been called
     * 
     * @return float
     */
    public function getPageWidth(): float {
        return $this->pdf->getPageWidth();
    }

    /**
     * Default is: title - monthName Year
     * 
     * @return string title to be printed on top of the calendar
     */
    protected function getTitle():string
    {
        return $this->title . ' - ' . $this->monthNames[$this->month - 1] . ' ' . $this->date["year"];
    }

    /**
     * How do we handle entries which span multiple days?
     *
     * This implementation just duplicates the entries in the followin days and
     * sets the start time to 00:00
     */
    protected function expandDaySpanners()
    {
        $toAddEntries= array();
        foreach ($this->dayEntries as $days) {
            foreach ($days["entries"] as $entry) {
                if($entry ->isSpanningDays())
                {
                    $entry->setHideEndTime(true);
                    $nextDayStart= clone $entry->getStartDate();
                    $nextDayStart= $nextDayStart->add(new \DateInterval("P1D"))->setTime(0,0,0);
                    while ($nextDayStart < $entry->getEndDate() && $nextDayStart->format("m") == $this->month)
                    {
                        $newEntry= new CalendarEntry(clone $nextDayStart,
                            clone $entry->getEndDate(), $entry->getMessage(),
                            $entry->getTextColor(), $entry->getBackgroundColor());
                        $newEntry->setHideStartTime(true);
                        $newEntry->setOriginalEntryStartDate($entry->getStartDate());
                        array_push($toAddEntries, $newEntry);
                        $nextDayStart= $nextDayStart->add(new \DateInterval("P1D"));
                    }
                    $entry->getEndDate()->setTime(0,0,0);
                }
                else
                {
                    // OK, single day entry
                }
            }
        }
        foreach($toAddEntries as $toAdd)
        {
            if ($toAdd->isSpanningDays())
            {
                $toAdd->setHideEndTime(true);
                $toAdd->setMessage($this->adaptNextDayMessage($toAdd));
            }
            $this->storeFullEntry($toAdd);
        }
    }

    /**
     * This method generates the text of the calendar entry
     * If the entry is spanning to the next day, we add ... after the message
     * If the entry is spanning from the previous day, we add ... in fron tof the message
     * 
     * @param \aschild\PDFCalendarBuilder\CalendarEntry $calEntry
     * @return string
     */
    protected function adaptNextDayMessage(CalendarEntry $calEntry):string
    {
        $msg= $calEntry->getMessage();
        if ($calEntry->isContinuationEntry())
        {
            $msg= '...'.$msg;
        }
        if ($calEntry->isSpanningDays())
        {
            return $msg.'...';
        }
        else
        {
            return $msg;
        }
    }

    /**
     * Sort calendar entries by start time.
     * All day spanning events are sorted on top
     * 
     * @return void
     */
    protected function sortEntries():void
    {
        for ($day = 0; $day < $this->days_in_month; $day++) {
            $entries= $this->dayEntries[$day]["entries"];
            usort($entries,
                function ($a, $b) : bool {
                    if ($a->isContinuationEntry() )
                    {
                        if ($b->isContinuationEntry())
                        {
                            // Check original start dates/times
                            return $a->getOriginalEntryStartDate()->getTimestamp() > $b->getOriginalEntryStartDate()->getTimestamp();
                        }
                        else
                        {
                            return false;
                        }
                    }
                    else
                    {
                        return $a->getStartDate()->getTimestamp() > $b->getStartDate()->getTimestamp();
                    }
                });
            $this->dayEntries[$day]["entries"]= $entries;
        }
    }
}
