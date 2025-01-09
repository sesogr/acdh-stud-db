import { parentPort, workerData } from 'worker_threads'; 
  
import mariadb from "mariadb";
import { computeStats, reducePropertyRecordsToPeople } from "./process";
import {
  loadBatchOfPropertyRecords,
  writeComparisonBatch,
} from "./database";

const [ids,credentials] = workerData;
console.log(ids);
mariadb.createConnection(credentials).then((conn) => loadBatchOfPropertyRecords(conn, ids)
  .then(reducePropertyRecordsToPeople)
  .then(computeStats)
  .then((comparisons) => writeComparisonBatch(conn, comparisons))
  .then(() => {
    conn.end();
  }).catch((err) => console.error(err)))
  /*let [ids,connection] = workerData;
  //let connection:Connection = workerData[1];
  getConnectionFromPool.then(() => loadBatchOfPropertyRecords(connection, ids)
    .then(reducePropertyRecordsToPeople)
    .then(computeStats)
    .then((comparisons) => writeComparisonBatch(connection, comparisons))
}))*/