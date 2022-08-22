<?php

$originalFileName = $argv[1];
$outputFileName = substr($originalFileName, 0, strlen($originalFileName) - 4) . "_output.csv";

$nameValueMap = array(
  'GEO' => 'Geo_Suppression',
  'CLD' => 'Cold_States',
  'HOT' => 'Warm_States',
  'DEF' => 'DEFAULT'
);

$row = 0;
$DEFAULT_STRING = 'DEFAULT';
$SCHEDULE_STRING = 'Schedule';
$ROTATION_STRING = 'Rotation';
$lastDate = "";
$lastKeyword = ""; 
$lastName = ""; 
$lastMoxie = "";
$lastNewest = "";
$lastZipCode = "";
$zipCodeGroup = "";        

$chunk = 0;
$subChunk = 0;
$total = 0;

if (($spreadSheet = fopen($originalFileName, "r")) !== FALSE) {
  if (($newSpreadSheet = fopen($outputFileName, "w")) !== FALSE) {
    while (($data = fgetcsv($spreadSheet, 1000, ",")) !== FALSE) {
      $allRows[] = $data;
      if ($row === 0){
        for ($i = 0; $i < count($data)-1; $i++){
          //first 4 always the same 
          if ($i <= 3) {
            switch ($i) {
              case 0:
                $data[0] = 'version_name';
                break;
              case 1:
                $data[1] = 'serving_type';
                break;
              case 2:
                $data[2] = 'serving_value';
                break;
              case 3:
                $data[3] = 'zip_code_group';
                break;
              default:
                break;
            }
          }
        }
        $columnLabelArray = array($data[0], $data[1], $data[2], $data[3]);
        $keywordColumns = count($data) - 2; 
        for ($j = 0; $j < $keywordColumns; $j++) {
          $columnLabelArray[] = "keyword";
        }
        array_push($columnLabelArray, "serving_type", "serving_value");
        fputcsv($newSpreadSheet, $columnLabelArray);
      }

      //begin logic for determining `default` rows to be added
      if ($row >= 1) {
        $currentName = $allRows[$row-1][0]; 
        $currentDate = $allRows[$row-1][1]; 
        $currentMoxie = $allRows[$row-1][2];
        $currentKeyword = $allRows[$row-1][3];
        $currentNewestKeyword = $allRows[$row-1][4];    

        $zipCodeGroupStart = strpos($currentName, '_RE~');
        $zipCodeGroupShort = substr($currentName, ($zipCodeGroupStart+4), 3);
        
        $zipCodeGroup = $nameValueMap[$zipCodeGroupShort];
        $currentZipCode = $zipCodeGroup;

        if ($currentNewestKeyword == $currentKeyword && $currentKeyword == $currentMoxie && $currentKeyword == $lastKeyword) {
          $chunk ++;
          $lastNewest = "";         
        } elseif ($currentNewestKeyword == $lastNewest && $currentKeyword == $lastKeyword && $currentMoxie == $lastMoxie){ 
          $chunk ++;
          $lastKeyword = ""; 
        } elseif ($currentNewestKeyword == $lastNewest && $currentKeyword == $lastKeyword && $currentKeyword == $DEFAULT_STRING){ 
          /*
            these have a chance of having sub-chunks
            do the math now for % cuz this is a new chunk starting
          */
           //take the last row and insert, with the percent value in a new column.
          $lastRow = array(
              $lastName,
              $SCHEDULE_STRING,
              $lastDate, 
              $lastZipCode,
              $lastMoxie, 
              $lastKeyword, 
              $lastNewest, 
              $ROTATION_STRING,
              $DEFAULT_STRING
            );
          
          fputcsv($newSpreadSheet, $lastRow);

          $chunk = 0;
        } else{
          if ($chunk > 0){
             //take the last row and insert, with the percent value in a new column.
            $lastRow = array(
              $lastName,
              $SCHEDULE_STRING,
              $lastDate, 
              $lastZipCode,
              $lastMoxie, 
              $lastKeyword, 
              $lastNewest, 
              $ROTATION_STRING,
              $DEFAULT_STRING
            );

            fputcsv($newSpreadSheet, $lastRow);
          }
          // this is for 100%-ers
          if ($chunk == 0 && $row > 2){
            $lastRow = array(
              $lastName,
              $SCHEDULE_STRING,
              $lastDate, 
              $lastZipCode,
              $lastMoxie, 
              $lastKeyword, 
              $lastNewest, 
              $ROTATION_STRING,
              $DEFAULT_STRING
            );
            
            fputcsv($newSpreadSheet, $lastRow);
          }
          $chunk = 0;
        }
        
        $lastDate = $currentDate;
        $lastKeyword = $currentKeyword;
        $lastName = $currentName;
        $lastMoxie = $currentMoxie;
        $lastNewest = $currentNewestKeyword;
        $lastZipCode = $zipCodeGroup;

        //write each row back into the spreadsheet
        $newRow = array(
          $currentName,
          $SCHEDULE_STRING, 
          $currentDate,
          $currentZipCode,
          $currentMoxie, 
          $currentKeyword, 
          $currentNewestKeyword,
          $ROTATION_STRING,
          1 // verify this
        );
        
        fputcsv($newSpreadSheet, $newRow);
      }
      $row++;
    }
    fclose($newSpreadSheet);
  }
  fclose($spreadSheet);
}
?>