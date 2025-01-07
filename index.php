<html>
    <head>
        <title>spunky2</title>
        <link rel='stylesheet' href='style.css'>
    </head> 
    <body>
        <span id='cB'> Welcome to spunky2.com</span>
        <i id='cM'>(Luka FB Lepkowski's personal repo / website)</i>

        <br/><br/><u id='cG'>Things I've made</u><br/><br/>
        
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
                $source = $data[6];
                $rawhtml_download = $data[7];
                $download = $data[8];

                echo "
                <div id='item'>
                    <img id='icon' src='",$img_addr,"'>
                    <div id='inline'>
                        <span id='cB'>",$title,"    </span><i id='cBD'> (Released ",$release,")</i><br/>
                        <i id='cMD'>",$tag,"</i><br/><br/>
                        <p id='cDesc'>",$desc,"</p><br/>
                        <div id='footer'>
                            <span id='cC'>Source code: </span><a id=",$source_id," href=",$rawhtml_source,">",$source,"</a><br/>
                            <span id='cCW'>",$download_desc,": </span><a id=cY href=",$rawhtml_download,">",$download,"</a>
                            <span id='cC'>Source code: </span><a id=cY href=",$rawhtml_source,">",$source,"</a><br/>
                            <span id='cCW'>Info / Download page: </span><a id=cY href=",$rawhtml_download,">",$download,"</a>
                        </div>
                    </div>
                </div>
                
                ";
            }

            $fclose($file);
        }
        
        
        ?>

        
    
    </body>
</html>