# Extracting data buried in cell backgrounds

A user named Villeroy posted a
[forum article](https://forum.openoffice.org/en/forum/viewtopic.php?f=21&t=2762)
containing macro definitions for introspection functions. With

```
=IF(CELL_BACKCOLOR(SHEET(),ROW(A2),COLUMN(A2))>0,"true","")
```

we can check whether a cell has a colored background. The function
CELL_BACKCOLOR() returns -1 for no background color and a positive
integer indicating the color.
