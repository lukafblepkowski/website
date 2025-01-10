<?php
    header('Content-Type: text/html; charset=UTF-8');
    
    $LEN = 166;
    $COLUMNS = 4;
    $grid = [];
    $colorPointMap = [];
    $terminatorColorPointMap = [];
    $unprocessedColorPoints = [];

    function set($index, $value) {
        global $grid;
        global $LEN;
        
        while(count($grid) <= $index) {
            array_push($grid, str_repeat(" ", $LEN));
        }

        $grid[$index] = $value;

        return $grid;
    }

    function setXY($x, $y, $char) {
        global $grid;
        global $LEN;

        while(count($grid) <= $y) {
            array_push($grid, str_repeat(" ", $LEN));
        }

        $grid[$y][$x] = $char;

        return $grid;
    }

    function fill($index, $char) {
        global $LEN;
        global $grid;

        $str = "";
        for($i = 0; $i < $LEN; $i += 1) {
            $str .= $char;   
        }

        set($index, $str);
    }

    function fillLRPartial($x1, $x2, $y, $char) {
        $cps = fetchUnprocessed();

        for($i = 0; $i < count($cps); $i += 1) {
            $cp = $cps[$i];
            setColorPoint([$x1, $y], $cp[0], $cp[1]);
            setColorPoint([$x2, $y], $cp[2], $cp[1], true);
        }

        for($i = $x1; $i <= $x2; $i++) {
            setXY($i, $y, $char);
        }
    }

    function fillUDPartial($x, $y1, $y2, $char) {
        $cps = fetchUnprocessed();

        for($i = $y1; $i <= $y2; $i++) {
            setXY($x, $i, $char);

            for($j = 0; $j < count($cps); $j += 1) {
                $cp = $cps[$j];
                setColorPoint([$x, $i], $cp[0], $cp[1]);
                setColorPoint([$x, $i], $cp[2], $cp[1], true);
            }
        }
    }

    function color($colorcode, $additional = "", $closer=-1) {
        global $unprocessedColorPoints;

        $colorPoint = [$colorcode, $additional, $closer];

        array_push($unprocessedColorPoints, $colorPoint);
    }

    function fetchUnprocessed() {
        global $unprocessedColorPoints;
        
        $temp = [];
        for($i = 0; $i < count($unprocessedColorPoints); $i++) {
            array_push($temp, $unprocessedColorPoints[$i]);
        }

        $unprocessedColorPoints = [];
        
        return $temp;
    }

    function setColorPoint($XY, $colorcode, $additional, $terminator = false) {
        global $colorPointMap;
        global $terminatorColorPointMap;

        $code = $XY[0]." ".$XY[1];
        if($terminator) {
            if(!isset($terminatorColorPointMap[$code])) $terminatorColorPointMap[$code] = [];
            array_push($terminatorColorPointMap[$code], [$colorcode, $additional]);
        } else {
            if(!isset($colorPointMap[$code])) $colorPointMap[$code] = [];
            array_push($colorPointMap[$code], [$colorcode, $additional]);
        }
    }

    function interpreter_handleColorCode($colorcode, $additional) {
        if($colorcode == -1) {
            echo "</span>";
        } else if($colorcode == -2) {
            echo "<i>";
        } else if($colorcode == -3) {
            echo "</i>";
        } else if($colorcode == -4) {
            echo "<a id=".$additional[0]." href=".$additional[1].">";
        } else if($colorcode == -5) {
            echo "</a>";
        } else if($colorcode == -6) {
            echo "<u>";
        } else if($colorcode == -7) {
            echo "</u>";
        } else {
            echo "<span id=".$colorcode.">";
        }
    }

    function inscribeString($string, $l, $r, $XY) {
        $cps = fetchUnprocessed();
        $split = explode(" ", $string);

        for($i = 0; $i < count($split); $i++) {
            $word = $split[$i];

            if($XY[0] + strlen($word) > $r) {
                $XY[1] += 1;
                $XY[0] = $l;
            }

            for($k = 0; $k < count($cps); $k += 1) {
                $cp = $cps[$k];
                setColorPoint($XY, $cp[0], $cp[1]);
            }

            for($j = 0; $j < strlen($word); $j++) {
                setXY($XY[0], $XY[1], $word[$j]);

                if($i + 1 != count($split) || $j + 1 != strlen($word)) $XY[0] += 1;
            }

            for($k = 0; $k < count($cps); $k += 1) {
                $cp = $cps[$k];
                setColorPoint([$XY[0], $XY[1]], $cp[2], $cp[1], true);
            }

            $XY[0] += 1;
        }

        return $XY;
    }
?>

<html>
    <head>
        <title>spunky2</title>
        <link rel='stylesheet' href='style.css'>
    </head> 
    <body>
        <p id="terminal"><?php
            $file = fopen('items.csv', 'r');
            $items = [];

            if($file !== false) {
                while(($data = fgetcsv($file)) !== false) {
                    array_push($items, $data);
                }
            }
            
            color("cB");
            $XY = inscribeString("Welcome to spunky2.com ", 0, $LEN, [$LEN/2-36, 0]);

            color("cM");
            color(-2, "", -3);
            $XY = inscribeString("(Luka FB Lepkowski's personal repo / website)", 0, $LEN,$XY);

            $XY[0] = $LEN/2-11;
            $XY[1] += 2;

            color("cG");
            color(-2, "", -3);
            color(-6, "", -7);
            $XY = inscribeString("Things I've made", 0, $LEN, $XY);
            //Generate grid and color point
            $csize = floor($LEN / $COLUMNS);
            $cheights = array_fill(0, $COLUMNS, 4);
            
            for($i = 0; $i < sizeof($items); $i += 1) {
                $c = $i % $COLUMNS;
                
                $x1 = $csize * $c;
                $x2 = $csize * ($c + 1) - 2;
                $y = $cheights[$c];
                color("bg");
                fillLRPartial($x1, $x2, $y, "@");
                $cheights[$c] += 1;
                
                $XY = [$x1 + 1, $y + 1];

                //Title
                color("cB");
                $XY = inscribeString($items[$i][0], $x1 + 1, $x2 - 1, $XY);
                
                $XY[0] += 1;

                //Release Date
                color(-2, "", -3);
                color("cBD");
                $XY = inscribeString("(Released ".$items[$i][2].")", $x1 + 1, $x2 - 1, $XY);

                $XY[0] = $x1 + 1;
                $XY[1] += 1;

                //Tag
                color("cMD");
                color(-2, "", -3);
                $XY = inscribeString($items[$i][3], $x1 + 1, $x2 - 1, $XY);

                $XY[0] = $x1 + 1;
                $XY[1] += 2;

                //Description
                color("cDesc");
                $XY = inscribeString($items[$i][4], $x1 + 1, $x2 - 1, $XY);

                $XY[0] = $x1 + 1;
                $XY[1] += 2;

                //Source code
                color("cC");
                $XY = inscribeString("Source code:", $x1 + 1, $x2 - 1, $XY);

                $XY[0] += 1;

                color(-4, [$items[$i][6], $items[$i][5]], -5);
                $XY = inscribeString($items[$i][7], $x1 + 1, $x2 - 1, $XY);
                
                $XY[0] = $x1 + 1;
                $XY[1] += 1;
            
                //Other link
                color("cCW");
                $XY = inscribeString($items[$i][9].":", $x1 + 1, $x2 - 1, $XY);
                
                $XY[0] += 1;

                color(-4, ["cY", $items[$i][8]], -5);
                $XY = inscribeString($items[$i][10], $x1 + 1, $x2 - 1, $XY);

                $XY[0] = $x1 + 1;
                $XY[1] += 1;
                
                //Bottom border
                color("bg");
                fillLRPartial($x1, $x2, $XY[1], "@");

                //Left border
                color("bg");
                fillUDPartial($x1, $y, $XY[1], "@");

                //Right border
                color("bg");
                fillUDPartial($x2, $y, $XY[1], "@");


                $cheights[$c] += ($XY[1] - ($y + 1)) + 2;
            }

            //Process grid and colorpoint
            for($y = 0; $y < sizeof($grid); $y += 1) {
                $line = $grid[$y];
                for($x = 0; $x < $LEN; $x += 1) {
                    $cp = $colorPointMap[$x." ".$y];
                    if(isset($cp)) {
                        for($j = 0; $j < count($cp); $j++) {
                            interpreter_handleColorCode($cp[$j][0], $cp[$j][1]);
                        }
                    }
                
                    echo htmlspecialchars($line[$x]);     
                    
                    $cp = $terminatorColorPointMap[$x." ".$y];
                    if(isset($cp)) {
                        for($j = 0; $j < count($cp); $j++) {
                            interpreter_handleColorCode($cp[$j][0], $cp[$j][1]);
                        }
                    }
                }

                echo "<br>";
            }
            
            ?>
        </p>

        
    
    </body>
</html>