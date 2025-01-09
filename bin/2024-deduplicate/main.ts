import mariadb from "mariadb";
import { computeStats, reducePropertyRecordsToPeople } from "./process";
import { get4batches } from "./createbatches";
import { Worker } from 'worker_threads'; 
import { resolve } from "node:dns";
import fs from "node:fs"

const pool = mariadb.createPool({
  host: "localhost",
  port: 13006,
  database: "rksd",
  charset: "utf8",
  user: "rksd",
  password: "nJkyj2pOsfUi",
  connectionLimit: 10,
});
if (fs.existsSync("ids.json")){
  fs.rmSync("ids.json");
}
get4batches(pool)
console.log("test");
/*
const idfile = fs.readFileSync("ids.json","ascii").split("\n")
let workers:Worker[] = [];
for (let i = 1; i < 5; i++) {
  let ids = idfile[i].substring(1,idfile[i].length - 1)
  let workerData = [ids,pool];
  const worker = new Worker( 
    './worker.js', { workerData }); 
  worker.on('message', resolve); 
  workers.push(worker);
}
workers.forEach((worker) => worker.postMessage("start"))

/*


/*
import fs from 'node:fs';
import fsPromises from 'node:fs/promises';




const credentials = {
  host: "localhost",
  port: 13006,
  database: "rksd",
  charset: "utf8",
  user: "rksd",
  password: "nJkyj2pOsfUi",
};
const BATCH_SIZE = 1024;



createConnection(credentials).then((connection) =>
  getBatchesFromFile().then((ids) => {
    console.log(ids[0], "/", ids[1], "..", ids[ids.length - 1]);
    return ids;
  })
  .then((ids) => loadBatchOfPropertyRecords(connection, ids))
  .then(reducePropertyRecordsToPeople)
  .then(computeStats)
  .then((comparisons) => writeComparisonBatch(connection, comparisons))
  .then(() => connection.end()).catch((message) => console.error(message)));

type GetBatchesFromFile = () => Promise<number[]>;
export const getBatchesFromFile: GetBatchesFromFile = () =>
  fsPromises.readFile("ids.json","ascii")
    .then((file) => {
    let fileArray = file.split("\n");
    let stringids = fileArray[1].substring(1,fileArray[1].length - 1).split(",");
    fsPromises.writeFile("ids.json","")
    fsPromises.writeFile("ids.json",fileArray[0])
    for (let i = 2;fileArray.length-1;i++){
      fsPromises.writeFile("ids.json",fileArray[0])
    }
    let ids:number[] = []
    stringids.forEach((id) => ids.push(parseInt(id)))
    return ids;
    }).then()
/*
createConnection(credentials).then((connection) =>
  getHighestAvailableIds(connection)
    .then((limits) => findBatchIds(connection, limits, BATCH_SIZE))
    .then((ids) => {
      console.log(ids[0], "/", ids[1], "..", ids[ids.length - 1]);
      return ids;
    })
    .then((ids) => loadBatchOfPropertyRecords(connection, ids))
    .then(reducePropertyRecordsToPeople)
    .then(computeStats)
    .then((comparisons) => writeComparisonBatch(connection, comparisons))
    .then(() => connection.end()).catch((message) => console.error(message)),
);
*/