@echo off
    Setlocal EnableDelayedExpansion
    cls
    set currentDirectory=%CD%
	FOR /D %%f IN ("*") DO (
		copy /Y "%currentDirectory%\view.php" "%%~ff"
	)
pause