<?php
    header('Content-Type: text/html; charset=UTF-8');
    
    $LEN = 166;
    $grid = [];
    $colorPoints = [];

    function set($index, $value) {
        global $grid;

        while(count($grid) <= $index) {
            array_push($grid, "");
        }

        $grid[$index] = $value;

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

    function color($x, $y, $colorcode) {
        global $colorPoints;

        array_push($colorPoints, [$x, $y, $colorcode]);
    }

    function interpreter_handleColorCode($colorcode) {
        if($colorcode == -1) {
            echo "</span>";
        } else {
            echo "<span id=".$colorcode.">";
        }
    }
?>

<html>
    <head>
        <title>spunky2</title>
        <link rel='stylesheet' href='style.css'>
    </head> 
    <body>
        <span id='cB'> Welcome to spunky2.com</span>
        <i id='cM'>(Luka FB Lepkowski's personal repo / website)</i>

        <br/><br/><u id='cG'>Things I've made</u><br/><br/>
        
        <p id="terminal">
            <?php

            $file = fopen('items.csv', 'r');

            if($file !== false) {
                while(($data = fgetcsv($file)) !== false) {
                    $title = $data[0];
                    $img_addr = $data[1];
                    $release = $data[2];
                    $tag = $data[3];
                    $desc = $data[4];
                    $rawhtml_source = $data[5];
                    $source_id = $data[6];
                    $source = $data[7];
                    $rawhtml_download = $data[8];
                    $download_desc = $data[9];
                    $download = $data[10];

                    /*echo "
                    <div id='item'>

                    </div>
                    
                    ";*/

                    /*                    
                        <div id='inlineText'>
                            <span id='cB'>",$title,"    </span><i id='cBD'> (Released ",$release,")</i><br/>
                            <i id='cMD'>",$tag,"</i><br/><br/>
                        </div>
                        <div id='inline'>
                            <p id='cDesc'>",$desc,"</p><br/>
                                <span id='cC'>Source code: </span><a id=",$source_id," href=",$rawhtml_source,">",$source,"</a><br/>
                                <span id='cCW'>",$download_desc,": </span><a id=cY href=",$rawhtml_download,">",$download,"</a>
                        </div>*/
                        //<img id='icon' src='",$img_addr,"'>
                }

            }
            
            //Generate grid and color point
            
            color(0, 0, "bg");
            fill( 0, "X");
            color($LEN, 0, -1);

            //Process grid and colorpoint
            for($y = 0; $y < sizeof(grid); $y += 1) {
                $line = $grid[$y];
                for($x = 0; $x < $LEN; $x += 1) {
                    //Check for colorPoint
                    for($j = 0; $j < sizeof($colorPoints); $j += 1) {
                        $cp = $colorPoints[$j];
                        if($cp[0] == $x && $cp[1] == $y) {
                            interpreter_handleColorCode($cp[2]);
                        }
                    }
                    echo $line[$x];
                }

                //echo "<br>";
            }
            
            ?>
        </p>

        
    
    </body>
</html>