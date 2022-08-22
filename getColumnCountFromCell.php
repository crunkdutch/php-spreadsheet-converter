<?php

$originalFile = $argv[1];
$outputFile = substr($originalFile, 0, strlen($originalFile) - 4) . "_part1.csv";

$DEFAULT_STRING = 'DEFAULT';

$allColumnFourValues = array();
$allRows = array();

$row = 0;

if (($spreadSheet = fopen($originalFile, "r")) !== FALSE) {
  if (($newSpreadSheet = fopen($outputFile, "w")) !== FALSE) {
    while (($data = fgetcsv($spreadSheet, 1000, ",")) !== FALSE) {
      $allRows[] = $data;
      $row++;
      
      /*
        parse every rows 4th column, and explode on '_' 
        the max number of values will determine how many
        keyword columns there will be.
      */

      if ($row > 0) {
        $columnFourValues = explode("_", $data[2]);
        $allColumnFourValues[] = $columnFourValues;
      }
    }
     
    /*
      get the max and min value of each new array
      figure out the difference so we can add 'default'
      the correct amount of times.
    */

    $keywordColumnAmount = (count(max($allColumnFourValues)));
    $smallestArrayLength = (count(min($allColumnFourValues)));
    $updatedColumnValuesArray = array();    

    for ($i = 0; $i < $row-1; $i++) {
      $eachArray = $allColumnFourValues[$i];
      if (count($eachArray) < ($keywordColumnAmount)) {
        if (count($eachArray) == ($keywordColumnAmount - ($keywordColumnAmount - $smallestArrayLength))) {
          array_push($eachArray, $DEFAULT_STRING, $DEFAULT_STRING);
        }
        else {
          $eachArray[] = $DEFAULT_STRING;
        }        
      }
      $updatedColumnValuesArray[] = $eachArray;
    }        
    
    //add everything new and old back to the csv
    
    for ($j = 0; $j < $row-1; $j++) {
      if ($j > 0) {
        $newRowWithValues = array(
          $allRows[$j][0], //name always first column
          $allRows[$j][1]  //date always second column
        );

        // use the `$keywordColumnAmount` to determine how many
        // more columns to add into the final array `$newRowWithValues

        for ($k = 0; $k < $keywordColumnAmount; $k++) {
          array_push($newRowWithValues, strtoupper($updatedColumnValuesArray[$j][$k]));
        }

        fputcsv($newSpreadSheet, $newRowWithValues);
      }
    }
    
    fclose($newSpreadSheet);
  }

  fclose($spreadSheet);
}
?>