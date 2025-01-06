import { workerData } from 'worker_threads'; 
  
import { Connection } from "mariadb";
import { computeStats, reducePropertyRecordsToPeople } from "./process";
import {
  loadBatchOfPropertyRecords,
  writeComparisonBatch,
} from "./database";


let [ids,] = workerData;
let connection:Connection = workerData[1];
loadBatchOfPropertyRecords(connection, ids)
  .then(reducePropertyRecordsToPeople)
  .then(computeStats)
  .then((comparisons) => writeComparisonBatch(connection, comparisons))
  .then(() => connection.end()).catch((message) => console.error(message));
