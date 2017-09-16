<?php

class Benchmark
{
    private $point;
    private $key = 0;

    public function __construct()
    {
        $this->set("start");
    }

    public function set($name = null)
    {
        $key = $this->key = $this->key+1;
        if (!$name) {
            $this->point[$key]["key"] = "Breakpoint_" . $key;
        } else {
            $this->point[$key]["key"] = $name;
        }
        $this->point[$key]["time"] = microtime(true);
        if ($key === 1) {
            $this->point[1]["totaltime"] = $this->point[1]["showtime"] = sprintf('%0.5f', 0);
        } else {
            $this->point[$key]["showtime"] = sprintf('%0.5f', ($this->point[$key]["time"] - $this->point[$key-1]["time"]) * 1000);
            $this->point[$key]["totaltime"] = sprintf('%0.5f', ($this->point[$key]["time"] - $this->point[1]["time"]) * 1000);
        }
        $this->point[$key]["usage"] = sprintf('%01.2f Byte', memory_get_usage());
        $this->point[$key]["usage_human"] = sprintf('%01.2f MB', memory_get_usage() / 1048576);
        $this->point[$key]["usage_peak"] = sprintf('%01.2f Byte', memory_get_peak_usage());
        $this->point[$key]["usage_peak_human"] = sprintf('%01.2f MB', memory_get_peak_usage() / 1048576);
    }

    public function display()
    {
        $this->set("display");
        if (php_sapi_name() === 'cli') {
            $this->displayConsole();
        } else {
            $this->displayHtml();
        }
    }

    public function displayHtml()
    {
        echo "<br/><br/><div style='position: fixed; bottom: 0px;'>ベンチ統計情報生成しました<br/>";
        echo "<table border='1' class='table table-bordered'>";
        echo "<tr>";
        echo "<th style='color: black;'>ブレークポイント</th>";
        echo "<th style='color: black;'>総時間(ミニ秒)</th>";
        echo "<th style='color: black;'>単位時間(ミニ秒)</th>";
        echo "<th style='color: black;'>メモリ消費(バイト)</th>";
        echo "<th style='color: black;'>メモリ消費(メガバイト)</th>";
        echo "<th style='color: black;'>ピークメモリ消費(バイト)</th>";
        echo "<th style='color: black;'>ピークメモリ消費(メガバイト)</th>";
        echo "</tr>";
        foreach ($this->point as $key => $value) {
            echo "<tr>";
            echo "<td style='color: black;'>" . $value["key"] . "</td>";
            echo "<td style='color: black;'>" . $value["totaltime"] . "</td>";
            echo "<td style='color: black;'>" . $value["showtime"] . "</td>";
            echo "<td style='color: black;'>" . $value["usage"] . "</td>";
            echo "<td style='color: black;'>" . $value["usage_human"] . "</td>";
            echo "<td style='color: black;'>" . $value["usage_peak"] . "</td>";
            echo "<td style='color: black;'>" . $value["usage_peak_human"] . "</td>";
            echo "</tr>";
        }
        echo "</table></div>";
    }

    public function displayConsole()
    {
        echo "ベンチ統計情報生成しました", PHP_EOL;
        echo "----------------------------------------------------------------------------------------", PHP_EOL;
        echo "| ブレークポイント | 総時間(ミニ秒) | 単位時間(ミニ秒) | メモリ消費(バイト) | メモリ消費(メガバイト)| ピークメモリ消費(バイト) | ピークメモリ消費(メガバイト)|", PHP_EOL;
        foreach ($this->point as $key => $value) {
            echo "| " . $value["key"] . " | " . $value["totaltime"] . " | " . $value["showtime"] . " | " . $value["usage"] . " | " . $value["usage_human"] . " |" . $value["usage_peak"] . " | " . $value["usage_peak_human"] . " |", PHP_EOL;
        }
        echo "----------------------------------------------------------------------------------------", PHP_EOL;
    }
}
