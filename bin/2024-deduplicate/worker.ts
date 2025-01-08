import { parentPort, workerData } from 'worker_threads'; 
  
import mariadb from "mariadb";
import { computeStats, reducePropertyRecordsToPeople } from "./process";
import {
  loadBatchOfPropertyRecords,
  writeComparisonBatch,
} from "./database";

const [ids,pool] = workerData;


parentPort?.on('message',(query: string) => {
    pool.getConnection().then((conn:mariadb.PoolConnection) => loadBatchOfPropertyRecords(conn, ids)
      .then(reducePropertyRecordsToPeople)
      .then(computeStats)
      .then((comparisons) => writeComparisonBatch(conn, comparisons))
      .finally(() => {
        conn.release(); // Release the connection back to the pool
      }))})
  /*let [ids,connection] = workerData;
  //let connection:Connection = workerData[1];
  getConnectionFromPool.then(() => loadBatchOfPropertyRecords(connection, ids)
    .then(reducePropertyRecordsToPeople)
    .then(computeStats)
    .then((comparisons) => writeComparisonBatch(connection, comparisons))
}))*/