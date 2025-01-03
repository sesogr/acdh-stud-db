import { createConnection } from "mariadb";
import { computeStats, reducePropertyRecordsToPeople } from "./process";
import {
  findBatchIds,
  getHighestAvailableIds,
  loadBatchOfPropertyRecords,
  writeComparisonBatch,
} from "./database";
import fs from 'node:fs';
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
