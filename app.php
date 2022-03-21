<?php

define('MAX_PINS', 10);
define('NUM_OF_ROUNDS', 10);

class Game
{
    private $round = 1;
    private $wasStrike = false;
    private $wasStrikeInARow = false;
    private $wasSpare = false;
    private $frames = array();

    private $isGameOver = false;
    private $rolls = array();

    public function runFrame()
    {
        if ($this->rolls[$this->getCurrentRound()][0] == MAX_PINS) {
            $this->addPoints();
            $this->enableStrike();
        } else {
            $this->addPoints();
            if ($this->rolls[$this->getCurrentRound()][0] + $this->rolls[$this->getCurrentRound()][1] == MAX_PINS)
                $this->enableSpare();
        }

        if ($this->getCurrentRound() == NUM_OF_ROUNDS) {
            $this->isGameOver = true;
        }
        $this->round++;
    }

    public function roll($knockedPins)
    {
        if ($knockedPins <= $this->getRemainingPins() && $knockedPins > -1) {
            $this->rolls[$this->getCurrentRound()][] = $knockedPins;

            if ($this->isFrameOver())
                $this->runFrame();
        }
    }
    private function isFrameOver()
    {
        if (isset($this->rolls[$this->getCurrentRound()])) { //undef array key
            if ($this->getCurrentRound() == NUM_OF_ROUNDS) {
                if (count($this->rolls[$this->getCurrentRound()]) >= 2) {
                    if ($this->rolls[$this->getCurrentRound()][0] == MAX_PINS || $this->rolls[$this->getCurrentRound()][0] + $this->rolls[$this->getCurrentRound()][1] == MAX_PINS) {
                        return (isset($this->rolls[$this->getCurrentRound()][2])) ?  true :  false;
                    } else {
                        return true;
                    }
                }
            } else {
                if ($this->rolls[$this->getCurrentRound()][0] == MAX_PINS || count($this->rolls[$this->getCurrentRound()]) == 2)
                    return true;
            }
        }
        return false;
    }
    private function addPoints()
    {
        $firstRoll = $this->rolls[$this->getCurrentRound()][0];
        $secondRoll = 0;

        if (isset($this->rolls[$this->getCurrentRound()][1]))
            $secondRoll = $this->rolls[$this->getCurrentRound()][1];

        $sum = $firstRoll + $secondRoll;

        if ($this->wasStrikeInARow) {
            $one_back = array_pop($this->frames);
            $two_back = array_pop($this->frames);
            array_push($this->frames, $two_back + $firstRoll);
            array_push($this->frames, $one_back + $firstRoll);

            $this->disableStrikeInARow();
        }
        if ($this->wasStrike) {
            $one_back = array_pop($this->frames);
            array_push($this->frames, $one_back + $sum);

            if ($firstRoll == MAX_PINS)
                $this->enableStrikeInARow();

            $this->disableStrike();
        }
        if ($this->wasSpare) {
            $one_back = array_pop($this->frames);
            array_push($this->frames, $one_back + $firstRoll);
            $this->disableSpare();
        }
        $total_points = end($this->frames);

        if (isset($this->rolls[$this->getCurrentRound()][2]))
            $sum += $this->rolls[$this->getCurrentRound()][2];

        array_push($this->frames, $total_points + $sum);
    }
    private function getRemainingPins()
    {
        if ($this->getCurrentRound() == NUM_OF_ROUNDS) {
            if (isset($this->rolls[$this->getCurrentRound()])) {
                $firstRoll = $this->rolls[$this->getCurrentRound()][0];
                switch (count($this->rolls[$this->getCurrentRound()])) {
                    case 1:
                        return ($firstRoll == MAX_PINS) ? MAX_PINS : MAX_PINS - $firstRoll;
                    case 2:
                        $secondRoll = $this->rolls[$this->getCurrentRound()][1];
                        return ($secondRoll == MAX_PINS || $firstRoll + $secondRoll == MAX_PINS) ? MAX_PINS : MAX_PINS - $secondRoll;
                }
            } else {
                return MAX_PINS;
            }
        } else {
            return (isset($this->rolls[$this->getCurrentRound()][0])) ? MAX_PINS - $this->rolls[$this->getCurrentRound()][0] : MAX_PINS;
        }
    }
    public function getScore()
    {
        $points = 0 + end($this->frames);

        if (isset($this->rolls[$this->getCurrentRound()]))
            if (count($this->rolls[$this->getCurrentRound()]) >= 1)
                $points += array_sum($this->rolls[$this->getCurrentRound()]);

        return $points;
    }
    private function enableStrike()
    {
        $this->wasStrike = true;
    }
    private function disableStrike()
    {
        $this->wasStrike = false;
    }
    private function enableStrikeInARow()
    {
        $this->wasStrikeInARow = true;
    }
    private function disableStrikeInARow()
    {
        $this->wasStrikeInARow = false;
    }
    private function enableSpare()
    {
        $this->wasSpare = true;
    }
    private function disableSpare()
    {
        $this->wasSpare = false;
    }
    public function getCurrentRound()
    {
        return $this->round;
    }
    public function isGameOver()
    {
        return $this->isGameOver;
    }
    public function getFrames()
    {
        return $this->frames;
    }
}

$game = new Game();
while (!$game->isGameOver()) {
    echo "Round " . $game->getCurrentRound() . "\n";
    $game->roll(readline("Pins knocked down: "));
    echo "\nTotal score: \033[31m", $game->getScore(), " points\033[0m\n\n";
}

foreach ($game->getFrames() as $num => $frame) {
    echo "Round ", $num + 1, ": \033[31m", $frame, " points\033[0m\n";
}
