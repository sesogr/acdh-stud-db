import { getbatches } from "./createbatches";
import fs from "node:fs";
import { promiseWorker } from "./mainWorker";
import { ComparisonWorkers } from "./types";

const path = "ids.json";
const credentials = {
  host: "localhost",
  port: 13006,
  database: "rksd",
  charset: "utf8",
  user: "rksd",
  password: "nJkyj2pOsfUi",
};

// function to read the json file to create as many workers as needed
function createworker(workerpath: string = "./worker.js") {
  let idfile = fs.readFileSync(path, "utf-8");
  const id: number[][] = JSON.parse(idfile);
  const workers: any[] = [];
  const workerLimit = Math.min(id.length, workercount + 1);
  for (let i = 1; i < workerLimit; i++) {
    const ids = id[i];
    workers.push(
      promiseWorker<ComparisonWorkers>(ids, credentials, workerpath),
    );
  }
  fs.writeFileSync(
    "ids.json",
    JSON.stringify([id[0], ...id.slice(workerLimit)], null, 2),
    { flag: "w" },
  );
  Promise.allSettled(workers).then((workerResults) => {
    workerResults.forEach((e) => {
      if (e.status === "rejected") {
        console.error(e.reason, "failed");
        fs.writeFileSync("error.log", e.reason + "\n", { flag: "a" });
      } else {
        console.log(
          e.value.value.ids[0],
          "/",
          e.value.value.ids[1],
          "..",
          e.value.value.ids[e.value.value.ids.length - 1],
          "done in",
          e.value.time,
          "seconds",
        );
      }
    });
  });
}

let start = 2; // 1 = similarity graph, 2= birthrange graph, 3= individual ids
// remove the ids.json file if it exists for looping
if (fs.existsSync(path) && start != 3) {
  fs.rmSync(path, { force: true });
}

// resize workercount to the number of available cores
// resize batchsize to the a number that each worker takes a reasonable amount of time
const workercount: number = 12;
const batchsize: number = 1024;

// comment block for the main graph
if (start == 1)
  getbatches(credentials, workercount, batchsize, "student_similarity_graph")
    .then(() => createworker("./worker.js"))
    .catch((error) => {
      console.log(error);
      createworker();
    });

//comment block for the birthrange graph
if (start == 2)
  getbatches(
    credentials,
    workercount,
    batchsize,
    "student_similarity_graph_birthrange",
  )
    .then(() => createworker("./workerDateRange.js"))
    .catch((error) => {
      console.log(error);
      createworker("./workerDateRange.js");
    });

// run this if you want to setup specific ids in ids.json remember to disable the remove ids.json
// the number of created workers will be the number of arrays in the ids.json file -1 because
// zero index is an array with the highest possible id in the database
if (start == 3) createworker();
