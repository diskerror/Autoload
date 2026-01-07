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
| 1   | Composer  |          0.354 |         32.767 |          32.767 |       3.181 |
|     | Diskerror |          0.270 |         36.091 |          36.091 |       3.152 |
| 2   | Composer  |          0.345 |         24.048 |          24.048 |       3.181 |
|     | Diskerror |          0.230 |         26.313 |          26.313 |       3.152 |
| 3   | Composer  |          0.331 |         23.739 |          23.739 |       3.181 |
|     | Diskerror |          0.229 |         26.501 |          26.501 |       3.152 |
| 4   | Composer  |          0.362 |         26.959 |          26.959 |       3.181 |
|     | Diskerror |          0.226 |         26.812 |          26.812 |       3.152 |

## OPcache on

The cache is cold on the first run.

| Run | Type      | Init Time (ms) | Load Time (ms) | Total Time (ms) | Memory (MB) |
|-----|-----------|---------------:|---------------:|----------------:|------------:|
| 1   | Composer  |          1.034 |        136.397 |         136.398 |       1.816 |
|     | Diskerror |          0.402 |         44.033 |          44.033 |       0.087 |
| 2   | Composer  |          0.206 |         43.142 |          43.142 |       0.084 |
|     | Diskerror |          0.159 |         44.704 |          44.704 |       0.086 |
| 3   | Composer  |          0.198 |         43.619 |          43.619 |       0.084 |
|     | Diskerror |          0.157 |         45.012 |          45.012 |       0.086 |
| 4   | Composer  |          0.195 |         42.312 |          42.312 |       0.084 |
|     | Diskerror |          0.183 |          58.52 |           58.52 |       0.086 |

## Conclusion

It looks like this program is only slightly useful particularly 
if OPcache is turned on.