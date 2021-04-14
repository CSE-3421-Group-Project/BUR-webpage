I used this sql command to import the .csv files

load data infile 'appointment.csv' 
into table appointment
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;
