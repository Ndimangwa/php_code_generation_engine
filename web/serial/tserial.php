<?php 
exec('stty -F /dev/ttyS0 4800 raw');

$fd=dio_open('/dev/ttyS0',O_RDWR | O_NOCTTY | O_NDELAY)
?>