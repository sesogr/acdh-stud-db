import { parentPort, workerData } from "worker_threads";
import fs from "node:fs";
import { createConnection } from "mariadb";
import {
  computeBirthRangeStats,
  computeStats,
  convertStringsArraystoDateRanges,
  reducePropertyRecordsToPeople2,
} from "./process";
import {
  loadBirthRangeProperties,
  writeComparisonBatch,
  writeComparisonBatchBirthrange,
} from "./database";
const [ids, credentials]: [number[], {}] = workerData;
const startDate: Date = new Date();
console.log(ids[0], "/", ids[1], "..", ids[ids.length - 1] + " resolving");

createConnection(credentials).then((connection) =>
  loadBirthRangeProperties(connection, ids)
    .then((e) => {
      if (e[0].person_id != ids[0]) throw Error;
      return e;
    })
    .then(reducePropertyRecordsToPeople2)
    .then(convertStringsArraystoDateRanges)
    .then(computeBirthRangeStats)
    .then((comparisons) =>
      writeComparisonBatchBirthrange(connection, comparisons)
    )
    .then(() => {
      parentPort?.postMessage({
        done: true,
        value: {
          ids: ids,
        },
        time: (new Date().getTime() - startDate.getTime()) / 1000,
      });
      connection.end();
    })
    .catch((error) => {
      throw Error(
        error +
          `\n Failed to process ${ids[0]} ... ${ids[1]} to ${ids[ids.length - 1]}`
      );
    })
);
