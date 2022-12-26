<?php
// TODO: set locale
// Calendar object
class Calendar {
    /*** PRIVATE PROPERTY ***/

    private $currentMonth = null;
    private $currentYear = null;
    private $dayLabels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
    private $currentDay = 0;
    private $currentDate = null;
    private $daysInMonth = 0;

    /*** PRIVATE METHODS ***/

    private function _currentMonthAndYear() {
        if (isset($_GET['mois']) && isset($_GET['annee'])) {
            $this->currentMonth = $_GET['mois'];
            $this->currentYear = $_GET['annee'];
        } else {
            $this->currentMonth = date('m', time());
            $this->currentYear = date('Y', time());
        }
    }

    private function _daysInMonth($month, $year) {
        return date('t', strtotime($year.'-'.$month.'-01'));
    }

    private function _weeksInMonth($month, $year) {
        $daysInMonth = $this->_daysInMonth($month, $year);
        $startDate = strtotime($year.'-'.$month.'-01');
        $firstDay = date('N', $startDate);
        $endDate = strtotime($year.'-'.$month.'-'.$daysInMonth);
        $lastDay = date('N', $endDate);

        for ($weeks=0; $startDate < $endDate; $weeks++) {
            $startDate = strtotime("+1 week", $startDate);
        }
        if ($lastDay < $firstDay) {
            $weeks++;
        }

        return $weeks;
    }

    private function _createTitle() {
        // previous
        $previousMonth = $this->currentMonth == 1 ? 12 : intval($this->currentMonth)-1;
        $previousYear = $this->currentMonth == 1 ? intval($this->currentYear)-1 : $this->currentYear;

        // next
        $nextMonth = $this->currentMonth == 12 ? 1 : intval($this->currentMonth)+1;
        $nextYear = $this->currentMonth == 12 ? intval($this->currentYear)+1 : $this->currentYear;

        $content = '<div class="header">'.
                        '<a class="arrow previous" href="?mois='.$previousMonth.'&annee='.$previousYear.'"></a>'. // previous link
                        '<span class="title">'.date('M Y', strtotime($this->currentYear.'-'.$this->currentMonth.'-01')).'</span>'. // TODO: set locale | title: Month YYYY
                        '<a class="arrow next" href="?mois='.$nextMonth.'&annee='.$nextYear.'"></a>'. // next link
                    '</div>';

        return $content;
    }
    
    private function _createLabel() {
        $content = '<ul class="label">';

        foreach($this->dayLabels as $label) {
            $content .= '<li>'.$label.'</li>';
        }

        $content .= '</ul>';

        return $content;
    }

    private function _createDay($dayNumber) {
        // currentDay not setted up yet
        if ($this->currentDay == 0) {
            $firstDayWeek = date('N', strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));

            if (intval($dayNumber) == intval($firstDayWeek)) {
                $this->currentDay = 1;
            }
        }
        // currentDay already setted up & not final day
        if (($this->currentDay != 0) && ($this->currentDay <= $this->daysInMonth)) {
            $content = $this->currentDay;;

            $this->currentDate = date('Y-m-d', strtotime($this->currentYear.'-'.$this->currentMonth.'-'.$this->currentDay));
            $this->currentDay ++;
        } else {
            $content = '';
        }

        return '<li id="li-'.($content == '' ? 'clear' : $content).'" class="day">'.$content.'</li>';
    }

    private function _createRow() {
        $weeksInMonth = $this->_weeksInMonth($this->currentMonth, $this->currentYear);

        $content = '<ul class="week">';
        // Create weeks
        for ($i=0; $i < $weeksInMonth; $i++) {
            $content .= '<li>'.
                            '<ul>';

            for ($j=1; $j<=7; $j++) {
                $content .= $this->_createDay($i*7 + $j);
            }

            $content .= '</ul>'.
                    '</li>';
        }

        $content .= '</ul>';

        return $content;
    }

    private function _createBox() {
        $content = '<div class="box">'.
                        $this->_createLabel().
                        $this->_createRow().
                    '</div>';

        return $content;
    }

    /*** PUBLIC METHODS ***/

    public function show() {
        $this->_currentMonthAndYear();
        $this->daysInMonth = $this->_daysInMonth($this->currentMonth, $this->currentYear);
        $content = '<div id="calendar">'.
                        $this->_createTitle().
                        $this->_createBox().
                    '</div>';

        return $content;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
</head>
<body>
    <?php
    $calendar = new Calendar;

    echo $calendar->show();
    ?>
<style>
    #calendar {
        width: 704px;
    }
    #calendar .header {
        text-align: center;
        /* new */
        vertical-align: middle;
        width: 100%;
        height: 30px;
        line-height: 30px;
    }
    .arrow {
        border: solid black;
        border-width: 0 3px 3px 0;
        display: inline-block;
        padding: 3px;
    }
    #calendar .header .previous {
        margin-right: 10px;
        transform: rotate(135deg);
        -webkit-transform: rotate(135deg);
    }
    #calendar .header .next {
        margin-left: 10px;
        transform: rotate(-45deg);
        -webkit-transform: rotate(-45deg);
    }
    #calendar .box {
        height: 564px;
        width: 700px;
    }
    #calendar .box .label {
        background-color: #808080;
        color: white;
    }
    #calendar .box .label, #calendar .box .week, #calendar .box .week li ul {
        margin: 0px;
        list-style-type: none;
        /* new */
        float: left;
        padding: 0px;
    }
    #calendar .box .label li, #calendar .box .week li ul .day {
        float: left;
        width: 97px;
        text-align: center;
        border: 2px solid black;
        /* new */
        margin: 0px;
        padding: 0px;
        margin-right: -1px;
        height: 20px;
        line-height: 20px;
        vertical-align: middle;
    }
    #calendar .box .week li ul .day {
        height: 100px;
        text-align: left;
        line-height: 20px;
    }
</style>
</body>
</html>