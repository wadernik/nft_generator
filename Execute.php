<?php

require 'Generator.php';

try {
    main();
} catch (\Exception $e) {
    echo "Было сброшено исключение в процессе исполнения. Прерывание.";
}
