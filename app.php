<?php

class Game
{

    public $round = 0;
    public $bonus = 0;
    public $frames = array(0);


    public function round()
    {

        echo "\n-------------------------\n";
        echo "|\t Round ", $this->getRound() + 1, "\t|\n";
        echo "-------------------------\n\n";

        $first_roll = $this->roll();
        $sum = $first_roll;

        if ($first_roll == 10) {
            if ($this->bonus == 1 or $this->bonus == 2) {
                $this->frames[$this->getRound()] += $first_roll;
                $this->bonus -= 1;
            } elseif ($this->bonus == 3) {
                $this->frames[$this->getRound() - 1] += $first_roll;
                $this->frames[$this->getRound()] += $first_roll * 2;
                $this->bonus -= 2;
            }
            $this->pushFrame($this->frames[$this->getRound()] + $first_roll);
            $this->scoreOutput(1, $first_roll);

            $this->bonus += 2;
        } else {
            if ($this->bonus == 3) {
                $this->frames[$this->getRound() - 1] += $first_roll;
                $this->frames[$this->getRound()] += $first_roll;
                $this->bonus -= 1;
            }
            if ($this->bonus) {
                $this->frames[$this->getRound()] += $first_roll;
                $this->bonus -= 1;
            }
            $this->scoreOutput(1, $first_roll);
            $second_roll = $this->roll(10 - $first_roll);
            $sum += $second_roll;

            if ($this->bonus) {
                $this->frames[$this->getRound()] += $second_roll;
                $this->bonus -= 1;
            }
            if ($second_roll + $first_roll == 10) {
                $this->bonus += 1;
            }

            $this->pushFrame($this->frames[$this->getRound()] + $first_roll + $second_roll);

            $this->scoreOutput(2, $first_roll, $second_roll);
        }

        //last round logic
        if ($this->round == 9 and $first_roll == 10) {

            $score = array_pop($this->frames);
            $second_roll = $this->roll();

            if ($this->bonus) {
                $this->frames[$this->getRound()] += $second_roll;
            }

            $this->pushFrame($score + $second_roll);

            $this->scoreOutput(1, $second_roll);

            if ($second_roll == 10) {
                $this->frames[10] += $second_roll;
            }

            if ($this->round == 9 and ($first_roll + $second_roll == 10 or $second_roll == 10 or $first_roll == 10)) {
                $score = array_pop($this->frames);
                $third_roll = $this->roll();
                $this->pushFrame($score + $third_roll);
                $this->scoreOutput(3, $third_roll);
            }
        } elseif ($this->round == 9 and ($first_roll + $second_roll == 10)) {
            $score = array_pop($this->frames);
            $third_roll = $this->roll();
            $this->pushFrame($score + $third_roll);
            $this->scoreOutput(3, $third_roll);
        }

        if ($this->round == 9) {
            echo "\n-------------------------\n";
            echo "|\tGAME OVER \t|\n";
            echo "-------------------------\n\n";
            array_shift($this->frames);

            foreach ($this->frames as $num => $frame) {
                echo "Round ", $num + 1, ": \033[31m", $frame, " points\033[0m\n";
            }
        } elseif ($this->bonus) {
            echo "\nRolls with spare/strike bonus: ", $this->bonus, "\n";
        }
        $this->round++;
    }

    public function roll($max = 10)
    {
        $pins = 0;

        do {
            $pins = readline("Pins knocked down: ");
        } while ($pins < 0 or $pins > $max); //basic input validation

        return $pins;
    }

    public function getScore()
    {
        return end($this->frames);
    }

    public function getRound()
    {
        return $this->round;
    }

    public function scoreOutput($i, $first_roll = 0, $second_roll = 0)
    {
        switch ($i) {
            case 1:
                echo "\nYou scored: \033[31m$first_roll pins\033[0m", " (", 10 - $first_roll - $second_roll, " left)", "\nTotal score: \033[31m", $this->getScore(), " points\033[0m\n\n";
                break;
            case 2:
                echo "\nYou scored: \033[31m$second_roll pins\033[0m", " (", 10 - $first_roll - $second_roll, " left)", "\nTotal score: \033[31m", $this->getScore(), " points\033[0m\n\n";
                break;
            case 3:
                echo "\nYou scored: \033[31m$first_roll pins\033[0m\nTotal score: \033[31m", $this->getScore(), " points\033[0m\n\n";
                break;
        }
    }

    public function pushFrame($value)
    {
        array_push($this->frames, $value);
    }
}

$game1 = new Game();

for ($i = 0; $i < 10; $i++) {
    $game1->round();
}
