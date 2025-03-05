# Ordering of the Todos 

## run docker compose up
1. [ ] database and php container runnign

## starting / inserting fixes .sql files
1. [ ] 2019-dump-before-changes.sql
2. [ ] 2019-all-changes.sql

## starting / inserting 2023 sql files
1. [ ] 01-doubtful-columns.sql
2. [ ] 02-data-additions.sql
3. [ ] 03-create-similarity-graph.sql
4. [ ] 04-create-property-weights.sql
5. [ ] 05-fill-property-weights.sql
6. [ ] 06-create-similarity-graph-birthrange

## installing and starting deduplication processes
1. [ ] go inside ./bin/2024-deduplicate
2. [ ] npm i
3. [ ] go into main.ts 
4. [ ] resize workercount and batchsize
5. [ ] decomment the first createbatches 
6. [ ] tsc
7. [ ] loop node main.js until it throws "no more ids above *max id count*
8. [ ] comment the first createbatches again
8. [ ] repeat 5. with the other createbatches 

## combine tables with 07.sql
1. [ ] 07-combine-similarity-graphs