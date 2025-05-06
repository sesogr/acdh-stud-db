# Style additions for deduplication

Since the whole style system has been rewritten here for ÖAW and no longer matches SednaSoft's local dev structure,
SednaSoft will just hand over their changes for ÖAW to apply them themselves:

```
main section ul { list-style-type: none; }
main section ul.dupes a { text-decoration: none; font-size: 80%; font-weight: bold; padding: 2px 4px 0; border-radius: 8px; }
main section td.orig span { font-size: 80%; font-weight: bold; padding: 2px 4px 0; border-radius: 8px; background-color: #ccc; }
main section td.dupe span { font-size: 80%; font-weight: bold; color: #000; padding: 2px 4px 0; border-radius: 8px; }
main section tr.a td span { font-size: 80%; font-weight: bold; padding: 2px 4px 0; border-radius: 8px; background-color: #ccc; }
main section td span { font-size: 80%; font-weight: bold; color: #000; padding: 2px 4px 0; border-radius: 8px; }
main section .hide td { display: none; }
main section :is(span,a).b { background-color: #f5c0cf; } /* 12:00 000° */
main section :is(span,a).c { background-color: #a0dde8; } /* 07:00 210° */
main section :is(span,a).d { background-color: #f0c8a8; } /* 02:00 060° */
main section :is(span,a).e { background-color: #c0d0fb; } /* 09:00 270° */
main section :is(span,a).f { background-color: #cbd7a9; } /* 04:00 120° */
main section :is(span,a).g { background-color: #e9c2e5; } /* 11:00 330° */
main section :is(span,a).h { background-color: #a3dfd2; } /* 06:00 180° */
main section :is(span,a).i { background-color: #f7c2b9; } /* 01:00 030° */
main section :is(span,a).j { background-color: #acd7f6; } /* 08:00 240° */
main section :is(span,a).k { background-color: #e1d0a2; } /* 03:00 090° */
main section :is(span,a).l { background-color: #d7c8f5; } /* 10:00 300° */
main section :is(span,a).m { background-color: #b4ddbb; } /* 05:00 150° */
```

What is `main section` for SednaSoft's index.php, would probably map to `#wrap .main-content` for ÖAW.
