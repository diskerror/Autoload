# Autoload
An autoloader using the Composer generated autoload files and the Ds data structures.
## Usage
Replace "vendor/autoload.php" with:
```
require 'vendor/diskerror/autoload/autoload.php';
```
# Benchmarks
Tests run on Mac Studio M2 Max.

## OPcache off
| Run | Type      | Init Time (ms) | Load Time (ms) | Total Time (ms) | Memory (MB) |
|-----|-----------|---------------:|---------------:|----------------:|------------:|
| 1   | Composer  | 0.367          | 37.651         | 38.018          | 3.521       |
|     | Diskerror | 0.255          | 32.680         | 32.935          | 3.486       |
| 2   | Composer  | 0.348          | 29.307         | 29.655          | 3.521       |
|     | Diskerror | 0.271          | 32.568         | 32.839          | 3.486       |
| 3   | Composer  | 0.342          | 28.984         | 29.326          | 3.521       |
|     | Diskerror | 0.240          | 32.990         | 33.230          | 3.486       |
| 4   | Composer  | 0.353          | 28.769         | 29.122          | 3.521       |
|     | Diskerror | 0.255          | 33.571         | 33.826          | 3.486       |


## OPcache on 
The cache is cold on the first run.

| Run | Type      | Init Time (ms) | Load Time (ms) | Total Time (ms) | Memory (MB) |
|-----|-----------|---------------:|---------------:|----------------:|------------:|
| 1   | Composer  |          2.323 |        269.044 |         271.367 |       6.129 |
|     | Diskerror |          1.620 |         54.726 |          56.346 |       3.646 |
| 2   | Composer  |          0.262 |         52.676 |          52.938 |       3.894 |
|     | Diskerror |          0.187 |         53.217 |          53.404 |       3.642 |
| 3   | Composer  |          0.228 |         50.786 |          51.014 |       3.894 |
|     | Diskerror |          0.203 |         53.575 |          53.778 |       3.642 |
| 4   | Composer  |          0.217 |         49.253 |          49.470 |       3.894 |
|     | Diskerror |          0.181 |         50.715 |          50.896 |       3.642 |

