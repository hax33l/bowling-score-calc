<?php

class Game
{
    private $round = 0;
    private $isStrike = false;
    private $isSecondStrike = false;
    private $isSpare = false;
    private $frames = array();


    public function round()
    {
        $this->roundInfo();
        $first_roll = $this->roll();
        if ($first_roll == 10) {
            $this->addPoints($first_roll);
            $this->toggleStrike();
        } else {
            $second_roll = $this->roll(10 - $first_roll);
            $this->addPoints($first_roll, $second_roll);

            if ($first_roll + $second_roll == 10)
                $this->toggleSpare();
        }

        if ($this->round == 9 and $first_roll == 10) {
            $second_roll = $this->roll();
            $third_roll = $this->roll();
            $this->addPoints($second_roll, $third_roll);
            array_pop($this->frames);
        } elseif ($this->round == 9 and $first_roll + $second_roll == 10) {
            $third_roll = $this->roll();
            $this->addPoints($third_roll);
            array_pop($this->frames);
        }

        $this->scoreOutput();

        if ($this->round == 9) {
            $this->gameOver();
        }
        $this->round++;
    }

    public function roll($max = 10)
    {
        do {
            $pins = readline("Pins knocked down: ");
        } while ($pins < 0 || $pins > $max);

        return $pins;
    }

    public function getScore()
    {
        return end($this->frames);
    }

    private function toggleStrike()
    {
        $this->isStrike = !$this->isStrike;
    }
    private function toggleSecondStrike()
    {
        $this->isSecondStrike = !$this->isSecondStrike;
    }
    private function toggleSpare()
    {
        $this->isSpare = !$this->isSpare;
    }
    private function addPoints($first_roll, $second_roll = 0)
    {
        $sum = $first_roll + $second_roll;
        if ($this->isSecondStrike) {
            $one_back = array_pop($this->frames);
            $two_back = array_pop($this->frames);
            array_push($this->frames, $two_back + $first_roll);
            array_push($this->frames, $one_back + $first_roll);

            $this->toggleSecondStrike();
        }
        if ($this->isStrike) {
            $one_back = array_pop($this->frames);
            array_push($this->frames, $one_back + $sum);

            if ($first_roll == 10)
                $this->toggleSecondStrike();

            $this->toggleStrike();
        }
        if ($this->isSpare) {
            $one_back = array_pop($this->frames);
            array_push($this->frames, $one_back + $first_roll);
            $this->toggleSpare();
        }
        $total_points = $this->getScore();
        array_push($this->frames, $total_points + $sum);
    }
    private function roundInfo()
    {
        echo "\n-------------------------\n";
        echo "|\t Round ", $this->round + 1, "\t|\n";
        echo "-------------------------\n\n";
    }
    private function scoreOutput()
    {
        echo "\nTotal score: \033[31m", $this->getScore(), " points\033[0m\n\n";
    }
    private function gameOver()
    {
        echo "\n-------------------------\n";
        echo "|\tGAME OVER \t|\n";
        echo "-------------------------\n\n";

        foreach ($this->frames as $num => $frame) {
            echo "Round ", $num + 1, ": \033[31m", $frame, " points\033[0m\n";
        }
    }
}

$game1 = new Game();
for ($i = 0; $i < 10; $i++) {
    $game1->round();
}
