import { parentPort, workerData } from 'worker_threads'; 
  
import mariadb from "mariadb";
import { computeStats, reducePropertyRecordsToPeople } from "./process";
import {
  loadBatchOfPropertyRecords,
  writeComparisonBatch,
} from "./database";
const [ids,credentials] = workerData;
console.log(ids[0], "/", ids[1], "..", ids[ids.length - 1] + " resolving")
mariadb.createConnection(credentials).then((conn) => loadBatchOfPropertyRecords(conn, ids)
  .then(reducePropertyRecordsToPeople)
  .then(computeStats)
  .then((comparisons) => {
    writeComparisonBatch(conn, comparisons)}
  )
  .then(() => {
    console.log(ids[0], "/", ids[1], "..", ids[ids.length - 1] + " done")
    conn.end();
  }))
  /*let [ids,connection] = workerData;
  //let connection:Connection = workerData[1];
  getConnectionFromPool.then(() => loadBatchOfPropertyRecords(connection, ids)
    .then(reducePropertyRecordsToPeople)
    .then(computeStats)
    .then((comparisons) => writeComparisonBatch(connection, comparisons))
}))*/