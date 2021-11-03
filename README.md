```
composer update

# Run with PHP 7.4

/opt/homebrew/Cellar/php@7.4/7.4.24_1/bin/php vendor/bin/rector process src/Test.php
 1/1 [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓] 100%

 [ERROR] Could not process "src/Test.php" file, due to:
         "Syntax error, unexpected T_FUNCTION, expecting T_VARIABLE:1043". On line: 38
```