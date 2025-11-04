# Spreadsheet Converter

## TLDR; 
- create a script that would take an xls as input and run through some business logic to create a new xls with additional rows and columns.
- create another script that would take the output from above, run through more business logic to create an xls with more rows and columns

## Long Version
the ask came in 2 parts. 18 months apart......sometime around 2018-ish for the first one, and then in 2020, shortly before i was made redundant lolz. I have tried to get an original xls to test with, but I can't get a response from the original requester. you will have to take my word for it that this actually DID accomplish the business goal. 

Originally, some ad-ops person had to spend 2 hours to generate the output created in the FIRST php script. _Only if there were 0 mistakes_. 

Then, a `final` spreadsheet took approx 2 more hours, so 4 hours to create the `final` xls, and there were multiple files that had to be created weekly. 


This set of scripts would take the original file as input – run it – create a new xls file. the `new` xls file would then be used as the input for the 2nd script, to create the `final` xls. 

Both scripts ran in < 1 min, with the approx max columns in each file (65,536)

example usage 

`php getColumnCountFromCell.php < original.xls | parseSpreadSheetFinal.php`



