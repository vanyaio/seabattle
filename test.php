<?php
$data = new DateTime('2018-08-17 05:00:00');

echo $data->format('Y-M-D H:I:s')."<br>";

$sec_to_add = 70;
$data->add(new DateInterval('PT' . $sec_to_add . 'S'));

echo $data->format('Y-m-d H:I:s');


?>
